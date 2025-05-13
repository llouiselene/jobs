<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO(
        'mysql:host=localhost;port=3307;dbname=projet;charset=utf8',
        'root',   // Utilisateur MySQL
        ''        // Mot de passe MySQL (vide par défaut sur XAMPP)
    );
} catch (PDOException $e) {
    die("Erreur de connexion à la base : " . $e->getMessage());
}

$stmt = $pdo->query("SELECT Id_Utilisateur, Mot_de_Passe FROM Utilisateur");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $user) {
    $id = $user['Id_Utilisateur'];
    $mdp_clair = $user['Mot_de_Passe'];

    // On force le hashage pour tous (même si déjà hashé, ce qui est rare dans ton cas)
    $mdp_hash = password_hash($mdp_clair, PASSWORD_DEFAULT);

    // Met à jour la base
    $stmtUpdate = $pdo->prepare("UPDATE Utilisateur SET Mot_de_Passe = ? WHERE Id_Utilisateur = ?");
    $stmtUpdate->execute([$mdp_hash, $id]);
    echo "Utilisateur $id : mot de passe hashé.<br>";
}

echo "Opération terminée !";
