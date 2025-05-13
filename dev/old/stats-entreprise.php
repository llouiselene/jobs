<?php
session_status() === PHP_SESSION_NONE && session_start();
require_once __DIR__ . '/../../twig.php';
require_once __DIR__ . '/../General/menu.php';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'etudiant'; // Par défaut, rôle 'etudiant'

$entreprise = [
    'nom' => 'CESI',
    'nombre_offres' => 586,
    'moyenne' => '3,9'
];

// Render the Twig template
echo $twig->render('stats_entreprise.twig', [
    'role' => $role,
    'entreprise' => $entreprise,
    'menu_items' => menu($role)
]);