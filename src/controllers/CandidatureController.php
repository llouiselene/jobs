<?php
namespace App\controllers;

use App\models\CandidatureModel;
use App\models\OffreModel;
use App\models\UtilisateurModel;
use Twig\Environment;

/**
 * Contrôleur pour la gestion des candidatures
 * Gère les actions liées aux candidatures des étudiants aux offres
 */
class CandidatureController extends Controller
{
    /**
     * Modèle pour les opérations sur les candidatures
     * @var CandidatureModel
     */
    private $candidatureModel;

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
        $this->candidatureModel = new CandidatureModel($db);
        $this->twig = $twig;
    }

    /**
     * Affiche le formulaire pour postuler à une offre (étudiants uniquement)
     *
     * @param int $id_offre ID de l'offre concernée
     * @param int $role_id ID du rôle de l'utilisateur connecté
     * @return void
     */
    public function afficherFormulairePostuler($id_offre, $role_id)
    {
        if ($role_id != 3) {
            header('Location: ?uri=accueil');
            exit;
        }

        // Récupération des modèles nécessaires
        $db = $this->candidatureModel->getDb();
        $offreModel = new OffreModel($db);
        $utilisateurModel = new UtilisateurModel($db);

        // Récupération des données
        $user = $utilisateurModel->getUtilisateurById($_SESSION['user_id']);
        $offre = $offreModel->getOffreById($id_offre);

        echo $this->twig->render('postuler_offre.twig', [
            'offre' => $offre,
            'user' => $user,
            'role_id' => $role_id,
            'menu_items' => menu($role_id)
        ]);
    }

    /**
     * Traite la soumission d'une candidature (étudiants uniquement)
     * Gère l'upload du CV et l'enregistrement de la candidature
     *
     * @return void
     */
    public function traiterCandidature()
    {
        // Vérifier que l'utilisateur est connecté et est un étudiant
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 3) {
            header('Location: ?uri=connexion');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?uri=accueil');
            exit;
        }

        // Récupérer les données du formulaire
        $id_offre = $_POST['id_offre'] ?? null;
        $lettre_motivation = $_POST['lettre_motivation'] ?? '';
        $id_utilisateur = $_SESSION['user_id'] ?? null;

        // Validation basique
        $errors = [];
        if (empty($lettre_motivation)) {
            $errors['lettre_motivation'] = "Veuillez fournir une lettre de motivation";
        }

        // Traitement du CV
        $cv_uploaded = false;
        if (isset($_FILES['cv']) && $_FILES['cv']['error'] == 0) {
            // Dossier de destination
            $upload_dir = 'uploads/cv/';

            // Vérifier que le dossier existe, sinon le créer
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Générer un nom unique pour le fichier
            $filename = $id_utilisateur . '_' . $id_offre . '_' . time() . '_' . $_FILES['cv']['name'];
            $destination = $upload_dir . $filename;

            // Déplacer le fichier uploadé
            if (move_uploaded_file($_FILES['cv']['tmp_name'], $destination)) {
                $cv_uploaded = true;
            } else {
                $errors['cv'] = "Erreur lors de l'upload du CV";
            }
        } else {
            $errors['cv'] = "Veuillez fournir votre CV";
        }

        // S'il y a des erreurs, réafficher le formulaire
        if (!empty($errors)) {
            $db = $this->candidatureModel->getDb();
            $offreModel = new OffreModel($db);
            $utilisateurModel = new UtilisateurModel($db);

            $offre = $offreModel->getOffreById($id_offre);
            $user = $utilisateurModel->getUtilisateurById($id_utilisateur);

            echo $this->twig->render('postuler_offre.twig', [
                'offre' => $offre,
                'user' => $user,
                'role_id' => $_SESSION['role_id'],
                'menu_items' => menu($_SESSION['role_id']),
                'errors' => $errors,
                'form_data' => $_POST
            ]);
            return;
        }

        // Enregistrer la candidature
        if ($this->candidatureModel->addCandidature($id_offre, $id_utilisateur, $lettre_motivation)) {
            // Rediriger avec un message de succès
            header('Location: ?uri=candidatures&success=1');
            exit;
        } else {
            // Rediriger avec un message d'erreur
            header('Location: ?uri=detail_offre&id=' . $id_offre . '&error=1');
            exit;
        }
    }

    /**
     * Affiche les candidatures de l'utilisateur connecté
     *
     * @return void
     */
    public function afficherCandidatures()
    {
        $role_id = $_SESSION['role_id'] ?? 4;
        $user_id = $_SESSION['user_id'] ?? null;

        if ($role_id != 3 || !$user_id) {
            header('Location: ?uri=connexion');
            exit;
        }

        $candidatures = $this->candidatureModel->getCandidaturesByUtilisateur($user_id);

        echo $this->twig->render('candidatures.twig', [
            'candidatures' => $candidatures,
            'role_id' => $role_id,
            'menu_items' => menu($role_id)
        ]);
    }
}
