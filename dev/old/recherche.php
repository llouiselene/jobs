<?php
session_status() === PHP_SESSION_NONE && session_start();
require_once __DIR__ . '/../../twig.php';
require_once __DIR__ . '/../General/menu.php';
// Supposons que le rôle de l'utilisateur soit stocké dans $_SESSION['role']
// Valeurs possibles : 'etudiant' ou 'admin'
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'admin'; // Par défaut, étudiant

$recherche = [
    ['nom' => 'Louis Martin', 'role' => 'admin'],
    ['nom' => 'Emma Dupont', 'role' => 'pilote'],
    ['nom' => 'Paul Durand', 'role' => 'etudiant'],
    ['nom' => 'Sophie Bernard', 'role' => 'etudiant'],
    ['nom' => 'Lucas Moreau', 'role' => 'pilot'],
    ['nom' => 'Julie Lefèvre', 'role' => 'etudiant'],
    ['nom' => 'Nicolas Petit', 'role' => 'admin'],
    ['nom' => 'Clara Girard', 'role' => 'pilote'],
    ['nom' => 'Thomas Roux', 'role' => 'etudiant'],
    ['nom' => 'Alice Fontaine', 'role' => 'admin']
];


$parPage = 9;
$totalPages = ceil(count($recherche) / $parPage);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1 || $page > $totalPages) {
    $page = 1; //on revient à la première page si invalide
}

$debut = ($page - 1) * $parPage;
$recherchepage = array_slice($recherche, $debut, $parPage);

// Inclure Twig
require_once __DIR__ . '/../../twig.php'; // Inclure la configuration Twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../../templates');
$twig = new \Twig\Environment($loader);

// Rendre le template Twig
echo $twig->render('recherche.twig', [
    'recherches' => $recherchepage,
    'page' => $page,
    'totalPages' => $totalPages,
    'role' => $role,
    'menu_items' => menu($role)
]);