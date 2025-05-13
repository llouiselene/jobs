<?php


// Définir le rôle de l'utilisateur (ex: admin ou étudiant)
session_status() === PHP_SESSION_NONE && session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'etudiant'; // Par défaut, rôle 'etudiant'
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Job*</title>
    <link href="/jobs/dev/assets/style.css" rel="stylesheet">
    <link crossorigin="anonymous" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
          integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
          referrerpolicy="no-referrer" rel="stylesheet"/>
</head>
<body>
<?php include '../General/menu.php'; ?>


<hr class="hr-large">
<div class="Title-box">Création d'une entreprise</div>
<div class="profil_container">
    <div class="form-group">
        <label>Nom :</label>
        <input type="text">
    </div>
    <div class="form-group">
        <label>Site web :</label>
        <input type="text">
    </div>
    <div class="form-group">
        <label>Coordonées :</label>
        <input type="text">
    </div>
    <div class="form-group">
        <label>Domaine d'activité :</label>
        <input type="text">
    </div>
    <div class="form-group">
        <label>Nombre d'employés :</label>
        <input type="text">
    </div>
    <div class="form-group">
        <label>Date de création :</label>
        <input id="dob" name="date_naissance" type="date">
    </div>
    <div class="form-group">
        <label>Responsable principal :</label>
        <input type="text">
    </div>
</div>

<button class="bouton-fixe">Créer votre entreprise</button>


<div class="footer_profil">
    <a href="/jobs/dev/General/Mentions_Legales.php">Mentions Légales</a> - 2025 Job* - À propos
</div>

<script src="/jobs/dev/assets/script.js"></script>

</body>
</html>
