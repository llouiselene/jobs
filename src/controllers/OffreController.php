<?php

namespace App\controllers;

use App\models\OffreModel;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Contrôleur pour la gestion des offres d'emploi
 * Gère l'affichage et les actions liées aux offres (liste, détails, création, modification, suppression, candidature)
 */
class OffreController extends Controller
{
    /**
     * Modèle pour les opérations sur les offres
     * @var OffreModel
     */
    private $offreModel;

    /**
     * Moteur de template Twig
     * @var Environment
     */
    private $twig;

    /**
     * Constructeur du contrôleur
     *
     * @param PDO $db Instance de connexion à la base de données
     * @param Environment $twig Instance du moteur de template Twig
     */
    public function __construct($db, $twig)
    {
        $this->offreModel = new OffreModel($db);
        $this->twig = $twig;
    }

    /**
     * Affiche les dernières offres sur la page d'accueil
     * Pour les étudiants, vérifie également si les offres sont dans leur wishlist
     *
     * @param int $role_id ID du rôle de l'utilisateur connecté
     * @return void
     */
    public function LastOffres($role_id) {
        $offres = $this->offreModel->getDernieresOffres(3);

        // Si l'utilisateur est un étudiant, vérifie les offres dans sa wishlist
        if ($role_id == 3 && isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            foreach ($offres as &$offre) {
                $offre['in_wishlist'] = $this->offreModel->isInWishlist($user_id, $offre['Id_Offre']);
            }
            unset($offre);
        }

        echo $this->twig->render('accueil.twig', [
            'offres' => $offres,
            'role_id' => $role_id,
            'menu_items' => menu($role_id)
        ]);
    }

    /**
     * Affiche les détails d'une offre spécifique
     *
     * @param int $id_offre ID de l'offre à afficher
     * @param int $role_id ID du rôle de l'utilisateur connecté
     * @return void
     */
    public function afficherOffre($id_offre, $role_id) : void
    {
        $offre = $this->offreModel->getOffreById($id_offre);
        echo $this->twig->render('detail_offre.twig', [
            'role_id' => $role_id,
            'offre' => $offre,
            'menu_items' => menu($role_id)
        ]);
    }

    /**
     * Affiche une liste paginée d'offres avec recherche
     * Pour les étudiants, vérifie également si les offres sont dans leur wishlist
     *
     * @param int $role_id ID du rôle de l'utilisateur connecté
     * @return void
     */
    public function afficher5Offre($role_id) {
        $searchTerm = $_GET['search'] ?? '';
        $page = (int)($_GET['page'] ?? 1);
        $parPage = 5;

        // Calcul de la pagination
        $total = $this->offreModel->countSearchResults($searchTerm);
        $totalPages = max(1, ceil($total / $parPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $parPage;

        $offres = $this->offreModel->searchOffres($searchTerm, $parPage, $offset);

        // Si l'utilisateur est un étudiant, vérifie les offres dans sa wishlist
        if ($role_id == 3 && isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            foreach ($offres as &$offre) {
                $offre['in_wishlist'] = $this->offreModel->isInWishlist($user_id, $offre['Id_Offre']);
            }
            unset($offre);
        }

        echo $this->twig->render('liste-offres.twig', [
            'offres' => $offres,
            'page' => $page,
            'totalPages' => $totalPages,
            'searchTerm' => $searchTerm,
            'role_id' => $role_id,
            'menu_items' => menu($role_id)
        ]);
    }

    /**
     * Affiche le formulaire de création d'offre et traite sa soumission
     * Réservé aux administrateurs
     *
     * @param int $role_id ID du rôle de l'utilisateur connecté
     * @return void
     */
    public function creerOffre($role_id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupération des données du formulaire
            $data = [
                'Nom_Offre' => $_POST['entreprise'] ?? '',
                'Domaine_Offre' => $_POST['departement'] ?? '',
                'Description_Offre' => $_POST['technologie'] ?? '',
                'Profil_Recherche' => $_POST['profil_recherche'] ?? '',
                'Coordonnees_Offre' => $_POST['ville'] . ', ' . $_POST['pays'],
                'Remuneration' => 'À négocier', // Ajouter un champ si nécessaire
            ];

            if ($this->offreModel->addOffre(
                $data['Nom_Offre'],
                $data['Description_Offre'],
                $data['Domaine_Offre'],
                $data['Profil_Recherche'],
                $data['Coordonnees_Offre'],
                $data['Remuneration'],
                date('Y-m-d'), // Date_Offre
                0, // Nombre_Etudiants initial
                $_SESSION['user_id'], // Id_Utilisateur de l'admin
                1 // Id_Entreprise (à adapter selon votre logique)
            )) {
                header('Location: ?uri=liste-offres&success=1');
                exit;
            } else {
                echo "Erreur lors de la création de l'offre";
            }
        } else {
            echo $this->twig->render('creation_offre.twig', [
                'role_id' => $role_id,
                'menu_items' => menu($role_id)
            ]);
        }
    }

    /**
     * Supprime une offre (admin uniquement)
     *
     * @return void
     */
    public function supprimer()
    {
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
            header("Location: ?uri=connexion");
            exit;
        }

        $id_offre = $_GET['id'] ?? null;
        if ($id_offre) {
            $this->offreModel->deleteOffre($id_offre);
        }
        header("Location: ?uri=accueil");
        exit;
    }

    /**
     * Modifie les informations d'une offre existante
     * Vérifie les permissions (admin ou pilote uniquement)
     *
     * @param int $id_offre ID de l'offre à modifier
     * @param int $role_id ID du rôle de l'utilisateur connecté
     * @return void
     */
    public function modifierOffre($id_offre, $role_id) : void {
        // Vérifier les permissions (admin ou pilote uniquement)
        if ($role_id != 1 && $role_id != 2) {
            header('Location: ?uri=connexion');
            exit;
        }

        // Récupérer l'offre existante pour conserver les champs non modifiés
        $offre = $this->offreModel->getOffreById($id_offre);
        if (!$offre) {
            header('Location: ?uri=offre');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $nom_offre = $_POST['Nom_Offre'] ?? '';
            $description_offre = $_POST['Description_Offre'] ?? '';
            $domaine_offre = $_POST['Domaine_Offre'] ?? '';
            $profil_recherche = $_POST['Profil_Recherche'] ?? '';
            $coordonnees_offre = $_POST['Coordonnees_Offre'] ?? '';
            $remuneration = $_POST['Remuneration'] ?? '';

            // Conserver les valeurs qui ne sont pas modifiées par le formulaire
            $date_offre = $offre['Date_Offre'];
            $nombre_etudiants = $offre['Nombre_Etudiants'];
            $id_utilisateur = $offre['Id_Utilisateur'];
            $id_entreprise = $offre['Id_Entreprise'];

            // Mettre à jour l'offre
            $success = $this->offreModel->updateOffre(
                $nom_offre,
                $description_offre,
                $domaine_offre,
                $profil_recherche,
                $coordonnees_offre,
                $remuneration,
                $date_offre,
                $nombre_etudiants,
                $id_utilisateur,
                $id_entreprise,
                $id_offre
            );

            // Rediriger vers la page de détail avec un message de succès
            if ($success) {
                header('Location: ?uri=detail_offre&id=' . $id_offre . '&success=1');
            } else {
                header('Location: ?uri=detail_offre&id=' . $id_offre . '&error=1');
            }
            exit;
        }

        // Si ce n'est pas une soumission POST, rediriger vers la page de détail
        header('Location: ?uri=detail_offre&id=' . $id_offre);
        exit;
    }


}