<?php
/**
 * Fichier principal de routage de l'application
 * Gère les requêtes entrantes et les dirige vers les contrôleurs appropriés
 */

// Activation de l'affichage des erreurs pour le développement
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Démarrage de la session si elle n'est pas déjà active
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Chargement des dépendances et fichiers requis
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/models/OffreModel.php';
require_once __DIR__ . '/src/controllers/OffreController.php';
require_once __DIR__ . '/src/controllers/AuthentificationController.php';
require_once __DIR__ . '/twig.php';
require_once __DIR__ . '/dev/General/menu.php';
require_once __DIR__ . '/auth_check.php';

// Import des classes de contrôleurs
use App\controllers\CandidatureController;
use App\controllers\OffreController;
use App\controllers\AuthentificationController;
use App\controllers\EntrepriseController;
use App\controllers\WishlistController;
use App\controllers\UtilisateurController;
use Twig\TwigFunction;
use App\controllers\StatistiquesController;

/**
 * Connexion à la base de données
 */

$db_host = getenv('DB_HOST') ?: 'localhost';
$db_name = getenv('DB_NAME') ?: 'projet';
$db_user = getenv('DB_USER') ?: 'louis';
$db_pass = getenv('DB_PASS') ?: 'motdepasse';
$db_port = getenv('DB_PORT') ?: '3307';
try {
    $db = new PDO(
        "mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8",
        $db_user,
        $db_pass
    );

    // Configuration des attributs PDO t
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $db->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
} catch (PDOException $e) {
    die("Erreur de connexion à la base : " . $e->getMessage());
}

/**
 * Configuration de Twig
 */
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/src/views');
$twig = new \Twig\Environment($loader, ['cache' => false]);

// Fonction Twig pour générer des URLs de routes
$function = new TwigFunction('path', function ($name) {
    $routes = [
        'connexion' => '?uri=connexion',
        'accueil' => '?uri=accueil',
        'annonce' => '?uri=annonce',
        'postuler' => '?uri=postuler',
        'profil' => '?uri=profil',
        'recherche' => '?uri=recherche',
        'creation_offre' => '?uri=creation_offre',
        'modifier_offre' => '?uri=modifier_offre',
        'supprimer_offre' => '?uri=supprimer_offre',
        'statistiques' => '?uri=statistiques',
        'deconnexion' => '?uri=deconnexion'
    ];
    return $routes[$name] ?? '/';
});
$twig->addFunction($function);

// Fonction Twig pour générer des URLs d'assets
$assetFunction = new TwigFunction('asset', function ($path) {
    return "/jobs/web/$path"; // Ajustez selon votre structure
});
$twig->addFunction($assetFunction);

/**
 * Vérifie si l'utilisateur est connecté
 * @return bool True si l'utilisateur est connecté, false sinon
 */
function isConnect() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Vérifie les permissions et redirige vers la page de connexion si non connecté
 * @return void
 */
function verifyPermission() {
    if (!isConnect()) {
        header('Location: ?uri=connexion');
        exit;
    }
}

/**
 * Routage des requêtes
 * Récupère l'URI demandée et dirige vers le contrôleur approprié
 */
$uri = $_GET['uri'] ?? 'accueil';

