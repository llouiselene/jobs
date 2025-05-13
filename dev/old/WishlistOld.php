<?php
session_status() === PHP_SESSION_NONE && session_start();
// Supposons que le rôle de l'utilisateur soit stocké dans $_SESSION['role']
// Valeurs possibles : 'etudiant'
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'etudiant'; // Seule possible, étudiant

// Simulated job data (this should ideally come from a database)
$jobs = [
    [
        'title' => 'Stage en informatique',
        'company' => 'Siemens',
        'department' => 'dev',
        'skills' => 'html',
    ],
    [
        'title' => 'Stage en marketing',
        'company' => 'L\'Oréal',
        'department' => 'Digital Marketing',
        'skills' => 'SEO, SEM',
    ],
    [
        'title' => 'Stage en finance',
        'company' => 'BNP Paribas',
        'department' => 'Analyse financière',
        'skills' => 'Excel, VBA',
    ],
    [
        'title' => 'Stage en design',
        'company' => 'Apple',
        'department' => 'UX/UI Design',
        'skills' => 'Sketch, Figma',
    ],
];

// Include the Twig template for rendering
require_once '/../../vendor/autoload.php';
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);
echo $twig->render('wishlist.twig', ['jobs' => $jobs]);
?>