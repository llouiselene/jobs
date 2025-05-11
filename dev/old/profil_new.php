<?php
session_status() === PHP_SESSION_NONE && session_start();
require_once __DIR__ . '/../../twig.php';
require_once __DIR__ . '/../General/menu.php';
require_once __DIR__ . '/../../index.php';

// Définir les variables pour le template
$user = [
    'nom' => 'John',
    'prenom' => 'Doe',
    'genre' => 'homme',
    'adresse' => '123 Rue Exemple',
    'email' => 'john.doe@example.com',
    'mdp' => 'password123',
    'telephone' => '0123456789',
    'date_naissance' => '2000-01-01'
];
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'admin'; // Par défaut, étudiant

echo $twig->render('profil.twig', [
    'user' => $user,
    'role' => $role,
    'menu_items' => menu($role)
]);