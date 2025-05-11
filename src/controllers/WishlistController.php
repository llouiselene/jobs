<?php
namespace App\controllers;

use App\models\WishlistModel;
use App\models\OffreModel;
use Twig\Environment;

/**
 * Contrôleur pour la gestion des wishlists
 * Gère les actions liées aux wishlists des étudiants
 */
class WishlistController extends Controller
{
    /**
     * Modèle pour les opérations sur les wishlists
     * @var WishlistModel
     */
    private $wishlistModel;

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
        $this->wishlistModel = new WishlistModel($db);
        $this->twig = $twig;
    }

    /**
     * Affiche la wishlist d'un utilisateur (étudiants uniquement)
     *
     * @return void
     */
    public function afficherWishlist() {
        $role_id = $_SESSION['role_id'] ?? 4;
        $user_id = $_SESSION['user_id'] ?? null;

        if ($role_id != 3 || !$user_id) {
            header('Location: ?uri=connexion');
            exit;
        }

        $page = (int)($_GET['page'] ?? 1);
        $parPage = 10;

        // Calcul de la pagination
        $total = $this->wishlistModel->countWishlist($user_id);
        $totalPages = ceil($total / $parPage);

        $offres = $this->wishlistModel->getWishlist($user_id, $page, $parPage);

        // Marque chaque offre comme dans la wishlist (utile pour le cœur)
        foreach ($offres as &$offre) {
            $offre['in_wishlist'] = true;
        }
        unset($offre);

        echo $this->twig->render('wishlist.twig', [
            'offres' => $offres,
            'page' => $page,
            'totalPages' => $totalPages,
            'menu_items' => menu($role_id),
            'role_id' => $role_id
        ]);
    }

    /**
     * Supprime une offre de la wishlist d'un utilisateur
     *
     * @return void
     */
    public function supprimerDeWishlist() {
        $role_id = $_SESSION['role_id'] ?? 4;
        $user_id = $_SESSION['user_id'] ?? null;

        if ($role_id != 3 || !$user_id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?uri=connexion');
            exit;
        }

        $id_offre = $_POST['id_offre'] ?? null;

        if ($id_offre) {
            $this->wishlistModel->supprimerDeWishlist($user_id, $id_offre);
        }

        header('Location: ?uri=wishlist');
        exit;
    }

    /**
     * Ajoute ou supprime une offre de la wishlist (bascule)
     * Utilisé par les requêtes AJAX
     *
     * @return void
     */
    public function toggleWishlist() {
        $role_id = $_SESSION['role_id'] ?? 4;
        $user_id = $_SESSION['user_id'] ?? null;

        if ($role_id != 3 || !$user_id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo "ERROR";
            exit;
        }

        $id_offre = $_POST['id_offre'] ?? null;

        if (!$id_offre) {
            echo "ERROR";
            exit;
        }

        $success = false;

        if ($this->wishlistModel->isInWishlist($user_id, $id_offre)) {
            $success = $this->wishlistModel->supprimerDeWishlist($user_id, $id_offre);
        } else {
            $success = $this->wishlistModel->ajouterAWishlist($user_id, $id_offre);
        }

        echo $success ? 'OK' : 'ERROR';
        exit;
    }
}
