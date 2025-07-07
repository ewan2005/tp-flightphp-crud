<?php
require_once __DIR__ . '/../controllers/UtilisateurController.php';

Flight::route('POST /login', function() {
    session_start();
    $email = Flight::request()->data->email;
    $mot_de_passe = Flight::request()->data->mot_de_passe;
    $result = UtilisateurController::login($email, $mot_de_passe);
    Flight::json($result, $result['success'] ? 200 : 401);
});

Flight::route('GET /login', function() {
    $data = ['message' => 'Bienvenue sur la page de connexion'];
    Flight::render('login', $data);
});

?>