<?php
namespace App\models;

use PDO;
use PDOException;

/**
 * Modèle de gestion des statistiques
 * Récupère et traite les données statistiques sur les entreprises, offres et candidatures
 */
class StatistiquesModel extends Model
{
    /**
     * Instance de connexion à la base de données
     * @var PDO
     */
    private PDO $db;

    /**
     * Constructeur du modèle
     * @param PDO $db Instance de connexion à la base de données
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Récupère l'ensemble des statistiques pour le tableau de bord
     * @return array Tableau associatif contenant toutes les statistiques
     */
    public function getDashboardStats()
    {
        try {
            return [
                'totals' => $this->getTotalCounts(),
                'users_by_role' => $this->getUsersByRole(),
                'top_entreprises' => $this->getTopRatedEntreprises(),
                'top_offres' => $this->getTopOffres(),
                'offres_by_domaine' => $this->getOffresByDomaine(),
                'entreprises_by_domaine' => $this->getEntreprisesByDomaine(),
                'entreprises_with_most_offres' => $this->getEntreprisesWithMostOffres(),
                'candidatures_by_month' => $this->getCandidaturesByMonth()
            ];
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des statistiques : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère les compteurs totaux (entreprises, offres, candidatures)
     * @return array Tableau associatif des compteurs
     */
    public function getTotalCounts()
    {
        try {
            $totals = [];

            $stmt = $this->db->query("SELECT COUNT(*) FROM entreprise");
            $totals['entreprises'] = $stmt->fetchColumn();

            $stmt = $this->db->query("SELECT COUNT(*) FROM offre");
            $totals['offres'] = $stmt->fetchColumn();

            $stmt = $this->db->query("SELECT COUNT(*) FROM postuler");
            $totals['candidatures'] = $stmt->fetchColumn();

            return $totals;
        } catch (PDOException $e) {
            error_log("Erreur SQL dans getTotalCounts: " . $e->getMessage());
            return [
                'entreprises' => 0,
                'offres' => 0,
                'candidatures' => 0
            ];
        }
    }

    /**
     * Récupère le nombre d'utilisateurs par rôle
     * @return array Tableau associatif avec le nombre d'utilisateurs par rôle
     */
    public function getUsersByRole()
    {
        try {
            $stmt = $this->db->query("
                SELECT 
                    CASE 
                        WHEN Id_Role = 1 THEN 'Administrateurs'
                        WHEN Id_Role = 2 THEN 'Pilotes'
                        WHEN Id_Role = 3 THEN 'Étudiants'
                        ELSE 'Autres'
                    END as role,
                    COUNT(*) as total
                FROM Utilisateur
                GROUP BY Id_Role
                ORDER BY Id_Role
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur SQL dans getUsersByRole: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère les entreprises les mieux notées
     * @param int $limit Nombre d'entreprises à récupérer
     * @return array Liste des entreprises les mieux notées
     */
    public function getTopRatedEntreprises($limit = 5)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    e.Id_Entreprise,
                    e.Nom_Entreprise,
                    AVG(n.Note) as note_moyenne,
                    COUNT(n.Note) as nombre_notes
                FROM Entreprise e
                JOIN noter n ON e.Id_Entreprise = n.Id_Entreprise
                GROUP BY e.Id_Entreprise
                HAVING COUNT(n.Note) > 0
                ORDER BY note_moyenne DESC, nombre_notes DESC
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur SQL dans getTopRatedEntreprises: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère les offres les plus populaires (avec le plus de candidatures)
     * @param int $limit Nombre d'offres à récupérer
     * @return array Liste des offres les plus populaires
     */
    public function getTopOffres($limit = 5)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    o.Id_Offre,
                    o.Nom_Offre,
                    e.Nom_Entreprise,
                    COUNT(p.Id_Utilisateur) as nombre_candidatures
                FROM Offre o
                JOIN Entreprise e ON o.Id_Entreprise = e.Id_Entreprise
                LEFT JOIN postuler p ON o.Id_Offre = p.Id_Offre
                GROUP BY o.Id_Offre
                ORDER BY nombre_candidatures DESC
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur SQL dans getTopOffres: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère la répartition des offres par domaine
     * @return array Tableau associatif avec le nombre d'offres par domaine
     */
    public function getOffresByDomaine()
    {
        try {
            $stmt = $this->db->query("
                SELECT 
                    Domaine_Offre as domaine,
                    COUNT(*) as total
                FROM Offre
                GROUP BY Domaine_Offre
                ORDER BY total DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur SQL dans getOffresByDomaine: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère la répartition des entreprises par domaine
     * @return array Tableau associatif avec le nombre d'entreprises par domaine
     */
    public function getEntreprisesByDomaine()
    {
        try {
            $stmt = $this->db->query("
                SELECT 
                    Domaine_Entreprise as domaine,
                    COUNT(*) as total
                FROM Entreprise
                GROUP BY Domaine_Entreprise
                ORDER BY total DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur SQL dans getEntreprisesByDomaine: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère les entreprises avec le plus d'offres
     * @param int $limit Nombre d'entreprises à récupérer
     * @return array Liste des entreprises avec le plus d'offres
     */
    public function getEntreprisesWithMostOffres($limit = 5)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    e.Id_Entreprise,
                    e.Nom_Entreprise,
                    COUNT(o.Id_Offre) as nombre_offres
                FROM Entreprise e
                LEFT JOIN Offre o ON e.Id_Entreprise = o.Id_Entreprise
                GROUP BY e.Id_Entreprise
                ORDER BY nombre_offres DESC
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur SQL dans getEntreprisesWithMostOffres: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère les statistiques mensuelles des candidatures
     * @param int $months Nombre de mois à récupérer
     * @return array Statistiques mensuelles
     */
    public function getCandidaturesByMonth($months = 6)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    DATE_FORMAT(Date_Candid, '%Y-%m') as mois,
                    COUNT(*) as total
                FROM postuler
                WHERE Date_Candid >= DATE_SUB(CURRENT_DATE(), INTERVAL :months MONTH)
                GROUP BY DATE_FORMAT(Date_Candid, '%Y-%m')
                ORDER BY mois
            ");
            $stmt->bindValue(':months', $months, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur SQL dans getCandidaturesByMonth: " . $e->getMessage());
            return [];
        }
    }
}
