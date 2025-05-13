<?php


// Définir le rôle de l'utilisateur (ex: admin ou étudiant)
session_status() === PHP_SESSION_NONE && session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'etudiant'; // Par défaut, rôle 'etudiant'
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

<div class="Title-box">Créer un stage</div>

<div class="space2-5"></div>

<div class="profil_container">
  <div class="form-group">
    <label>Entreprise :</label>
    <input type="text">
  </div>
  <div class="form-group">
    <label>Départment :</label>
    <input type="text">
  </div>
  <div class="form-group">
    <label>Technologie :</label>
    <input type="text">
  </div>
  <div class="form-group">
    <label>Profil recherché :</label>
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
  <div class="form-group">
  <label>Présentation du stage :</label>
  <input type="text">
</div>
</div>

<a class="container" href="/jobs/dev/Pilote/Creer_offre.php">
  <h2> Créer l'offre </h2>
</a>

<div class="space5"></div>

<div class="footer">
  <a href="/jobs/dev/General/Mentions_Legales.php">Mentions Légales</a> - 2025 Job* - À propos
</div>

<script src="/jobs/dev/assets/script.js"></script>

</body>