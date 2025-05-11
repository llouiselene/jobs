<?php
// Définir le rôle de l'utilisateur (ex: admin ou étudiant)
session_status() === PHP_SESSION_NONE && session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'etudiant'; // Par défaut, rôle 'etudiant'


$entreprises = [
['nom' => 'TechCorp', 'secteur' => 'Technologie', 'ville' => 'Paris'],
['nom' => 'FinSoft', 'secteur' => 'Finance', 'ville' => 'Londres'],
['nom' => 'AutoMeca', 'secteur' => 'Automobile', 'ville' => 'Berlin'],
['nom' => 'MediLife', 'secteur' => 'Santé', 'ville' => 'Madrid'],
['nom' => 'GreenEnergy', 'secteur' => 'Énergie', 'ville' => 'Amsterdam'],
['nom' => 'Foodies', 'secteur' => 'Agroalimentaire', 'ville' => 'Rome'],
['nom' => 'SkyNet', 'secteur' => 'Technologie', 'ville' => 'New York'],
['nom' => 'EcoBuild', 'secteur' => 'Construction', 'ville' => 'Toronto'],
['nom' => 'RetailPro', 'secteur' => 'Commerce', 'ville' => 'Paris'],
['nom' => 'BioMed', 'secteur' => 'Santé', 'ville' => 'Stockholm'],
['nom' => 'FinSoft', 'secteur' => 'Finance', 'ville' => 'Londres'],
['nom' => 'AutoMeca', 'secteur' => 'Automobile', 'ville' => 'Berlin'],
['nom' => 'MediLife', 'secteur' => 'Santé', 'ville' => 'Madrid'],
['nom' => 'GreenEnergy', 'secteur' => 'Énergie', 'ville' => 'Amsterdam'],
['nom' => 'Foodies', 'secteur' => 'Agroalimentaire', 'ville' => 'Rome'],
['nom' => 'SkyNet', 'secteur' => 'Technologie', 'ville' => 'New York'],
['nom' => 'EcoBuild', 'secteur' => 'Construction', 'ville' => 'Toronto'],
['nom' => 'RetailPro', 'secteur' => 'Commerce', 'ville' => 'Paris'],
['nom' => 'BioMed', 'secteur' => 'Santé', 'ville' => 'Stockholm'],
['nom' => 'TechCorp', 'secteur' => 'Technologie', 'ville' => 'Paris'],
['nom' => 'AutoMeca', 'secteur' => 'Automobile', 'ville' => 'Berlin'],
['nom' => 'MediLife', 'secteur' => 'Santé', 'ville' => 'Madrid'],
['nom' => 'GreenEnergy', 'secteur' => 'Énergie', 'ville' => 'Amsterdam'],
['nom' => 'Foodies', 'secteur' => 'Agroalimentaire', 'ville' => 'Rome'],
['nom' => 'SkyNet', 'secteur' => 'Technologie', 'ville' => 'New York'],
['nom' => 'EcoBuild', 'secteur' => 'Construction', 'ville' => 'Toronto'],
['nom' => 'RetailPro', 'secteur' => 'Commerce', 'ville' => 'Paris'],
['nom' => 'BioMed', 'secteur' => 'Santé', 'ville' => 'Stockholm'],
];

$parPage = 9;

$totalPages = ceil(count($entreprises) / $parPage);

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1 || $page > $totalPages) {
    $page = 1; //on revient à la première page si invalide
}


$debut = ($page - 1) * $parPage;
$entreprisesPage = array_slice($entreprises, $debut, $parPage);

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

<div class="title-and-search">
    <div class="Title-box">Entreprise</div>
    <div class="search-container">
        <div class="search-icon-container" id="searchIcon">
            <i class="fas fa-search" style="color: white;"></i>
        </div>
    </div>
</div>

<input type="text" id="searchInput" class="search-input" placeholder="Rechercher...">

<div class="space2-5"></div>

<div class="Title">Liste des Entreprises :</div>

<div class="space2-5"></div>

<?php if ($role == 'admin') { ?>
    <div class="form-group-entreprise">
        <a class="container_entreprise" href="/jobs/dev/General/entreprise-detail.php">
            <h2>Entreprise (Admin)</h2>
        </a>
    </div>
<?php } else { ?>
    <?php foreach ($entreprisesPage as $entreprise) : ?>
        <div class="form-group-entreprise">
            <a class="container_entreprise" href="Entreprise_Detail_Etudiant.html">
                <h2><?= htmlspecialchars($entreprise['nom']) ?></h2>
            </a>

            <a class="container_entreprise" href="Entreprise_Detail_Etudiant.html">
                <h2><?= htmlspecialchars($entreprise['nom']) ?></h2>
            </a>

            <a class="container_entreprise" href="Entreprise_Detail_Etudiant.html">
                <h2><?= htmlspecialchars($entreprise['nom']) ?></h2>
            </a>
        </div>
    <?php endforeach; ?>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>">Précédent</a>
        <?php endif; ?>

        Page <?= $page ?> sur <?= $totalPages ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>">Suivant</a>
        <?php endif; ?>
    </div>
<?php } ?>

<div class="footer">
    <a href="/jobs/dev/General/Mentions_Legales.php">Mentions Légales</a> - 2025 Job* - À propos
</div>

<script src="/jobs/dev/assets/script.js"></script>
</body>
</html>
