<?php
namespace App\controllers;
use RuntimeException;
use InvalidArgumentException;
use App\models\EntrepriseModel;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../../twig.php';
require_once __DIR__ . '/../../index.php';

/**
 * Contrôleur pour la gestion des entreprises
 * Gère l'affichage et les actions liées aux entreprises (liste, détails, création, modification, suppression)
 */
class EntrepriseController extends Controller
{
    /**
     * Constructeur du contrôleur
     * Initialise le modèle et le moteur de template
     *
     * @param PDO $db Instance de connexion à la base de données
     */
    public function __construct($db)
    {
        $this->model = new EntrepriseModel($db);
        $this->templateEngine = new Environment(new FilesystemLoader(__DIR__ . '/../views'));
    }


    /**
     * Affiche les détails d'une entreprise spécifique
     *
     * @param int $id_entreprise ID de l'entreprise à afficher
     * @param int $role_id ID du rôle de l'utilisateur connecté
     * @return void
     */
    /**
     * Affiche les détails d'une entreprise spécifique
     *
     * @param int $id_entreprise ID de l'entreprise à afficher
     * @param int $role_id ID du rôle de l'utilisateur connecté
     * @return void
     */
    public function afficherEntreprise($id_entreprise, $role_id) : void
    {
        // Récupération des détails de l'entreprise
        $entreprise = $this->model->getEntrepriseById($id_entreprise);
        if (!$entreprise) {
            header('Location: ?uri=accueil');
            exit;
        }

        // Récupération de la note moyenne
        $averageRating = $this->model->getAverageRating($id_entreprise);

        // Récupération de la note de l'utilisateur connecté (si admin ou pilote)
        $userRating = null;
        if (($role_id == 1 || $role_id == 2) && isset($_SESSION['user_id'])) {
            $userRating = $this->model->getUserRating($_SESSION['user_id'], $id_entreprise);
            $userRating = $userRating ? $userRating['Note'] : null;
        }

        // Affichage via Twig
        echo $this->templateEngine->render('detail_entreprise.twig', [
            'entreprise' => $entreprise,
            'role_id' => $role_id,
            'menu_items' => menu($role_id),
            'average_rating' => $averageRating,
            'user_rating' => $userRating
        ]);
    }