switch ($uri) {
    /**
     * Route: connexion
     * Affiche le formulaire de connexion ou traite la soumission
     */
    case 'connexion':
        $controller = new AuthentificationController($db, $twig);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->traiterFormulaire();
        } else {
            $controller->afficherAuthentifier('login');
        }
        break;

    /**
     * Route: mentions_legales
     * Affiche les metions legales
     */
    case 'mentions_legales':
        $role_id = $_SESSION['role_id'] ?? 4; // 4 = visiteur par défaut
        echo $twig->render('mentions_legales.twig', [
            'role_id' => $role_id,
            'menu_items' => menu($role_id)
        ]);
        break;

    /**
     * Route: accueil
     * Affiche la page d'accueil avec les dernières offres
     */
    case 'accueil':
        $role_id = $_SESSION['role_id'] ?? 4;
        $offreController = new OffreController($db, $twig);
        $offreController->LastOffres($role_id);
        break;

    /**
     * Route: offre
     * Affiche la liste des offres
     */
    case 'offre':
        $role_id = $_SESSION['role_id'] ?? 4;
        $offreController = new OffreController($db, $twig);
        $offreController->afficher5Offre($role_id);
        break;

    /**
     * Route: entreprise
     * Affiche la liste des entreprises
     */
    case 'entreprise':
        $role_id = $_SESSION['role_id'] ?? 4;
        $entrepriseController = new EntrepriseController($db);
        $entrepriseController->afficher5Entreprise($role_id);
        break;

    /**
     * Route: liste-offres
     * Affiche la liste des offres (alias de offre)
     */
    case 'liste-offres':
        $role_id = $_SESSION['role_id'] ?? 4;
        $offreController = new OffreController($db, $twig);
        $offreController->afficher5Offre($role_id);
        break;

    /**
     * Route: recherche
     * Affiche la page de recherche d'utilisateurs
     */
    case 'recherche':
        $role_id = $_SESSION['role_id'] ?? 4;
        $ctrl = new UtilisateurController($db, $twig);
        $ctrl->afficherRecherche($role_id);
        break;

    /**
     * Route: deconnexion
     * Déconnecte l'utilisateur
     */
    case 'deconnexion':
        $authController = new AuthentificationController($db, $twig);
        $authController->deconnecter();
        break;

    /**
     * Route: profil
     * Affiche le profil de l'utilisateur connecté
     */
    case 'profil':
        $user_id = $_SESSION['user_id'] ?? null;
        $role_id = $_SESSION['role_id'] ?? 4;
        $ctrl = new UtilisateurController($db, $twig);
        $ctrl->afficherProfil($user_id, $role_id);
        break;

    /**
     * Route: detail_profil
     * Affiche les détails d'un profil utilisateur spécifique
     */
    case 'detail_profil':
        if (isset($_GET['id'])) {
            $ctrl = new UtilisateurController($db);
            $ctrl->afficherDetailProfil($_GET['id']);
        }
        break;

    /**
     * Route: statistiques_etudiant
     * Affiche les statistiques d'un étudiant (admin et pilote uniquement)
     */
    case 'statistiques_etudiant':
        if (isset($_GET['id']) && isset($_SESSION['role_id']) && ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2)) {
            $ctrl = new UtilisateurController($db);
            $ctrl->afficherStatistiquesEtudiant($_GET['id']);
        } else {
            header('Location: ?uri=accueil');
        }
        break;

    case 'statistiques':
        $role_id = $_SESSION['role_id'] ?? 4; // 4 = visiteur par défaut
        $statistiquesController = new StatistiquesController($db);
        $statistiquesController->afficherStatistiques($role_id);
        break;

    /**
     * Route: creation_profil
     * Affiche le formulaire de création de profil (admin et pilote uniquement)
     */
    case 'creation_profil':
        if (isset($_SESSION['role_id']) && ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2)) {
            $ctrl = new UtilisateurController($db, $twig);
            $ctrl->afficherCreationProfil();
        } else {
            header('Location: ?uri=accueil');
            exit;
        }
        break;

    /**
     * Route: ajouter_profil
     * Traite la soumission du formulaire de création de profil
     */
    case 'ajouter_profil':
        if (
            isset($_SESSION['role_id'])
            && ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2)
            && $_SERVER['REQUEST_METHOD'] === 'POST'
        ) {
            $ctrl = new UtilisateurController($db, $twig);
            $ctrl->ajouterProfil();
        } else {
            header('Location: ?uri=accueil');
            exit;
        }
        break;

    /**
     * Route: supprimer_profil
     * Supprime un profil utilisateur
     */
    case 'supprimer_profil':
        if (isset($_GET['id']) && isset($_SESSION['role_id'])) {
            $ctrl = new UtilisateurController($db);
            $ctrl->supprimerProfil($_GET['id'], $_SESSION['role_id']);
        } else {
            header('Location: ?uri=accueil');
        }
        break;

    /**
     * Route: detail_offre
     * Affiche les détails d'une offre spécifique
     */
    case 'detail_offre':
        $id_offre = $_GET['id'] ?? 1;
        $role_id = $_SESSION['role_id'] ?? 4;
        $offreController = new OffreController($db, $twig);
        $offreController->afficherOffre($id_offre, $role_id);
        break;

    /**
     * Route: detail_entreprise
     * Affiche les détails d'une entreprise spécifique
     * Redirige vers la connexion si visiteur
     */
    case 'detail_entreprise':
        $id_entreprise = $_GET['id'] ?? null;
        $role_id = $_SESSION['role_id'] ?? 4;

        // Si c'est un visiteur (role_id = 4), rediriger vers la connexion
        if ($role_id == 4) {
            header('Location: ?uri=connexion');
            exit;
        }

        if ($id_entreprise) {
            $entrepriseController = new EntrepriseController($db);
            $entrepriseController->afficherEntreprise($id_entreprise, $role_id);
        } else {
            header('Location: ?uri=entreprise');
            exit;
        }
        break;


    /**
     * Route: noter_entreprise
     * note une entreprise (admin et pilote uniquement)
     */
    case 'noter_entreprise':
        if (isset($_SESSION['role_id']) && ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2)) {
            $entrepriseController = new EntrepriseController($db);
            $entrepriseController->noterEntreprise();
        } else {
            header('Location: ?uri=connexion');
        }
        break;

    /**
     * Route: modifier_entreprise
     * modifie une entreprise (admin et pilote uniquement)
     */
    case 'modifier_entreprise':
        if (isset($_GET['id']) && isset($_SESSION['role_id']) && ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2)) {
            $entrepriseController = new EntrepriseController($db);
            $entrepriseController->modifierEntreprise($_GET['id'], $_SESSION['role_id']);
        } else {
            header('Location: ?uri=connexion');
        }
        break;

    /**
     * Route: supprimer_entreprise
     * Supprime une entreprise (admin uniquement)
     */
    case 'supprimer_entreprise':
        $role_id = $_SESSION['role_id'] ?? 4;
        if ($role_id == 1) {
            $id_entreprise = $_GET['id'] ?? null;
            if ($id_entreprise) {
                $entrepriseController = new EntrepriseController($db);
                $entrepriseController->supprimerEntreprise($id_entreprise, $role_id);
            } else {
                header('Location: ?uri=entreprise');
            }
        } else {
            header('Location: ?uri=connexion');
        }
        break;

    /**
     * Route: wishlist
     * Affiche la wishlist de l'utilisateur (étudiants uniquement)
     */
    case 'wishlist':
        $role_id = $_SESSION['role_id'] ?? 4;
        if ($role_id == 3) {
            $wishlistController = new WishlistController($db, $twig);
            $wishlistController->afficherWishlist();
        } else {
            header('Location: ?uri=connexion');
        }
        break;

    /**
     * Route: wishlist_toggle
     * Ajoute ou supprime une offre de la wishlist (AJAX)
     */
    case 'wishlist_toggle':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && ($_SESSION['role_id'] ?? 0) == 3) {
            $wishlistController = new WishlistController($db, $twig);
            $wishlistController->toggleWishlist();
        }
        break;

    /**
     * Route: wishlistsuppr
     * Supprime une offre de la wishlist
     */
    case 'wishlistsuppr':
        $role_id = $_SESSION['role_id'] ?? 4;
        if ($role_id == 3 && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $wishlistController = new WishlistController($db, $twig);
            $wishlistController->supprimerDeWishlist();
        }
        header('Location: ?uri=wishlist');
        exit;
        break;

    /**
     * Route: creation_offre
     * Affiche le formulaire de création d'offre (admin uniquement)
     */
    case 'creation_offre':
        $role_id = $_SESSION['role_id'] ?? 4;
        if ($role_id == 1) {
            $offreController = new OffreController($db, $twig);
            $offreController->creerOffre($role_id);
        } else {
            header('Location: ?uri=connexion');
        }
        break;

    /**
     * Route: modifier_offre
     * Modifie une offre existante (pilote et admin uniquement)
     */
    case 'modifier_offre':
        if (isset($_GET['id']) && isset($_SESSION['role_id']) && ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2)) {
            $offreController = new OffreController($db, $twig);
            $offreController->modifierOffre($_GET['id'], $_SESSION['role_id']);
        } else {
            header('Location: ?uri=connexion');
        }
        break;


    /**
     * Route: supprimer_offre
     * Supprime une offre
     */
    case 'supprimer_offre':
        verifyPermission();
        (new OffreController($db, $twig))->supprimer();
        break;

    /**
     * Route: traiter_candidature
     * Traite une candidature (étudiants uniquement)
     */
    case 'traiter_candidature':
        $role_id = $_SESSION['role_id'] ?? 4;
        if ($role_id == 3) {
            $candidatureController = new CandidatureController($db, $twig);
            $candidatureController->traiterCandidature();
        } else {
            header('Location: ?uri=connexion');
        }
        break;

    /**
     * Route: candidature
     * Affiche les candidatures de l'utilisateur (étudiants uniquement)
     */
    case 'candidatures':
        $role_id = $_SESSION['role_id'] ?? 4;
        if ($role_id == 3) {
            $candidatureController = new CandidatureController($db, $twig);
            $candidatureController->afficherCandidatures();
        } else {
            header('Location: ?uri=connexion');
        }
        break;

    /**
     * Route: postuler_offre
     * Affiche le formulaire pour postuler à une offre
     */
    case 'postuler_offre':
        $id_offre = $_GET['id'] ?? null;
        $role_id = $_SESSION['role_id'] ?? 4;
        $candidatureController = new CandidatureController($db, $twig);
        $candidatureController->afficherFormulairePostuler($id_offre, $role_id);
        break;

    /**
     * Route: creation_entreprise
     * Affiche le formulaire de création d'entreprise (admin uniquement)
     */
    case 'creation_entreprise':
        $role_id = $_SESSION['role_id'] ?? 4;
        if ($role_id == 1) {
            $entrepriseController = new EntrepriseController($db);
            $entrepriseController->afficherFormulaireCreation($role_id);
        } else {
            header('Location: ?uri=connexion');
        }
        break;

    /**
     * Route par défaut (404)
     * Affiche une page d'erreur 404
     */
    default:
        http_response_code(404);
        echo '404 Not Found';
        break;
}
