<?php

namespace App\models;

use PDO;

class AuthentificationModel
{
    private $pdo;
    private string $table = 'utilisateur';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function authentifier($email, $mdp) {
        $user = $this->getUserByEmail($email);

        if (!$user || !password_verify($mdp, $user['Mot_de_Passe'])) {
            return false;
        }

        return $user;
    }

    private function getUserByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE Email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function storeRememberToken($user_id, $token) {
        $expiry = date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60)); // 30 jours
        $hash = password_hash($token, PASSWORD_DEFAULT);

        // Supprimer les anciens tokens pour cet utilisateur
        $sql = "DELETE FROM user_tokens WHERE user_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id]);

        // Insérer le nouveau token
        $sql = "INSERT INTO user_tokens (user_id, token, expiry) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id, $hash, $expiry]);

        return true;
    }

    public function verifyRememberToken($user_id, $token) {
        $sql = "SELECT * FROM user_tokens WHERE user_id = ? AND expiry > NOW()";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && password_verify($token, $result['token'])) {
            return true;
        }

        return false;
    }

    public function getUserById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE Id_Utilisateur = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteRememberToken($user_id) {
        $sql = "DELETE FROM user_tokens WHERE user_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id]);
        return true;
    }


}
