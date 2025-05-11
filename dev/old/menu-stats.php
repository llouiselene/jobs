<?php
session_status() === PHP_SESSION_NONE && session_start();
require_once __DIR__ . '/../../twig.php'; // Inclure la configuration Twig
require_once __DIR__ . '/menu.php';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'etudiant'; // Par défaut, rôle 'etudiant'

// Définir les variables pour le template
$total_offres = 4; // Example value, replace with actual logic to get the total offers

// Render the Twig template
echo $twig->render('menu-stats.twig', [
    'role' => $role,
    'total_offres' => $total_offres,
    'menu_items' => menu($role)
]);
?>