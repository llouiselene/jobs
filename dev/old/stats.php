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
    <link rel="stylesheet" href="/dev/assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inria+Serif&family=Rampart+One&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.7.0/gsap.min.js"></script>
</head>
<body>
<?php include __DIR__ . '/menu.php'; ?>

    <hr class="hr-large">
    <h1 class="Title-box">statistiques</h1>

    <div class="space5"></div>

    <div class="stat-box">
        <h2>Combien d'offres publiées ?</h2>
        <div class="conteur-nb-offre">
            <h2>4</h2>
        </div>
    </div>

    <div class="space2-5"></div>

    <div class="flex-container">
        <div class="stat-box">
            <h3>Combien d'offres publiées par jour</h3>
            <div class="graph-container">
                <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MDAiIGhlaWdodD0iMjUwIj48cmVjdCB4PSIwIiB5PSIwIiB3aWR0aD0iNDAwIiBoZWlnaHQ9IjI1MCIgZmlsbD0iI2Y1ZjVmNSIvPjxnIHRyYW5zZm9ybT0idHJhbnNsYXRlKDQwLCAyMCkiPjxyZWN0IHg9IjEwIiB5PSIxODAiIGhlaWdodD0iNTAiIHdpZHRoPSIxMCIgZmlsbD0iIzQ2ODJCNCIvPjxyZWN0IHg9IjMwIiB5PSIxNTAiIGhlaWdodD0iODAiIHdpZHRoPSIxMCIgZmlsbD0iIzQ2ODJCNCIvPjxyZWN0IHg9IjUwIiB5PSIxODAiIGhlaWdodD0iNTAiIHdpZHRoPSIxMCIgZmlsbD0iIzQ2ODJCNCIvPjxyZWN0IHg9IjkwIiB5PSIxNTAiIGhlaWdodD0iODAiIHdpZHRoPSIxMCIgZmlsbD0iIzQ2ODJCNCIvPjxyZWN0IHg9IjExMCIgeT0iMTYwIiBoZWlnaHQ9IjcwIiB3aWR0aD0iMTAiIGZpbGw9IiM0NjgyQjQiLz48cmVjdCB4PSIxOTAiIHk9IjEwMCIgaGVpZ2h0PSIxMzAiIHdpZHRoPSIxMCIgZmlsbD0iIzQ2ODJCNCIvPjxyZWN0IHg9IjIxMCIgeT0iODAiIGhlaWdodD0iMTUwIiB3aWR0aD0iMTAiIGZpbGw9IiM0NjgyQjQiLz48cmVjdCB4PSIyMzAiIHk9IjMwIiBoZWlnaHQ9IjIwMCIgd2lkdGg9IjEwIiBmaWxsPSIjNDY4MkI0Ii8+PHJlY3QgeD0iMjUwIiB5PSIxMzAiIGhlaWdodD0iMTAwIiB3aWR0aD0iMTAiIGZpbGw9IiM0NjgyQjQiLz48cmVjdCB4PSIyNzAiIHk9IjEwMCIgaGVpZ2h0PSIxMzAiIHdpZHRoPSIxMCIgZmlsbD0iIzQ2ODJCNCIvPjxsaW5lIHgxPSIwIiB5MT0iMjMwIiB4Mj0iMzIwIiB5Mj0iMjMwIiBzdHJva2U9IiMzMzMiIHN0cm9rZS13aWR0aD0iMiIvPjxsaW5lIHgxPSIwIiB5MT0iMjMwIiB4Mj0iMCIgeTI9IjAiIHN0cm9rZT0iIzMzMyIgc3Ryb2tlLXdpZHRoPSIyIi8+PC9nPjwvc3ZnPg==" alt="Graphique des offres publiées par jour" />
                <div class="chart-container">
                    <canvas id="offresParJour"></canvas>
                </div>
            </div>
        </div>

        <div class="stat-box">
            <h3>Les entreprises qui recrutent le plus</h3>
            <div class="graph-container">
                <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MDAiIGhlaWdodD0iMjUwIj48cmVjdCB4PSIwIiB5PSIwIiB3aWR0aD0iNDAwIiBoZWlnaHQ9IjI1MCIgZmlsbD0iI2Y1ZjVmNSIvPjxnIHRyYW5zZm9ybT0idHJhbnNsYXRlKDQwLCAyMCkiPjxyZWN0IHg9IjEwIiB5PSIxODAiIGhlaWdodD0iNTAiIHdpZHRoPSIxMCIgZmlsbD0iIzQ2ODJCNCIvPjxyZWN0IHg9IjMwIiB5PSIxNTAiIGhlaWdodD0iODAiIHdpZHRoPSIxMCIgZmlsbD0iIzQ2ODJCNCIvPjxyZWN0IHg9IjUwIiB5PSIxODAiIGhlaWdodD0iNTAiIHdpZHRoPSIxMCIgZmlsbD0iIzQ2ODJCNCIvPjxyZWN0IHg9IjkwIiB5PSIxNTAiIGhlaWdodD0iODAiIHdpZHRoPSIxMCIgZmlsbD0iIzQ2ODJCNCIvPjxyZWN0IHg9IjExMCIgeT0iMTYwIiBoZWlnaHQ9IjcwIiB3aWR0aD0iMTAiIGZpbGw9IiM0NjgyQjQiLz48cmVjdCB4PSIxOTAiIHk9IjEwMCIgaGVpZ2h0PSIxMzAiIHdpZHRoPSIxMCIgZmlsbD0iIzQ2ODJCNCIvPjxyZWN0IHg9IjIxMCIgeT0iODAiIGhlaWdodD0iMTUwIiB3aWR0aD0iMTAiIGZpbGw9IiM0NjgyQjQiLz48cmVjdCB4PSIyMzAiIHk9IjMwIiBoZWlnaHQ9IjIwMCIgd2lkdGg9IjEwIiBmaWxsPSIjNDY4MkI0Ii8+PHJlY3QgeD0iMjUwIiB5PSIxMzAiIGhlaWdodD0iMTAwIiB3aWR0aD0iMTAiIGZpbGw9IiM0NjgyQjQiLz48cmVjdCB4PSIyNzAiIHk9IjEwMCIgaGVpZ2h0PSIxMzAiIHdpZHRoPSIxMCIgZmlsbD0iIzQ2ODJCNCIvPjxsaW5lIHgxPSIwIiB5MT0iMjMwIiB4Mj0iMzIwIiB5Mj0iMjMwIiBzdHJva2U9IiMzMzMiIHN0cm9rZS13aWR0aD0iMiIvPjxsaW5lIHgxPSIwIiB5MT0iMjMwIiB4Mj0iMCIgeTI9IjAiIHN0cm9rZT0iIzMzMyIgc3Ryb2tlLXdpZHRoPSIyIi8+PC9nPjwvc3ZnPg==" alt="Graphique des entreprises qui recrutent le plus" />
                <div class="chart-container">
                    <canvas id="entreprisesRecrutement"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="flex-container">
        <div class="stat-box">
            <h3>note moyene entreprises</h3>
            <div class="graph-container">
                <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MDAiIGhlaWdodD0iMjUwIj48cmVjdCB4PSIwIiB5PSIwIiB3aWR0aD0iNDAwIiBoZWlnaHQ9IjI1MCIgZmlsbD0iI2Y1ZjVmNSIvPjxnIHRyYW5zZm9ybT0idHJhbnNsYXRlKDQwLCAyMCkiPjxyZWN0IHg9IjEwIiB5PSIxODAiIGhlaWdodD0iNTAiIHdpZHRoPSIxMCIgZmlsbD0iIzQ2ODJCNCIvPjxyZWN0IHg9IjMwIiB5PSIxNTAiIGhlaWdodD0iODAiIHdpZHRoPSIxMCIgZmlsbD0iIzQ2ODJCNCIvPjxyZWN0IHg9IjUwIiB5PSIxODAiIGhlaWdodD0iNTAiIHdpZHRoPSIxMCIgZmlsbD0iIzQ2ODJCNCIvPjxyZWN0IHg9IjkwIiB5PSIxNTAiIGhlaWdodD0iODAiIHdpZHRoPSIxMCIgZmlsbD0iIzQ2ODJCNCIvPjxyZWN0IHg9IjExMCIgeT0iMTYwIiBoZWlnaHQ9IjcwIiB3aWR0aD0iMTAiIGZpbGw9IiM0NjgyQjQiLz48cmVjdCB4PSIxOTAiIHk9IjEwMCIgaGVpZ2h0PSIxMzAiIHdpZHRoPSIxMCIgZmlsbD0iIzQ2ODJCNCIvPjxyZWN0IHg9IjIxMCIgeT0iODAiIGhlaWdodD0iMTUwIiB3aWR0aD0iMTAiIGZpbGw9IiM0NjgyQjQiLz48cmVjdCB4PSIyMzAiIHk9IjMwIiBoZWlnaHQ9IjIwMCIgd2lkdGg9IjEwIiBmaWxsPSIjNDY4MkI0Ii8+PHJlY3QgeD0iMjUwIiB5PSIxMzAiIGhlaWdodD0iMTAwIiB3aWR0aD0iMTAiIGZpbGw9IiM0NjgyQjQiLz48cmVjdCB4PSIyNzAiIHk9IjEwMCIgaGVpZ2h0PSIxMzAiIHdpZHRoPSIxMCIgZmlsbD0iIzQ2ODJCNCIvPjxsaW5lIHgxPSIwIiB5MT0iMjMwIiB4Mj0iMzIwIiB5Mj0iMjMwIiBzdHJva2U9IiMzMzMiIHN0cm9rZS13aWR0aD0iMiIvPjxsaW5lIHgxPSIwIiB5MT0iMjMwIiB4Mj0iMCIgeTI9IjAiIHN0cm9rZT0iIzMzMyIgc3Ryb2tlLXdpZHRoPSIyIi8+PC9nPjwvc3ZnPg==" alt="Graphique des offres publiées par jour" />
                <div class="chart-container">
                    <canvas id="noteentreprise"></canvas>
                </div>
            </div>
        </div>

        <div class="stat-box">
            <h3>étudiant postulant le plus</h3>
            <div class="graph-container">
                <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MDAiIGhlaWdodD0iMjUwIj48cmVjdCB4PSIwIiB5PSIwIiB3aWR0aD0iNDAwIiBoZWlnaHQ9IjI1MCIgZmlsbD0iI2Y1ZjVmNSIvPjxnIHRyYW5zZm9ybT0idHJhbnNsYXRlKDQwLCAyMCkiPjxyZWN0IHg9IjEwIiB5PSIxODAiIGhlaWdodD0iNTAiIHdpZHRoPSIxMCIgZmlsbD0iIzQ2ODJCNCIvPjxyZWN0IHg9IjMwIiB5PSIxNTAiIGhlaWdodD0iODAiIHdpZHRoPSIxMCIgZmlsbD0iIzQ2ODJCNCIvPjxyZWN0IHg9IjUwIiB5PSIxODAiIGhlaWdodD0iNTAiIHdpZHRoPSIxMCIgZmlsbD0iIzQ2ODJCNCIvPjxyZWN0IHg9IjkwIiB5PSIxNTAiIGhlaWdodD0iODAiIHdpZHRoPSIxMCIgZmlsbD0iIzQ2ODJCNCIvPjxyZWN0IHg9IjExMCIgeT0iMTYwIiBoZWlnaHQ9IjcwIiB3aWR0aD0iMTAiIGZpbGw9IiM0NjgyQjQiLz48cmVjdCB4PSIxOTAiIHk9IjEwMCIgaGVpZ2h0PSIxMzAiIHdpZHRoPSIxMCIgZmlsbD0iIzQ2ODJCNCIvPjxyZWN0IHg9IjIxMCIgeT0iODAiIGhlaWdodD0iMTUwIiB3aWR0aD0iMTAiIGZpbGw9IiM0NjgyQjQiLz48cmVjdCB4PSIyMzAiIHk9IjMwIiBoZWlnaHQ9IjIwMCIgd2lkdGg9IjEwIiBmaWxsPSIjNDY4MkI0Ii8+PHJlY3QgeD0iMjUwIiB5PSIxMzAiIGhlaWdodD0iMTAwIiB3aWR0aD0iMTAiIGZpbGw9IiM0NjgyQjQiLz48cmVjdCB4PSIyNzAiIHk9IjEwMCIgaGVpZ2h0PSIxMzAiIHdpZHRoPSIxMCIgZmlsbD0iIzQ2ODJCNCIvPjxsaW5lIHgxPSIwIiB5MT0iMjMwIiB4Mj0iMzIwIiB5Mj0iMjMwIiBzdHJva2U9IiMzMzMiIHN0cm9rZS13aWR0aD0iMiIvPjxsaW5lIHgxPSIwIiB5MT0iMjMwIiB4Mj0iMCIgeTI9IjAiIHN0cm9rZT0iIzMzMyIgc3Ryb2tlLXdpZHRoPSIyIi8+PC9nPjwvc3ZnPg==" alt="Graphique des entreprises qui recrutent le plus" />
                <div class="chart-container">
                    <canvas id="posteetudiant"></canvas>
                </div>
            </div>
        </div>
    </div>


    <div class="footer">
        <a href="/jobs/dev/General/Mentions_Legales.html">Mentions Légales</a> - 2025 Job* - À propos
    </div>

    <script src="/dev/assets/script.js"></script>

</body>