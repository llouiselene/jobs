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

<?php include __DIR__ . '/menu.php'; ?>
<hr class="hr-large">
<div class="Title-box">Profil</div>
<div class="profil_container">
    <form id="passwordForm">
        <div class="form-group">
            <label>Nom :</label>
            <input id="nom" type="text">
        </div>
        <div class="form-group">
            <label>Prénom :</label>
            <input id="prenom" type="text">
        </div>
        <div class="form-group">
            <label>Genre :</label>
            <select id="genre" name="genre">
                <option value="homme">Homme</option>
                <option value="femme">Femme</option>
                <option value="autre">Autre</option>
            </select>
        </div>
        <div class="form-group">
            <label>Adresse :</label>
            <input id="adresse" type="text">
        </div>
        <div class="form-group">
            <label>Email :</label>
            <input id="email" type="text">
        </div>
        <div class="form-group">
            <label>Téléphone :</label>
            <input id="telephone" type="text">
        </div>
        <div class="form-group">
            <label>Date de naissance :</label>
            <input id="dob" name="date_naissance" type="date">
        </div>
        <div class="form-group">
            <label>Mot de passe :</label>
            <input id="password" type="password">
        </div>
        <div class="form-group">
            <label>Confirmer le mot de passe :</label>
            <input id="confirmPassword" type="password">
        </div>
        <p id="message"></p>
        <?php if ($role === 'admin') { ?>
            <div class="form-group">
                <label>Type de compte :</label>
                <select id="typeCompte" name="typeCompte">
                    <option value="etudiant">Étudiant</option>
                    <option value="pilote">Pilote</option>
                </select>
            </div>

            <div class="space5"></div>

            <div class="form-group">
                <label>Créer une nouvelle entreprise :</label>
                <a href="/jobs/dev/Pilote/Entreprise_Creation.php">
                    <input class="button" type="button" value="Créer">
                </a>
            </div>

            <button class="bouton-fixe-gauche">Supprimer le compte</button>


        <?php }?>
        <button class="bouton-fixe">Enregistrer les modifications</button>
    </form>
</div>




<div class="footer">
    <a href="/jobs/dev/General/Mentions_Legales.php">Mentions Légales</a> - 2025 Job* - À propos
</div>

<script src="/jobs/dev/assets/script.js"></script>
</body>
</html>
