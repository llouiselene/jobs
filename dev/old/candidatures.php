<?php
session_status() === PHP_SESSION_NONE && session_start();

// Le rôle de l'utilisateur soit stocké dans $_SESSION['role']
// Valeurs possibles : 'etudiant' ou 'admin'
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'etudiant'; // Par défaut, étudiant

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job*</title>
    <link rel="stylesheet" href="/jobs/dev/assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
<?php include '../General/menu.php'; ?>

<hr class="hr-large">

<div class="title-and-search">
    <div class="Title-box">Candidature</div>
    <div class="search-container">
        <div class="search-icon-container" id="searchIcon">
            <i class="fas fa-search" style="color: white;"></i>
        </div>
    </div>
</div>

<input type="text" id="searchInput" class="search-input" placeholder="Rechercher...">

<div class="space2-5"></div>

<div class="Title"> Liste des Candidature :</div>

<div class="space5"></div>

<div class="job-listing">
    <div class="vertical-scroll-bar">
        <a class="job-box" href="/jobs/dev/Etudiant/Offre_Detail.php">
            <h2>Stage en informatique</h2>
            <p>Entreprise: Siemens</p>
            <p>Département: dev</p>
            <p>Technologie: html</p>
            <div class ="right-align">
                <button class="button">En cours...</button>
            </div>
        </a>
        <a class="job-box" href="/jobs/dev/Etudiant/Offre_Detail.php">
            <h2>Stage en informatique</h2>
            <p>Entreprise: Siemens</p>
            <p>Département: front end</p>
            <p>Technologie: html</p>
            <div class ="right-align">
                <button class="button">Refusée</button>
            </div>
        </a>
    </div>
</div>
<div class="footer">
    <a href="/jobs/dev/General/Mentions_Legales.php">Mentions Légales</a> - 2025 Job* - À propos
</div>

<script src="/jobs/dev/assets/script.js"></script>
</body>
</html>
