<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est déjà connecté via session
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Si pas de session active, vérifier le cookie
    if (isset($_COOKIE['remember_me'])) {
        list($user_id, $token) = explode(':', $_COOKIE['remember_me']);

        require_once __DIR__ . '/src/models/AuthentificationModel.php';
        $db = new PDO('mysql:host=localhost;port=3307;dbname=projet;charset=utf8', 'root', '');
        $authModel = new App\models\AuthentificationModel($db);

        // Vérifier si le token est valide
        if ($authModel->verifyRememberToken($user_id, $token)) {
            // Récupérer les informations utilisateur
            $user = $authModel->getUserById($user_id);

            if ($user) {
                // Recréer la session
                session_regenerate_id(true);
                $_SESSION['logged_in'] = true;
                $_SESSION['role_id'] = $user['Id_Role'];
                $_SESSION['user_id'] = $user['Id_Utilisateur'];
                $_SESSION['email'] = $user['Email'];

                // Optionnel : renouveler le cookie pour prolonger la session
                setcookie(
                    'remember_me',
                    $user_id . ':' . $token,
                    time() + (30 * 24 * 60 * 60),
                    '/',
                    '',
                    true,
                    true
                );
            }
        }
    }
}
?>
