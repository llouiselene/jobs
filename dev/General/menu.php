<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Démarrage de la session si elle n'est pas déjà active
session_status() === PHP_SESSION_NONE && session_start();
require_once __DIR__ . '/../../twig.php';
require_once __DIR__ . '/../../index.php';


$role_id = $_SESSION['role_id'] ?? 4; // 4 = visiteur/anonyme par défaut

// Définition des liens du menu
function menu($role_id) {
    switch ($role_id) {
        case 1: // Administrateur
            return [
                ['href' => '?uri=accueil', 'label' => 'Accueil'],
                ['href' => '?uri=offre', 'label' => 'Offres'],
                ['href' => '?uri=entreprise', 'label' => 'Entreprise'],
                ['href' => '?uri=statistiques', 'label' => 'Statistiques'],
                ['href' => '?uri=recherche', 'label' => 'Chercher un profil'],
                ['href' => '?uri=deconnexion', 'label' => 'Deconnexion']
            ];
        case 2: // Pilote
            return [
                ['href' => '?uri=accueil', 'label' => 'Accueil'],
                ['href' => '?uri=offre', 'label' => 'Offres'],
                ['href' => '?uri=entreprise', 'label' => 'Entreprise'],
                ['href' => '?uri=recherche', 'label' => 'Chercher un profil'],
                ['href' => '?uri=statistiques', 'label' => 'Statistiques'],
                ['href' => '?uri=deconnexion', 'label' => 'Deconnexion']
            ];
        case 3: // Étudiant
            return [
                ['href' => '?uri=accueil', 'label' => 'Accueil'],
                ['href' => '?uri=offre', 'label' => 'Offres'],
                ['href' => '?uri=entreprise', 'label' => 'Entreprise'],
                ['href' => '?uri=statistiques', 'label' => 'Statistiques'],
                ['href' => '?uri=wishlist', 'label' => 'Wishlist'],
                ['href' => '?uri=deconnexion', 'label' => 'Deconnexion']
            ];
        default: // Anonyme
            return [
                ['href' => '?uri=accueil', 'label' => 'Accueil'],
                ['href' => '?uri=offre', 'label' => 'Offres'],
                ['href' => '?uri=entreprise', 'label' => 'Entreprise'],
                ['href' => '?uri=connexion', 'label' => 'Connexion']
            ];
    }
}




?>