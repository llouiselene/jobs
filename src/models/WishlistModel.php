<?php
namespace App\models;

use PDO;

/**
 * Modèle de gestion des wishlists
 * Gère les opérations liées aux wishlists des étudiants
 */
class WishlistModel extends Model {
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
     * Récupère la wishlist d'un utilisateur avec pagination
     *
     * @param int $user_id ID de l'utilisateur
     * @param int $page Numéro de page
     * @param int $parPage Nombre d'éléments par page
     * @return array Liste des offres dans la wishlist
     */
    public function getWishlist($user_id, $page = 1, $parPage = 10) {
        try {
            $offset = ($page - 1) * $parPage;

            $sql = "SELECT 
                o.Id_Offre, 
                o.Nom_Offre, 
                o.Domaine_Offre, 
                o.Description_Offre,
                e.Nom_Entreprise
            FROM wishlist w
            JOIN offre o ON w.Id_Offre = o.Id_Offre
            JOIN entreprise e ON o.Id_Entreprise = e.Id_Entreprise
            WHERE w.Id_Utilisateur = ?
            LIMIT ? OFFSET ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$user_id, $parPage, $offset]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération de la wishlist : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Compte le nombre total d'offres dans la wishlist d'un utilisateur
     *
     * @param int $user_id ID de l'utilisateur
     * @return int Nombre total d'offres dans la wishlist
     */
    public function countWishlist($user_id) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM wishlist WHERE Id_Utilisateur = ?");
            $stmt->execute([$user_id]);
            return $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("Erreur lors du comptage de la wishlist : " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Vérifie si une offre est dans la wishlist d'un utilisateur
     *
     * @param int $user_id ID de l'utilisateur
     * @param int $id_offre ID de l'offre
     * @return bool True si l'offre est dans la wishlist, false sinon
     */
    public function isInWishlist($user_id, $id_offre) {
        try {
            $stmt = $this->db->prepare("SELECT 1 FROM wishlist WHERE Id_Utilisateur = ? AND Id_Offre = ?");
            $stmt->execute([$user_id, $id_offre]);
            return (bool)$stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("Erreur lors de la vérification de la wishlist : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Ajoute une offre à la wishlist d'un utilisateur
     * Utilise INSERT IGNORE pour éviter les doublons
     *
     * @param int $user_id ID de l'utilisateur
     * @param int $id_offre ID de l'offre à ajouter
     * @return bool Succès de l'opération
     */
    public function ajouterAWishlist($user_id, $id_offre) {
        try {
            $stmt = $this->db->prepare("INSERT IGNORE INTO wishlist (Id_Utilisateur, Id_Offre) VALUES (?, ?)");
            return $stmt->execute([$user_id, $id_offre]);
        } catch (\PDOException $e) {
            error_log("Erreur lors de l'ajout à la wishlist : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprime une offre de la wishlist d'un utilisateur
     *
     * @param int $user_id ID de l'utilisateur
     * @param int $id_offre ID de l'offre à supprimer
     * @return bool Succès de l'opération
     */
    public function supprimerDeWishlist($user_id, $id_offre) {
        try {
            $stmt = $this->db->prepare("DELETE FROM wishlist WHERE Id_Utilisateur = ? AND Id_Offre = ?");
            return $stmt->execute([$user_id, $id_offre]);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la suppression de la wishlist : " . $e->getMessage());
            return false;
        }
    }
}
