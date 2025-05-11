<?php

namespace App\controllers;
use App\models\UtilisateurModel;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Contrôleur pour la gestion des utilisateurs
 * Gère l'affichage et les actions liées aux utilisateurs (profils, recherche, statistiques)
 */
class UtilisateurController extends Controller
{
    /**
     * Constructeur du contrôleur
     * Initialise le modèle et le moteur de template
     *
     * @param PDO $db Instance de connexion à la base de données
     */
    public function __construct($db)
    {
        $this->model = new UtilisateurModel($db);
        $this->templateEngine = new Environment(new FilesystemLoader(__DIR__ . '/../views'));
    }

    /**
     * Affiche les statistiques d'un étudiant
     *
     * @param int $id_etudiant ID de l'étudiant dont on veut voir les statistiques
     * @return void
     */
    public function afficherStatistiquesEtudiant($id_etudiant) {
        // Récupération du rôle de l'utilisateur connecté
        $role_id_connecte = $_SESSION['role_id'] ?? 4;

        // Récupération des données de l'étudiant
        $etudiant = $this->model->getUtilisateurById($id_etudiant);

        if (!$etudiant || $etudiant['Id_Role'] != 3) {
            header('Location: ?uri=recherche&error=not_student');
            exit;
        }

        // Récupération des statistiques
        $stats = $this->model->getStatistiquesEtudiant($id_etudiant);

        echo $this->templateEngine->render('stats_etudiant.twig', [
            'etudiant' => $etudiant,
            'stats' => $stats,
            'role_id' => $role_id_connecte,
            'menu_items' => menu($role_id_connecte)
        ]);
    }

    /**
     * Affiche le profil d'un utilisateur
     *
     * @param int $id_utilisateur ID de l'utilisateur à afficher
     * @param string $role Rôle de l'utilisateur connecté
     * @return void
     */
    public function afficherProfil($id_utilisateur, $role)
    {
        $utilisateur = $this->model->getUtilisateurById($id_utilisateur);
        if (!$utilisateur) {
            echo "Utilisateur non trouvé.";
            return;
        }
        echo $this->templateEngine->render('profil.twig', [
            'utilisateur' => $utilisateur,
            'role' => $role,
            'menu_items' => menu(strtolower($role))
        ]);
    }

    /**
     * Affiche le formulaire de création de profil
     *
     * @return void
     */
    public function afficherCreationProfil() {
        echo $this->templateEngine->render('creation_profil.twig', [
            'role_id' => $_SESSION['role_id'] ?? 1,
            'role' => 'admin',
            'menu_items' => menu(1) // menu pour admin
        ]);
    }

    /**
     * Traite la soumission du formulaire de création de profil
     * Ajoute un nouvel utilisateur dans la base de données
     *
     * @return void
     */
    public function ajouterProfil() {
        // Récupère les champs du POST
        $nom = $_POST['nom'] ?? '';
        $prenom = $_POST['prenom'] ?? '';
        $genre = $_POST['genre'] ?? '';
        $email = $_POST['email'] ?? '';
        $telephone = $_POST['telephone'] ?? '';
        $date_naissance = $_POST['date_naissance'] ?? '';
        $mot_de_passe = $_POST['password'] ?? '';
        $id_role = $_POST['typeCompte'] ?? 3;

        // Vérification des permissions (pilote ne peut créer que des étudiants)
        if ($_SESSION['role_id'] == 2 && $id_role != 3) {
            // Pilote ne peut créer que des étudiants
            $errors['typeCompte'] = "Un pilote ne peut créer que des comptes étudiants.";
            header('Location: ?uri=creation_profil&erreur=1');
            exit;
        }

        // Ajoute l'utilisateur via le modèle
        $success = $this->model->addUtilisateur(
            $nom,
            $prenom,
            $email,
            $mot_de_passe,
            $genre,
            $telephone,
            $date_naissance,
            $id_role
        );

        if ($success) {
            header('Location: ?uri=profil&message=profil_cree');
        } else {
            header('Location: ?uri=creation_profil&erreur=1');
        }
        exit;
    }

