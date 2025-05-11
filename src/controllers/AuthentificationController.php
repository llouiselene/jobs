<?php

namespace App\controllers;

use App\models\AuthentificationModel;

class AuthentificationController
{
    private $authentifierModele;
    private $twig;

    public function __construct($pdo, $twig) {
        $this->authentifierModele = new AuthentificationModel($pdo);
        $this->twig = $twig;
    }

    public function traiterFormulaire() {
        session_status() === PHP_SESSION_NONE && session_start();

        $email = $_POST['email'] ?? null;
        $mdp = $_POST['password'] ?? null;
        $remember = isset($_POST['remember']) ? true : false; // Nouvelle ligne pour la case à cocher

        if (!$email || !$mdp) {
            $_SESSION['error_message'] = "Veuillez remplir tous les champs.";
            header('Location: ?uri=connexion');
            exit;
        }

        $user = $this->authentifierModele->authentifier($email, $mdp);

        if ($user) {
            session_regenerate_id(true);
            $_SESSION['logged_in'] = true;
            $_SESSION['role_id'] = $user['Id_Role'];
            $_SESSION['user_id'] = $user['Id_Utilisateur'];
            $_SESSION['email'] = $user['Email'];

            // Gestion du cookie "Se souvenir de moi"
            if ($remember) {
                // Générer un token unique
                $token = bin2hex(random_bytes(32));
                $user_id = $user['Id_Utilisateur'];

                // Stocker le token en base de données
                $this->authentifierModele->storeRememberToken($user_id, $token);

                // Définir le cookie (1 mois de validité)
                setcookie(
                    'remember_me',
                    $user_id . ':' . $token,
                    time() + (30 * 24 * 60 * 60), // 30 jours
                    '/',
                    '',
                    true,    // Sécurisé (HTTPS uniquement)
                    true     // HttpOnly (inaccessible via JavaScript)
                );
            }

            header('Location: ?uri=accueil');
            exit;
        } else {
            $_SESSION['error_message'] = "Identifiants incorrects.";
            header('Location: ?uri=connexion');
            exit;
        }
    }



    public function afficherAuthentifier(string $pageweb) {
        $errorMessage = $_SESSION['error_message'] ?? '';
        unset($_SESSION['error_message']);

        $role_id = $_SESSION['role_id'] ?? 4;

        echo $this->twig->render("$pageweb.twig", [
            'error_message' => $errorMessage,
            'menu_items' => menu($role_id)
        ]);
    }

    public function deconnecter() {
        session_start();

        // Supprimer le cookie remember_me
        if (isset($_COOKIE['remember_me'])) {
            list($user_id, $token) = explode(':', $_COOKIE['remember_me']);

            // Supprimer le token de la base de données
            $this->authentifierModele->deleteRememberToken($user_id);

            // Supprimer le cookie
            setcookie('remember_me', '', time() - 3600, '/');
        }

        // Détruire la session
        session_unset();
        session_destroy();

        header('Location: ?uri=connexion');
        exit;
    }

}

