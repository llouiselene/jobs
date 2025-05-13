<?php
session_status() === PHP_SESSION_NONE && session_start();
$host = '20.107.86.55';
$dbname = 'Projet';
$username = 'UserTest';
$password = 'Test1234!';
require_once __DIR__ . '/../../twig.php';
require_once __DIR__ . '/../General/menu.php';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'admin';
use App\controllers\OffreController;
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $offreControll = new OffreController($pdo);
    $offreControll->ajouterOffre($role);
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
?>