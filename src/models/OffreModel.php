<?php

namespace App\models;

use PDO;

/**
 * Modèle de gestion des offres d'emploi
 * Gère les opérations CRUD et les requêtes spécifiques liées aux offres
 */
class OffreModel extends Model {
    /**
     * Instance de connexion à la base de données
     * @var PDO
     */
    private pdo $db;

    /**
     * Constructeur du modèle
     * @param PDO $db Instance de connexion à la base de données
     */
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Récupère l'instance de connexion à la base de données
     * @return PDO Instance de connexion
     */
    public function getDb() {
        return $this->db;
    }

    /**
     * Récupère toutes les offres de la base de données
     * @return array Liste de toutes les offres
     */
    public function getAllOffres() {
        $stmt = $this->db->query("SELECT * FROM Offre");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les 3 dernières offres par date
     * @return array Liste des 3 dernières offres
     */
    public function getLastOffres() {
        $stmt = $this->db->query("SELECT * FROM Offre ORDER BY Date_Offre DESC LIMIT 3");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère une offre par son ID
     * @param int $id_offre ID de l'offre à récupérer
     * @return array|false Données de l'offre ou false si non trouvée
     */
    public function getOffreById($id_offre)
    {
        $stmt = $this->db->prepare("SELECT * FROM Offre WHERE Id_Offre = ?");
        $stmt->execute([$id_offre]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Ajoute une nouvelle offre dans la base de données
     *
     * @param string $nom_offre Nom de l'offre
     * @param string $description_offre Description détaillée
     * @param string $domaine_offre Domaine d'activité
     * @param string $profil_recherche Profil recherché
     * @param string $coordonnees Coordonnées/lieu du poste
     * @param string $remuneration Rémunération proposée
     * @param string $date_offre Date de publication (format Y-m-d)
     * @param int $nombre_etudiants Nombre d'étudiants ayant postulé
     * @param int $id_utilisateur ID de l'utilisateur créateur
     * @param int $id_entreprise ID de l'entreprise concernée
     * @return bool Succès de l'opération
     */
    public function addOffre($nom_offre, $description_offre, $domaine_offre, $profil_recherche, $coordonnees, $remuneration, $date_offre, $nombre_etudiants, $id_utilisateur, $id_entreprise) {
        $stmt = $this->db->prepare("INSERT INTO Offre (Nom_Offre, Description_Offre, Domaine_Offre, Profil_Recherche, Coordonnees_Offre, Remuneration, Date_Offre, Nombre_Etudiants, Id_Utilisateur, Id_Entreprise) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$nom_offre, $description_offre, $domaine_offre, $profil_recherche, $coordonnees, $remuneration, $date_offre, $nombre_etudiants, $id_utilisateur, $id_entreprise]);
    }


    /**
     * Supprime une offre de la base de données
     *
     * @param int $id_offre ID de l'offre à supprimer
     * @return bool Succès de l'opération
     */
    public function deleteOffre($id_offre)
    {
        $stmt = $this->db->prepare("DELETE FROM Offre WHERE Id_Offre = ?");
        return $stmt->execute([$id_offre]);
    }


    /**
     * Recherche des offres selon un terme de recherche avec pagination
     *
     * @param string $searchTerm Terme de recherche
     * @param int $limit Nombre maximum de résultats
     * @param int $offset Position de départ
     * @return array Liste des offres correspondant aux critères
     */
    public function searchOffres($searchTerm, $limit, $offset)
    {
        $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        $sql = "SELECT * FROM Offre 
            WHERE 
                Nom_Offre LIKE :search1 OR
                Domaine_Offre LIKE :search2 OR
                Description_Offre LIKE :search3 
            LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $searchParam = '%' . $searchTerm . '%';

        // Binding explicite pour chaque paramètre
        $stmt->bindValue(':search1', $searchParam, PDO::PARAM_STR);
        $stmt->bindValue(':search2', $searchParam, PDO::PARAM_STR);
        $stmt->bindValue(':search3', $searchParam, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Compte le nombre total de résultats pour une recherche
     * Utilisé pour la pagination
     *
     * @param string $searchTerm Terme de recherche
     * @return int Nombre total de résultats
     */
    public function countSearchResults($searchTerm)
    {
        $sql = "SELECT COUNT(*) as total FROM Offre 
            WHERE 
                Nom_Offre LIKE :search1 OR
                Domaine_Offre LIKE :search2 OR
                Description_Offre LIKE :search3";

        $stmt = $this->db->prepare($sql);

        // Binding explicite pour chaque paramètre
        $searchParam = '%' . $searchTerm . '%';
        $stmt->bindValue(':search1', $searchParam, PDO::PARAM_STR);
        $stmt->bindValue(':search2', $searchParam, PDO::PARAM_STR);
        $stmt->bindValue(':search3', $searchParam, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    /**
     * Récupère les dernières candidatures avec informations associées
     *
     * @param int $limit Nombre maximum de candidatures à récupérer
     * @return array Liste des dernières candidatures
     */

    /**
     * Récupère les dernières offres publiées
     *
     * @param int $limit Nombre maximum d'offres à récupérer
     * @return array Liste des dernières offres
     */
    public function getDernieresOffres($limit = 3) {
        $stmt = $this->db->prepare("
        SELECT 
            o.Id_Offre,
            o.Nom_Offre,
            o.Domaine_Offre,
            o.Date_Offre,
            e.Nom_Entreprise
        FROM offre o
        JOIN entreprise e ON o.Id_Entreprise = e.Id_Entreprise
        ORDER BY o.Date_Offre DESC
        LIMIT :limit
    ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Vérifie si une offre est dans la wishlist d'un utilisateur
     *
     * @param int $user_id ID de l'utilisateur
     * @param int $id_offre ID de l'offre
     * @return bool True si l'offre est dans la wishlist, false sinon
     */
    public function isInWishlist($user_id, $id_offre) {
        $stmt = $this->db->prepare("SELECT 1 FROM wishlist WHERE Id_Utilisateur = ? AND Id_Offre = ?");
        $stmt->execute([$user_id, $id_offre]);
        return (bool)$stmt->fetchColumn();
    }

    /**
     * Met à jour une offre existante
     *
     * @param string $nom_offre Nouveau nom
     * @param string $description_offre Nouvelle description
     * @param string $domaine_offre Nouveau domaine
     * @param string $profil_recherche Nouveau profil recherché
     * @param string $coordonnees Nouvelles coordonnées
     * @param string $remuneration Nouvelle rémunération
     * @param string $date_offre Date de l'offre (inchangée)
     * @param int $nombre_etudiants Nombre d'étudiants (inchangé)
     * @param int $id_utilisateur ID de l'utilisateur (inchangé)
     * @param int $id_entreprise ID de l'entreprise (inchangé)
     * @param int $id_offre ID de l'offre à modifier
     * @return bool Succès de l'opération
     */
    public function updateOffre($nom_offre, $description_offre, $domaine_offre, $profil_recherche, $coordonnees, $remuneration, $date_offre, $nombre_etudiants, $id_utilisateur, $id_entreprise, $id_offre) {
        try {
            $stmt = $this->db->prepare("
            UPDATE Offre 
            SET Nom_Offre = ?, 
                Description_Offre = ?, 
                Domaine_Offre = ?, 
                Profil_Recherche = ?, 
                Coordonnees_Offre = ?, 
                Remuneration = ?, 
                Date_Offre = ?, 
                Nombre_Etudiants = ?, 
                Id_Utilisateur = ?, 
                Id_Entreprise = ? 
            WHERE Id_Offre = ?
        ");

            return $stmt->execute([
                $nom_offre,
                $description_offre,
                $domaine_offre,
                $profil_recherche,
                $coordonnees,
                $remuneration,
                $date_offre,
                $nombre_etudiants,
                $id_utilisateur,
                $id_entreprise,
                $id_offre
            ]);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la mise à jour de l'offre : " . $e->getMessage());
            return false;
        }
    }

}
