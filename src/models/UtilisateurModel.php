<?php

namespace App\models;

use PDO;

/**
 * Modèle de gestion des utilisateurs
 * Gère les opérations CRUD et les requêtes spécifiques liées aux utilisateurs
 */
class UtilisateurModel extends Model
{
    /**
     * Instance de connexion à la base de données
     * @var PDO
     */
    private pdo $db;

    /**
     * Constructeur du modèle
     * @param PDO $db Instance de connexion à la base de données
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Récupère l'instance de connexion à la base de données
     * @return PDO Instance de connexion
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * Récupère tous les utilisateurs de la base de données
     * @return array Liste de tous les utilisateurs
     */
    public function getAllUtilisateurs()
    {
        try {
            $stmt = $this->db->query("SELECT * FROM utilisateur");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur SQL : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère un utilisateur par son ID
     * @param int $id_utilisateur ID de l'utilisateur à récupérer
     * @return array|false Données de l'utilisateur ou false si non trouvé
     */
    public function getUtilisateurById($id_utilisateur)
    {
        $stmt = $this->db->prepare("SELECT * FROM Utilisateur WHERE Id_Utilisateur = ?");
        $stmt->execute([$id_utilisateur]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Ajoute un nouvel utilisateur dans la base de données
     * @param string $nom_utilisateur Nom de l'utilisateur
     * @param string $prenom_utilisateur Prénom de l'utilisateur
     * @param string $email Adresse email
     * @param string $mot_de_passe Mot de passe (non hashé)
     * @param string $genre Genre de l'utilisateur
     * @param string $Telephone Numéro de téléphone
     * @param string $Date_Naissance Date de naissance (format Y-m-d)
     * @param int $id_role ID du rôle (1=admin, 2=pilote, 3=étudiant)
     * @return bool Succès de l'opération
     */
    public function addUtilisateur($nom_utilisateur, $prenom_utilisateur, $email, $mot_de_passe, $genre, $Telephone, $Date_Naissance, $id_role)
    {
        $stmt = $this->db->prepare("INSERT INTO Utilisateur (Nom_Utilisateur, Prenom, Email, Mot_de_passe, Genre, Telephone, Date_de_Naissance, Id_Role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$nom_utilisateur, $prenom_utilisateur, $email, $mot_de_passe, $genre, $Telephone, $Date_Naissance, $id_role]);
    }

    /**
     * Met à jour le profil d'un utilisateur
     * @param int $id_utilisateur ID de l'utilisateur à mettre à jour
     * @param array $data Tableau associatif des données à mettre à jour
     * @return bool Succès de l'opération
     */
    public function updateProfil($id_utilisateur, $data)
    {
        $sql = "UPDATE Utilisateur SET Nom_Utilisateur = ?, Prenom = ?, Email = ?, Genre = ?, Telephone = ?, Date_de_Naissance = ?";
        $params = [
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['genre'],
            $data['telephone'],
            $data['date_naissance'],
        ];
        // Si un nouveau mot de passe est fourni
        if (!empty($data['mot_de_passe'])) {
            $sql .= ", Mot_de_passe = ?";
            $params[] = password_hash($data['mot_de_passe'], PASSWORD_BCRYPT);
        }
        $sql .= " WHERE Id_Utilisateur = ?";
        $params[] = $id_utilisateur;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Supprime un utilisateur et ses données associées
     * @param int $id_utilisateur ID de l'utilisateur à supprimer
     * @return bool Succès de l'opération
     */
    public function deleteUtilisateur($id_utilisateur)
    {
        // Supprimer d'abord les notes liées
        $this->db->prepare("DELETE FROM noter WHERE Id_Utilisateur = ?")
            ->execute([$id_utilisateur]);

        // Puis supprimer l'utilisateur
        $stmt = $this->db->prepare("DELETE FROM Utilisateur WHERE Id_Utilisateur = ?");
        return $stmt->execute([$id_utilisateur]);
    }

    /**
     * Recherche des utilisateurs selon différents critères
     * @param string $nom Nom à rechercher (partiel)
     * @param string $prenom Prénom à rechercher (partiel)
     * @param string $email Email à rechercher (partiel)
     * @param int|null $roleFilter Filtre par rôle (optionnel)
     * @return array Liste des utilisateurs correspondant aux critères
     */
    public function rechercherUtilisateurs($nom, $prenom, $email, $roleFilter = null)
    {
        $sql = "SELECT * FROM utilisateur WHERE 1=1";
        $params = [];

        if (!empty($nom)) {
            $sql .= " AND Nom_Utilisateur LIKE :nom";
            $params[':nom'] = '%' . $nom . '%';
        }
        if (!empty($prenom)) {
            $sql .= " AND Prenom LIKE :prenom";
            $params[':prenom'] = '%' . $prenom . '%';
        }
        if (!empty($email)) {
            $sql .= " AND Email LIKE :email";
            $params[':email'] = '%' . $email . '%';
        }
        if ($roleFilter !== null) {
            $sql .= " AND Id_Role = :role";
            $params[':role'] = $roleFilter;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les statistiques d'un étudiant
     * @param int $id_etudiant ID de l'étudiant
     * @return array Tableau de statistiques
     */
    public function getStatistiquesEtudiant($id_etudiant) {
        // Statistiques de base
        $stats = [
            'nb_candidatures' => 0,
            'nb_candidatures_acceptees' => 0,
            'nb_candidatures_refusees' => 0,
            'nb_candidatures_en_attente' => 0,
            'entreprises_postulees' => [],
            'domaines_postules' => []
        ];

        try {
            // Nombre total de candidatures
            $stmt = $this->db->prepare("
            SELECT COUNT(*) as total
            FROM postuler
            WHERE Id_Utilisateur = ?
        ");
            $stmt->execute([$id_etudiant]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['nb_candidatures'] = $result['total'] ?? 0;

            // Entreprises postulées
            $stmt = $this->db->prepare("
            SELECT e.Nom_Entreprise, COUNT(*) as nb_candidatures
            FROM postuler p
            JOIN offre o ON p.Id_Offre = o.Id_Offre
            JOIN entreprise e ON o.Id_Entreprise = e.Id_Entreprise
            WHERE p.Id_Utilisateur = ?
            GROUP BY e.Id_Entreprise
            ORDER BY nb_candidatures DESC
        ");
            $stmt->execute([$id_etudiant]);
            $stats['entreprises_postulees'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Domaines postulés
            $stmt = $this->db->prepare("
            SELECT o.Domaine_Offre, COUNT(*) as nb_candidatures
            FROM postuler p
            JOIN offre o ON p.Id_Offre = o.Id_Offre
            WHERE p.Id_Utilisateur = ?
            GROUP BY o.Domaine_Offre
            ORDER BY nb_candidatures DESC
        ");
            $stmt->execute([$id_etudiant]);
            $stats['domaines_postules'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            error_log("Erreur SQL dans getStatistiquesEtudiant: " . $e->getMessage());
        }

        return $stats;
    }
}
