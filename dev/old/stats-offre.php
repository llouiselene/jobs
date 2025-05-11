<?php
session_status() === PHP_SESSION_NONE && session_start();
require_once __DIR__ . '/../../twig.php';
require_once __DIR__ . '/../General/menu.php';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'etudiant'; // Par défaut, rôle 'etudiant'

$jobDetails = [
    'titre' => 'Stage en informatique',
    'nbetudiants' => 19
];

// Render the Twig template
echo $twig->render('stats_offre.twig', [
    'role' => $role,
    'job' => $jobDetails,
    'menu_items' => menu($role)
]);