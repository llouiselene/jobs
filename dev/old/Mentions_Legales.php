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
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Job*</title>
    <link rel="stylesheet" href="/jobs/dev/assets/style.css">
    <link crossorigin="anonymous" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
          integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
          referrerpolicy="no-referrer" rel="stylesheet"/>
</head>
<body>
<?php include __DIR__ . '/menu.php'; ?>

<hr class="hr-large">

<div class="space2-5"></div>

<div class="mentions">
    <h1>Mentions Légales</h1>
    <h2>1. Informations Générales</h2>
    <p><strong>Nom du site :</strong> Job* </p>
    <p><strong>Responsable de publication :</strong> Groupe n°4</p>
    <p><strong>Adresse du siège social :</strong> 2 Allée des Foulons 67380 Lingolsheim</p>
    <p><strong>Email de contact :</strong> nicolas.schnell@viacesi.fr, nicolas.kuteifan@viacesi.fr,
        guillaume.haeberle@viacesi.fr, louis.elene@viacesi.fr</p>
    <p><strong>Numéro de téléphone :</strong> +33 07 83 71 94 23</p>
    <p><strong>SIRET :</strong> 775 722 572 01109</p>
    <p><strong>Hébergeur :</strong> CESI</p>

    <h2>2. Propriété Intellectuelle</h2>
    <p>Tout le contenu du site (Job*) est la propriété exclusive de l’éditeur du site, sauf mention contraire.</p>

    <h2>3. Données Personnelles</h2>
    <p>Les informations collectées sont utilisées uniquement pour la recherche de stage. Conformément au RGPD, vous
        disposez d’un droit d’accès, de rectification et de suppression en nous contactant à :
        contact.job@viacesi.fr.</p>

    <h2>4. Cookies</h2>
    <p>Ce site peut utiliser des cookies pour améliorer l’expérience utilisateur. Vous pouvez les désactiver dans les
        paramètres de votre navigateur.</p>

    <h2>5. Responsabilités</h2>
    <p>L’éditeur ne saurait être tenu responsable des erreurs ou omissions dans les informations diffusées.</p>

    <h2>6. Droit Applicable</h2>
    <p>Ces mentions légales sont régies par le droit français. En cas de litige, seuls les tribunaux français seront
        compétents.</p>
</div>
<div class="space2-5"></div>

<div class="footer">
    <a href="/jobs/dev/General/Mentions_Legales.php">Mentions Légales</a> - 2025 Job* - À propos
</div>

<script src="/jobs/dev/assets/script.js"></script>

</body>