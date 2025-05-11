<?php
session_status() === PHP_SESSION_NONE && session_start();

// Supposons que le rôle de l'utilisateur soit stocké dans $_SESSION['role']
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
<?php include __DIR__ . '/menu.php'; ?>

<hr class="hr-large">

<?php if ($role == 'admin') { ?>
    <div class="title-and-search">
        <div class="Title-box">Mes Offres</div>
        <div class="search-container">
            <div class="search-icon-container" id="searchIcon">
                <i class="fas fa-search" style="color: white;"></i>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="title-and-search">
        <div class="Title-box">Offres disponibles</div>
        <div class="search-container">
            <div class="search-icon-container" id="searchIcon">
                <i class="fas fa-search" style="color: white;"></i>
            </div>
        </div>
    </div>
<?php } ?>

<input type="text" id="searchInput" class="search-input" placeholder="Rechercher...">

<div class="space2-5"></div>

<div class="Title"> Liste des offres :</div>



<?php if ($role == 'admin') { ?>
    <div class="job-listing">
        <div class="vertical-scroll-bar">
            <a class="job-box" href="Offre_Detail.php">
                <h2>Stage en informatique</h2>
                <p>Entreprise: Siemens</p>
                <p>Département: dev</p>
                <p>Technologie: html</p>
            </a>
            <a class="job-box" href="Offre_Detail.php">
                <h2>Stage en informatique</h2>
                <p>Entreprise: Siemens</p>
                <p>Département: front end</p>
                <p>Technologie: html</p>
            </a>
        </div>
    </div>
<?php } else { ?>
    <div class="job-listing">
        <div class="vertical-scroll-bar">
            <a class="job-box" href="/jobs/dev/Etudiant/Offre_Detail.php">
                <h2>Stage en informatique</h2>
                <p>Entreprise: Siemens</p>
                <p>Département: dev</p>
                <p>Technologie: html</p>
                <p>Nombre de candidatures : x</p>
            </a>
            <a class="job-box" href="/jobs/dev/Etudiant/Offre_Detail.php">
                <h2>Stage en informatique</h2>
                <p>Entreprise: Siemens</p>
                <p>Département: front end</p>
                <p>Technologie: html</p>
                <p>Nombre de candidatures : x</p>
            </a>
        </div>
    </div>
<?php } ?>

<div class="footer">
    <a href="/jobs/dev/General/Mentions_Legales.php">Mentions Légales</a> - 2025 Job* - À propos
</div>

<script src="/jobs/dev/assets/script.js"></script>
</body>
</html>

