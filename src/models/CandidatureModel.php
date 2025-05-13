<?php
namespace App\models;

use PDO;

/**
 * Modèle de gestion des candidatures
 * Gère les opérations liées aux candidatures des étudiants aux offres
 */
class CandidatureModel extends Model {
    /**
     * Instance de connexion à la base de données
     * @var PDO
     */
    private PDO $db;

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
     * Ajoute une nouvelle candidature
     *
     * @param int $id_offre ID de l'offre
     * @param int $id_utilisateur ID de l'utilisateur
     * @param string $lettre_motivation Lettre de motivation
     * @return bool Succès de l'opération
     */
    public function addCandidature($id_offre, $id_utilisateur, $lettre_motivation) {
        try {
            // Vérifier si l'utilisateur a déjà postulé à cette offre
            $checkStmt = $this->db->prepare(
                "SELECT 1 FROM postuler 
                WHERE Id_Utilisateur = :id_utilisateur 
                AND Id_Offre = :id_offre 
                LIMIT 1"
            );
            $checkStmt->execute([
                ':id_utilisateur' => $id_utilisateur,
                ':id_offre' => $id_offre
            ]);
            if ($checkStmt->fetchColumn()) {
                return false; // Déjà postulé
            }

            // Insérer la candidature
            $stmt = $this->db->prepare(
                "INSERT INTO postuler 
                (Id_Utilisateur, Id_Offre, Date_Candid, Lettre_Motivation) 
                VALUES (:id_utilisateur, :id_offre, NOW(), :lettre_motivation)"
            );

            return $stmt->execute([
                ':id_utilisateur' => $id_utilisateur,
                ':id_offre' => $id_offre,
                ':lettre_motivation' => $lettre_motivation
            ]);
        } catch (\PDOException $e) {
            error_log("Erreur lors de l'ajout de candidature : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère les candidatures d'un utilisateur
     *
     * @param int $id_utilisateur ID de l'utilisateur
     * @return array Liste des candidatures
     */
    public function getCandidaturesByUtilisateur($id_utilisateur) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.Id_Offre,
                    p.Date_Candid,
                    p.Lettre_Motivation,
                    o.Nom_Offre,
                    o.Domaine_Offre,
                    e.Nom_Entreprise
                FROM postuler p
                JOIN offre o ON p.Id_Offre = o.Id_Offre
                JOIN entreprise e ON o.Id_Entreprise = e.Id_Entreprise
                WHERE p.Id_Utilisateur = :id_utilisateur
                ORDER BY p.Date_Candid DESC
            ");
            $stmt->execute([':id_utilisateur' => $id_utilisateur]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des candidatures : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère les dernières candidatures (pour les admins et pilotes)
     *
     * @param int $limit Nombre maximum de candidatures à récupérer
     * @return array Liste des dernières candidatures
     */
    public function getDernieresCandidatures($limit = 10) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.Date_Candid,
                    u.Nom_Utilisateur AS etudiant_nom,
                    u.Prenom AS etudiant_prenom,
                    o.Nom_Offre AS offre_titre,
                    e.Nom_Entreprise
                FROM postuler p
                JOIN utilisateur u ON p.Id_Utilisateur = u.Id_Utilisateur
                JOIN offre o ON p.Id_Offre = o.Id_Offre
                JOIN entreprise e ON o.Id_Entreprise = e.Id_Entreprise
                ORDER BY p.Date_Candid DESC
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des dernières candidatures : " . $e->getMessage());
            return [];
        }
    }
}
