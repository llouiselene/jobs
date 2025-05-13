<?php
try {
    $db = new PDO(
        "mysql:host=localhost;port=3307;dbname=projet;charset=utf8",
        "root",
        ""
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Active les erreurs PDO

    $stmt = $db->query("SELECT * FROM utilisateur");
    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<pre>";
    print_r($resultats);
    echo "</pre>";
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
