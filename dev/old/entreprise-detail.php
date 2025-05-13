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
<div class="Title-box">Siemens</div>
<div class="space2-5"></div>

<div class="Title">Informations de l'entreprise :</div>
<div class="space5"></div>
<div class="text">Site web : www.siteinternet.com</div>
<div class="text">Date de création : JJ/MM/AAAA</div>
<div class="text">Domaine(s) d’activité : Domaine</div>
<div class="text">Nombre d’employés : X employés</div>
<div class="text">Coordonnées : Ville, Pays</div>
<div class="text">Responsable : Mr. Monsieur</div>



<?php if ($role === 'admin') { ?>
    <div class="space5"></div>

    <div class="Title">Modifier les informations de l'entreprise :</div>

    <div class="container">
        <div class="form-group">
            <label>Site Web :</label>
            <input type="text">
        </div>
        <div class="form-group">
            <label>Date de création :</label>
            <input type="text">
        </div>
        <div class="form-group">
            <label>Domaine(s) d'activité : </label>
            <input type="text">
        </div>
        <div class="form-group">
            <label>Nombre d'employés :</label>
            <input type="text">
        </div>
        <div class="form-group">
            <label>Ville :</label>
            <input type="text">
        </div>
        <div class="form-group">
            <label>Pays :</label>
            <input type="text">
        </div>
    </div>

    <button class="bouton-fixe-gauche"> Supprimer l'Entreprise</button>


    <div class="Title">Noter l'entreprise :</div>
    <a class="rate-company" href="/jobs/dev/Pilote/Noter_Entreprise.php">
        <i class="fas fa-star"></i>
    </a>


<?php } ?>

<div class="space5"></div>
<div class="Title"> Liste des offres disponibles de l'entreprise :</div>


<div class="job-listing">
    <div class="job-box">
        <h2>Stage en informatique</h2>
        <p>Entreprise: Siemens</p>
        <p>Département: dev</p>
        <p>Technologie: html</p>
    </div>
    <div class="job-box">
        <h2>Stage en informatique</h2>
        <p>Entreprise: Siemens</p>
        <p>Département: front end</p>
        <p>Technologie: html</p>
    </div>
</div>

<div class="footer">
    <a href="/jobs/dev/General/Mentions_Legales.html">Mentions Légales</a> - 2025 Job* - À propos
</div>

<script src="/jobs/dev/assets/script.js"></script>
</body>
</html>