    /**
     * Affiche la page de recherche d'utilisateurs avec filtrage selon le rôle
     *
     * @param int $role_id ID du rôle de l'utilisateur connecté
     * @return void
     */
    public function afficherRecherche($role_id)
    {
        $nom = $_GET['nom'] ?? '';
        $prenom = $_GET['prenom'] ?? '';
        $email = $_GET['email'] ?? '';

        // Filtre selon le rôle connecté
        $roleFilter = null;
        if ($role_id == 2) { // Pilote
            $roleFilter = 3; // 3 = étudiant
        }
        // Admin (1) : pas de filtre, tous les rôles

        // Utilise la nouvelle méthode
        if ($nom || $prenom || $email) {
            $resultats = $this->model->rechercherUtilisateurs($nom, $prenom, $email, $roleFilter);
        } else {
            // Affiche tout (avec filtre pour pilote)
            if ($roleFilter) {
                $resultats = $this->model->rechercherUtilisateurs('', '', '', $roleFilter);
            } else {
                $resultats = $this->model->getAllUtilisateurs();
            }
        }

        // Pagination (optionnel)
        $page = (int)($_GET['page'] ?? 1);
        $parPage = 10;
        $total = count($resultats);
        $totalPages = max(1, ceil($total / $parPage));
        $resultats = array_slice($resultats, ($page - 1) * $parPage, $parPage);

        echo $this->templateEngine->render('recherche.twig', [
            'recherches' => $resultats,
            'role_id' => $role_id,
            'page' => $page,
            'totalPages' => $totalPages,
            'menu_items' => menu($role_id),
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email
        ]);
    }

    /**
     * Affiche les détails d'un profil utilisateur
     *
     * @param int $id_utilisateur ID de l'utilisateur à afficher
     * @return void
     */
    public function afficherDetailProfil($id_utilisateur) {
        // Rôle de l'utilisateur CONNECTÉ (pas celui du profil consulté)
        $role_id_connecte = $_SESSION['role_id'] ?? 4;

        $utilisateur = $this->model->getUtilisateurById($id_utilisateur);

        if (!$utilisateur) {
            header('Location: ?uri=recherche');
            exit;
        }

        // Si c'est un étudiant, récupérer ses statistiques de base
        $stats = null;
        if ($utilisateur['Id_Role'] == 3) {
            $stats = [
                'nb_candidatures' => $this->model->getStatistiquesEtudiant($id_utilisateur)['nb_candidatures'] ?? 0
            ];
        }

        echo $this->templateEngine->render('detail_profil.twig', [
            'utilisateur' => $utilisateur,
            'role_id' => $role_id_connecte,
            'stats' => $stats,
            'menu_items' => menu($role_id_connecte)
        ]);
    }

    /**
     * Supprime un profil utilisateur avec vérification des permissions
     *
     * @param int $id_utilisateur ID de l'utilisateur à supprimer
     * @param int $role_id ID du rôle de l'utilisateur connecté
     * @return void
     */
    public function supprimerProfil($id_utilisateur, $role_id) {
        // Récupérer l'utilisateur à supprimer
        $utilisateur = $this->model->getUtilisateurById($id_utilisateur);

        // Vérifications
        if (!$utilisateur) {
            header('Location: ?uri=recherche&error=profil_inexistant');
            exit;
        }

        // Admin peut tout supprimer, Pilote seulement les étudiants
        $isAdmin = ($role_id == 1);
        $isPiloteTryingToDeleteEtudiant = ($role_id == 2 && $utilisateur['Id_Role'] == 3);

        if (!$isAdmin && !$isPiloteTryingToDeleteEtudiant) {
            header('Location: ?uri=detail_profil&id=' . $id_utilisateur . '&error=permission_refusee');
            exit;
        }

        // Suppression
        $success = $this->model->deleteUtilisateur($id_utilisateur);

        if ($success) {
            header('Location: ?uri=recherche&success=profil_supprime');
        } else {
            header('Location: ?uri=detail_profil&id=' . $id_utilisateur . '&error=echec_suppression');
        }
        exit;
    }
}
