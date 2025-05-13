<?php
namespace App\models;
use RuntimeException;
use InvalidArgumentException;
use PDO;
use PDOException;

/**
 * Modèle de gestion des entreprises
 * Gère les opérations CRUD et les requêtes spécifiques liées aux entreprises
 */
class EntrepriseModel extends Model{
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
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Récupère toutes les entreprises de la base de données
     * @return array Liste de toutes les entreprises
     */
    public function getAllEntreprises() {
        $stmt = $this->db->query("SELECT * FROM entreprise");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère une entreprise par son ID
     * @param int $id_entreprise ID de l'entreprise à récupérer
     * @return array|false Données de l'entreprise ou false si non trouvée
     */
    public function getEntrepriseById($id_entreprise) {
        $stmt = $this->db->prepare("SELECT * FROM entreprise WHERE Id_Entreprise = ?");
        $stmt->execute([$id_entreprise]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Ajoute une nouvelle entreprise dans la base de données
     * Effectue plusieurs validations avant l'insertion
     *
     * @param int $id_entreprise ID de l'entreprise
     * @param string $nom_entreprise Nom de l'entreprise
     * @param string $site_web URL du site web
     * @param string $date_creation Date de création (format Y-m-d)
     * @param string $domaine_entreprise Domaine d'activité
     * @param int $nombre_employes Nombre d'employés
     * @param string $coordonnees_entreprise Coordonnées (adresse)
     * @param string $description_entreprise Description
     * @param string $contact Informations de contact
     * @param int $nombre_stagiaires Nombre de stagiaires
     * @return bool Succès de l'opération
     * @throws InvalidArgumentException Si les données sont invalides
     * @throws RuntimeException Si une erreur technique survient
     */
    public function addEntreprise($id_entreprise,$nom_entreprise, $site_web, $date_creation, $domaine_entreprise, $nombre_employes, $coordonnees_entreprise, $description_entreprise, $contact, $nombre_stagiaires) {
        try {
            // Validation des données
            if (empty($nom_entreprise)) {
                throw new InvalidArgumentException("Le nom de l'entreprise est obligatoire.");
            }
            if (!filter_var($site_web, FILTER_VALIDATE_URL)) {
                throw new InvalidArgumentException("L'URL du site web est invalide.");
            }
            if (!is_numeric($nombre_employes) || $nombre_employes < 0) {
                throw new InvalidArgumentException("Le nombre d'employés doit être un nombre positif.");
            }
            if (!is_numeric($nombre_stagiaires) || $nombre_stagiaires < 0) {
                throw new InvalidArgumentException("Le nombre de stagiaires doit être un nombre positif.");
            }
            if (empty($date_creation)) {
                throw new InvalidArgumentException("La date de création est obligatoire.");
            }

            // Validation du format de date
            $dateObj = \DateTime::createFromFormat('Y-m-d', $date_creation);
            if (!$dateObj) {
                throw new InvalidArgumentException("Format de date invalide (YYYY-MM-DD requis).");
            }
            $date_creation = $dateObj->format('Y-m-d');

            // Vérification si l'entreprise existe déjà
            $checkStmt = $this->db->prepare(
                "SELECT 1 FROM entreprise 
            WHERE Id_Entreprise = :id_entreprise 
            LIMIT 1"
            );
            $checkStmt->execute([
                ':id_entreprise' => $id_entreprise,
            ]);
            if ($checkStmt->fetchColumn()) {
                throw new RuntimeException("Vous avez déjà postulé à cette offre");
            }

            // Préparation de la requête d'insertion
            $stmt = $this->db->prepare(
                "INSERT INTO entreprise 
                (Id_Entreprise, Nom_Entreprise, Site_Web, Date_Creation, Domaine_Entreprise, Nombre_Employes, 
                Coordonnees_Entreprise, Description_Entreprise, Contact, Nombre_Stagiaires) 
                VALUES (:id_entreprise,:nom_entreprise,:site_web,:date_creation,:domaine_entreprise,
                :nombre_employes,:coordonnees_entreprise,:description_entreprise,:contact,:nombre_stagiaires)"
            );

            echo("ici 1");

            // Binding des paramètres avec type spécifique
            $stmt->bindParam(':id_entreprise', $id_entreprise, PDO::PARAM_INT); // Entier
            $stmt->bindParam(':nom_entreprise', $nom_entreprise, PDO::PARAM_STR); // Chaîne
            $stmt->bindParam(':site_web', $site_web, PDO::PARAM_STR); // Chaîne (URL)
            $stmt->bindParam(':date_creation', $date_creation, PDO::PARAM_STR); // Chaîne (date)
            $stmt->bindParam(':domaine_entreprise', $domaine_entreprise, PDO::PARAM_STR); // Chaîne
            $stmt->bindParam(':nombre_employes', $nombre_employes, PDO::PARAM_INT); // Entier
            $stmt->bindParam(':coordonnees_entreprise', $coordonnees_entreprise, PDO::PARAM_STR); // Chaîne
            $stmt->bindParam(':description_entreprise', $description_entreprise, PDO::PARAM_STR); // Chaîne
            $stmt->bindParam(':contact', $contact, PDO::PARAM_STR); // Chaîne
            $stmt->bindParam(':nombre_stagiaires', $nombre_stagiaires, PDO::PARAM_INT);

            // Exécution de la requête avec gestion d'erreur
            try {
                echo("ici2");
                $stmt->execute();
                echo("ici3");
            } catch (PDOException $e) {
                echo("ici");
                echo($e->getMessage());
                error_log("Erreur d'exécution : " . $e->getMessage());
                throw new RuntimeException("Échec technique lors de l'exécution de la requête");
            }

            echo("test");
            return true;
        } catch (PDOException $e) {
            echo("ici 0");
            echo("Erreur SQL dans addEntreprise : " . $e->getMessage());
            throw new RuntimeException("Erreur technique lors de l'ajout de l'entreprise.");
        } catch (InvalidArgumentException $e) {
            echo("ici 0");
            echo("Erreur métier dans addEntreprise : " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Met à jour les informations d'une entreprise existante
     *
     * @param int $id_entreprise ID de l'entreprise à modifier
     * @param string $nom_entreprise Nouveau nom
     * @param string $site_web Nouveau site web
     * @param string $date_creation Nouvelle date de création
     * @param string $domaine_entreprise Nouveau domaine
     * @param int $nombre_employes Nouveau nombre d'employés
     * @param string $coordonnees_entreprise Nouvelles coordonnées
     * @param string $description_entreprise Nouvelle description
     * @param string $contact Nouveau contact
     * @param int $nombre_stagiaires Nouveau nombre de stagiaires
     * @return bool Succès de l'opération
     */
    public function updateEntreprise($id_entreprise, $nom_entreprise, $site_web, $date_creation, $domaine_entreprise, $nombre_employes, $coordonnees_entreprise, $description_entreprise, $contact, $nombre_stagiaires) {
        $stmt = $this->db->prepare("UPDATE entreprise SET Nom_Entreprise = ?, Site_Web = ?, Date_Creation = ?, Domaine_Entreprise = ?, Nombre_Employes = ?, Coordonnees_Entreprise = ?, Description_Entreprise = ?, Contact = ?, Nombre_Stagiaires = ? WHERE Id_Entreprise = ?");
        return $stmt->execute([$nom_entreprise, $site_web, $date_creation, $domaine_entreprise, $nombre_employes, $coordonnees_entreprise, $description_entreprise, $contact, $nombre_stagiaires, $id_entreprise]);
    }

    /**
     * Supprime une entreprise de la base de données
     *
     * @param int $id_entreprise ID de l'entreprise à supprimer
     * @return bool Succès de l'opération
     */
    public function deleteEntreprise($id_entreprise) {
        $stmt = $this->db->prepare("DELETE FROM entreprise WHERE Id_Entreprise = ?");
        return $stmt->execute([$id_entreprise]);
    }

    /**
     * Récupère le nom d'une entreprise par son ID
     *
     * @param int $id_entreprise ID de l'entreprise
     * @return array Nom de l'entreprise
     */
    public function getEntrepriseName($id_entreprise) {
        $stmt = $this->db->query("SELECT Nom_Entreprise FROM entreprise WHERE Id_Entreprise = ?");
        $stmt->execute([$id_entreprise]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recherche des entreprises selon un terme de recherche avec pagination
     *
     * @param string $searchTerm Terme de recherche
     * @param int $limit Nombre maximum de résultats
     * @param int $offset Position de départ
     * @return array Liste des entreprises correspondant aux critères
     */
    public function searchOffres($searchTerm, $limit, $offset)
    {
        $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        $sql = "SELECT * FROM entreprise 
            WHERE 
                Nom_Entreprise LIKE :search1 OR
                Domaine_Entreprise LIKE :search2 OR
                Contact LIKE :search3 
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
        $sql = "SELECT COUNT(*) as total FROM entreprise 
            WHERE 
                Nom_Entreprise LIKE :search1 OR
                Domaine_Entreprise LIKE :search2 OR
                Contact LIKE :search3";

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
     * Génère un nouvel ID d'entreprise unique
     *
     * @return int Prochain ID disponible
     */
    public function getNextId() {
        $stmt = $this->db->query("SELECT MAX(Id_Entreprise) FROM entreprise");
        $maxId = $stmt->fetchColumn();
        return $maxId ? $maxId + 1 : 1;
    }

    /**
     * Vérifie si un utilisateur a déjà noté une entreprise
     *
     * @param int $id_utilisateur ID de l'utilisateur
     * @param int $id_entreprise ID de l'entreprise
     * @return array|false La note existante ou false si aucune note
     */
    public function getUserRating($id_utilisateur, $id_entreprise) {
        $stmt = $this->db->prepare("SELECT Note FROM noter WHERE Id_Utilisateur = ? AND Id_Entreprise = ?");
        $stmt->execute([$id_utilisateur, $id_entreprise]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Ajoute ou met à jour la note d'un utilisateur pour une entreprise
     *
     * @param int $id_utilisateur ID de l'utilisateur
     * @param int $id_entreprise ID de l'entreprise
     * @param int $note Note attribuée (1-5)
     * @return bool Succès de l'opération
     */
    public function rateEntreprise($id_utilisateur, $id_entreprise, $note) {
        // Vérifier si l'utilisateur a déjà noté cette entreprise
        $existingRating = $this->getUserRating($id_utilisateur, $id_entreprise);

        try {
            if ($existingRating) {
                // Mettre à jour la note existante
                $stmt = $this->db->prepare("UPDATE noter SET Note = ? WHERE Id_Utilisateur = ? AND Id_Entreprise = ?");
                return $stmt->execute([$note, $id_utilisateur, $id_entreprise]);
            } else {
                // Ajouter une nouvelle note
                $stmt = $this->db->prepare("INSERT INTO noter (Id_Utilisateur, Id_Entreprise, Note) VALUES (?, ?, ?)");
                return $stmt->execute([$id_utilisateur, $id_entreprise, $note]);
            }
        } catch (\PDOException $e) {
            error_log("Erreur lors de la notation : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Calcule la note moyenne d'une entreprise
     *
     * @param int $id_entreprise ID de l'entreprise
     * @return float|null Note moyenne ou null si aucune note
     */
    public function getAverageRating($id_entreprise) {
        $stmt = $this->db->prepare("SELECT AVG(Note) as average FROM noter WHERE Id_Entreprise = ?");
        $stmt->execute([$id_entreprise]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['average'] ? round($result['average'], 1) : null;
    }

}
