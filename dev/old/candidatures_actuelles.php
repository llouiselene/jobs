<?php
$host = '20.107.86.55';
$dbname = 'Projet';
$username = 'UserTest';
$password = 'Test1234!';
session_status() === PHP_SESSION_NONE && session_start();
require_once __DIR__ . '/../../twig.php';
require_once __DIR__ . '/../General/menu.php';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'etudiant';
use App\controllers\CandidatureController;
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $candidatureControll = new CandidatureController($pdo);
    $candidatureControll->getCandidature();
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
?>