    /**
     * Affiche une liste paginée d'entreprises avec recherche
     *
     * @param int $role_id ID du rôle de l'utilisateur connecté
     * @return void
     */
    public function afficher5Entreprise($role_id) : void
    {
        $searchTerm = $_GET['searchTerm'] ?? '';
        $page = (int)($_GET['page'] ?? 1);
        $parPage = 9;

        // Calcul de la pagination
        $total = $this->model->countSearchResults($searchTerm);
        $totalPages = max(1, ceil($total / $parPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $parPage;

        // Récupération des résultats paginés
        $entreprises = $this->model->searchOffres($searchTerm, $parPage, $offset);

        echo $this->templateEngine->render('entreprise.twig', [
            'entreprises' => $entreprises,
            'page' => $page,
            'totalPages' => $totalPages,
            'searchTerm' => $searchTerm, // Seul paramètre nécessaire
            'role_id' => $role_id,
            'menu_items' => menu($role_id)
        ]);
    }

    /**
     * Traite l'ajout d'une nouvelle entreprise
     *
     * @return void
     */
    public function ajouterEntreprise()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];
            $nom_entreprise = $_POST['nom_entreprise'] ?? '';
            $site_web = $_POST['site_web'] ?? '';
            $date_creation = $_POST['date_creation'] ?? '';
            $domaine_entreprise = $_POST['domaine_activite'] ?? '';
            $nombre_employes = $_POST['nombre_employes'] ?? '';
            $id_entreprise = 210;
            $coordonnees_entreprise = $_POST['coordonnees'] ?? '';
            $description_entreprise = $_POST['description_entreprise'] ?? '';
            $contact = $_POST['contact_entreprise'] ?? '';
            $nombre_stagiaires = $_POST['nombre_stagiaires'] ?? '';
            if (empty($errors)) {
                try {
                    if ($this->model->addEntreprise($id_entreprise,$nom_entreprise, $site_web, $date_creation, $domaine_entreprise, $nombre_employes, $coordonnees_entreprise, $description_entreprise, $contact, $nombre_stagiaires)) {
                        header('Location: ?uri=liste_entreprises');
                        exit;
                    }
                } catch (InvalidArgumentException $e) {
                    $errors['form'] = $e->getMessage();
                } catch (RuntimeException $e) {
                    $errors['form'] = $e->getMessage();
                }
            }

        }
        echo $this->templateEngine->render('creation_entreprise.twig', [
            'role' => $_SESSION['role'] ?? 'etudiant',
            'menu_items' => menu(strtolower($_SESSION['role'] ?? 'etudiant')),
            'errors' => $errors ?? [],
            'old_input' => $_POST
        ]);
    }

    /**
     * Modifie les informations d'une entreprise existante
     * Vérifie les permissions (admin ou pilote uniquement)
     *
     * @param int $id_entreprise ID de l'entreprise à modifier
     * @param string $role Rôle de l'utilisateur connecté
     * @return void
     */
    /**
     * Modifie les informations d'une entreprise existante
     * Vérifie les permissions (admin ou pilote uniquement)
     *
     * @param int $id_entreprise ID de l'entreprise à modifier
     * @param int $role_id ID du rôle de l'utilisateur connecté
     * @return void
     */
    public function modifierEntreprise($id_entreprise, $role_id) : void {
        // Vérifier les permissions (admin ou pilote uniquement)
        if ($role_id != 1 && $role_id != 2) {
            header('Location: ?uri=connexion');
            exit;
        }

        // Récupérer l'entreprise existante pour conserver les champs non modifiés
        $entreprise = $this->model->getEntrepriseById($id_entreprise);
        if (!$entreprise) {
            header('Location: ?uri=entreprise');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $nom_entreprise = $entreprise['Nom_Entreprise']; // On garde le nom inchangé
            $site_web = $_POST['Site_Web'] ?? '';
            $date_creation = $_POST['Date_Creation'] ?? '';
            $domaine_entreprise = $_POST['Domaine_Entreprise'] ?? '';
            $nombre_employes = $_POST['Nombre_Employes'] ?? '';
            $coordonnees_entreprise = $_POST['Coordonnees_Entreprise'] ?? '';
            $description_entreprise = $entreprise['Description_Entreprise']; // On garde la description inchangée
            $contact = $_POST['Contact'] ?? '';
            $nombre_stagiaires = $_POST['Nombre_Stagiaires'] ?? '';

            // Mettre à jour l'entreprise
            $success = $this->model->updateEntreprise(
                $id_entreprise,
                $nom_entreprise,
                $site_web,
                $date_creation,
                $domaine_entreprise,
                $nombre_employes,
                $coordonnees_entreprise,
                $description_entreprise,
                $contact,
                $nombre_stagiaires
            );

            // Rediriger vers la page de détail avec un message de succès
            header('Location: ?uri=detail_entreprise&id=' . $id_entreprise . '&success=1');
            exit;
        }

        // Si ce n'est pas une soumission POST, rediriger vers la page de détail
        header('Location: ?uri=detail_entreprise&id=' . $id_entreprise);
        exit;
    }

    /**
     * Supprime une entreprise (admin uniquement)
     *
     * @param int $id_entreprise ID de l'entreprise à supprimer
     * @param int $role_id ID du rôle de l'utilisateur connecté
     * @return void
     */
    public function supprimerEntreprise($id_entreprise, $role_id)
    {
        if ($role_id != 1) {
            header('Location: ?uri=connexion');
            exit;
        }

        try {
            $this->model->deleteEntreprise($id_entreprise);
            header('Location: ?uri=entreprise&success=suppr');
            exit;
        } catch (\Exception $e) {
            // Afficher une erreur ou rediriger avec un message d'erreur
            header('Location: ?uri=entreprise&error=suppr');
            exit;
        }
    }

    /**
     * Méthode alternative de suppression d'entreprise
     * Vérifie les permissions (admin uniquement)
     *
     * @return void
     */
    public function supprimer()
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?uri=connexion");
            exit;
        }

        $id = $_GET['id'] ?? null;

        if ($id) {
            $this->model->deleteEntreprise($id);
        }

        header("Location: ?uri=recherche_entreprise");
        exit;
    }

    /**
     * Récupère et affiche le nom d'une entreprise
     *
     * @param int $id_entreprise ID de l'entreprise
     * @return void
     */
    public function recuperernomEntreprise($id_entreprise) : void {
        $name_entreprise = $this->model->getEntrepriseName($id_entreprise);
        echo $this->templateEngine->render('entreprise.twig', ['nom_entreprise' => $name_entreprise]);
    }

    /**
     * Affiche le formulaire de création d'entreprise et traite sa soumission
     *
     * @param int $role_id ID du rôle de l'utilisateur connecté
     * @return void
     */
    public function afficherFormulaireCreation($role_id)
    {
        $errors = [];
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupération des données du formulaire
            $data = [
                'nom_entreprise'    => $_POST['nom_entreprise'] ?? '',
                'site_web'          => $_POST['site_web'] ?? '',
                'coordonnees'       => $_POST['coordonnees'] ?? '',
                'domaine_activite'  => $_POST['domaine_activite'] ?? '',
                'nombre_employes'   => $_POST['nombre_employes'] ?? 0,
                'nombre_stagiaires' => $_POST['nombre_stagiaires'] ?? 0,
                'date_creation'     => $_POST['date_creation'] ?? '',
                'description'       => $_POST['description_entreprise'] ?? '',
                'contact'           => $_POST['contact_entreprise'] ?? ''
            ];

            // Génère un nouvel ID unique
            $id_entreprise = $this->model->getNextId(); // À ajouter dans le modèle (voir plus bas)

            // Validation côté contrôleur (optionnel, tu peux en ajouter ici)
            if (empty($data['nom_entreprise'])) $errors['nom_entreprise'] = "Champ obligatoire";

            if (empty($errors)) {
                try {
                    $this->model->addEntreprise(
                        $id_entreprise,
                        $data['nom_entreprise'],
                        $data['site_web'],
                        $data['date_creation'],
                        $data['domaine_activite'],
                        $data['nombre_employes'],
                        $data['coordonnees'],
                        $data['description'],
                        $data['contact'],
                        $data['nombre_stagiaires']
                    );
                    header('Location: ?uri=entreprise&success=1');
                    exit;
                } catch (\InvalidArgumentException $e) {
                    $errors['form'] = $e->getMessage();
                } catch (\RuntimeException $e) {
                    $errors['form'] = "Erreur technique : " . $e->getMessage();
                }
            }
        }

        // Affichage du formulaire (GET ou POST avec erreurs)
        echo $this->templateEngine->render('creation_entreprise.twig', [
            'errors'    => $errors,
            'form_data' => $data,
            'role_id'   => $role_id,
            'menu_items'=> menu($role_id)
        ]);
    }

    /**
     * Traite la soumission d'une note pour une entreprise
     * Réservé aux administrateurs et pilotes
     *
     * @return void
     */
    public function noterEntreprise() {
        // Vérifier les permissions (admin ou pilote uniquement)
        if (!isset($_SESSION['role_id']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2)) {
            header('Location: ?uri=connexion');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?uri=entreprise');
            exit;
        }

        $id_entreprise = $_POST['id_entreprise'] ?? null;
        $note = (int)($_POST['note'] ?? 0);
        $id_utilisateur = $_SESSION['user_id'] ?? null;

        // Validation de base
        if (!$id_entreprise || !$id_utilisateur || $note < 1 || $note > 5) {
            header('Location: ?uri=detail_entreprise&id=' . $id_entreprise . '&error=invalid_rating');
            exit;
        }

        // Enregistrer la note
        $success = $this->model->rateEntreprise($id_utilisateur, $id_entreprise, $note);

        if ($success) {
            header('Location: ?uri=detail_entreprise&id=' . $id_entreprise . '&success=rating_saved');
        } else {
            header('Location: ?uri=detail_entreprise&id=' . $id_entreprise . '&error=rating_failed');
        }
        exit;
    }

}
