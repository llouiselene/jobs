<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: ?uri=admin_dashboard'); // ou accueil selon le rôle
    exit;
}

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // Redirection selon le rôle
    switch ($_SESSION['role_id']) {
        case 1: header('Location: ?uri=admin_dashboard'); break;
        case 2: header('Location: ?uri=etudiant_accueil'); break;
        case 3: header('Location: ?uri=pilote_accueil'); break;
        default: header('Location: ?uri=accueil'); break;
    }
    exit;
}


