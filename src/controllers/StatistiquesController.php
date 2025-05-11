<?php
namespace App\controllers;

use App\models\StatistiquesModel;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Contrôleur pour la gestion des statistiques
 * Affiche les statistiques globales sur les entreprises, offres et candidatures
 */
class StatistiquesController extends Controller
{
    /**
     * Modèle pour les opérations sur les statistiques
     * @var StatistiquesModel
     */
    protected $model;

    /**
     * Moteur de template Twig
     * @var Environment
     */
    protected $templateEngine;

    /**
     * Constructeur du contrôleur
     * Initialise le modèle et le moteur de template
     *
     * @param PDO $db Instance de connexion à la base de données
     */
    public function __construct($db)
    {
        $this->model = new StatistiquesModel($db);
        $this->templateEngine = new Environment(new FilesystemLoader(__DIR__ . '/../views'));
    }

    /**
     * Affiche la page des statistiques globales
     * Accessible à tous les utilisateurs
     *
     * @param int $role_id ID du rôle de l'utilisateur connecté
     * @return void
     */
    public function afficherStatistiques($role_id)
    {
        // Récupération de toutes les statistiques en une seule fois
        $stats = $this->model->getDashboardStats();

        // Préparation des données pour la vue
        $viewData = [
            'stats' => $stats,
            'role_id' => $role_id,
            'menu_items' => menu($role_id),
            'page_title' => 'Tableau de bord statistique'
        ];

        // Rendu de la vue
        echo $this->templateEngine->render('statistiques.twig', $viewData);
    }

    /**
     * Affiche les statistiques détaillées d'une entreprise spécifique
     * Accessible aux administrateurs et pilotes uniquement
     *
     * @param int $id_entreprise ID de l'entreprise
     * @param int $role_id ID du rôle de l'utilisateur connecté
     * @return void
     */
    public function afficherStatistiquesEntreprise($id_entreprise, $role_id)
    {
        // Vérification des permissions
        if ($role_id != 1 && $role_id != 2) {
            header('Location: ?uri=accueil');
            exit;
        }

        // Récupération des statistiques spécifiques à l'entreprise
        // Cette méthode serait à implémenter dans le modèle
        $stats = $this->model->getEntrepriseStats($id_entreprise);

        // Rendu de la vue
        echo $this->templateEngine->render('statistiques_entreprise.twig', [
            'stats' => $stats,
            'role_id' => $role_id,
            'menu_items' => menu($role_id),
            'page_title' => 'Statistiques de l\'entreprise'
        ]);
    }

    /**
     * Génère et télécharge un rapport statistique au format CSV
     * Accessible aux administrateurs uniquement
     *
     * @param int $role_id ID du rôle de l'utilisateur connecté
     * @return void
     */
    public function genererRapportCSV($role_id)
    {
        // Vérification des permissions (admin uniquement)
        if ($role_id != 1) {
            header('Location: ?uri=accueil');
            exit;
        }

        // Récupération des données
        $stats = $this->model->getDashboardStats();

        // Préparation du fichier CSV
        $filename = 'rapport_statistiques_' . date('Y-m-d') . '.csv';

        // En-têtes HTTP pour le téléchargement
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Création du flux de sortie
        $output = fopen('php://output', 'w');

        // En-têtes CSV
        fputcsv($output, ['Catégorie', 'Valeur']);

        // Données totales
        fputcsv($output, ['Nombre total d\'entreprises', $stats['totals']['entreprises']]);
        fputcsv($output, ['Nombre total d\'offres', $stats['totals']['offres']]);
        fputcsv($output, ['Nombre total de candidatures', $stats['totals']['candidatures']]);

        // Fermeture du flux
        fclose($output);
        exit;
    }
}
