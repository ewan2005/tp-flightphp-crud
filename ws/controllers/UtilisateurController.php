<?php
require_once __DIR__ . '/../models/Utilisateur.php';

class UtilisateurController {
    public static function login($email, $mot_de_passe) {
        $user = Utilisateur::findByEmail($email);
        if ($user && $mot_de_passe == $user['mot_de_passe']) { // à remplacer par password_verify si hashé
            $_SESSION['user'] = [
                'id_utilisateur' => $user['id_utilisateur'],
                'nom' => $user['nom'],
                'email' => $user['email'],
                'role' => $user['role'],
                'id_etablissement' => $user['id_etablissement']
            ];
            return ['success' => true, 'user' => $_SESSION['user']];
        }
        return ['success' => false, 'message' => 'Identifiants invalides'];
    }
}