<?php
session_start();

require 'vendor/autoload.php';
require 'db.php';


Flight::route('POST /login', function() {
    $email = Flight::request()->data->email;
    $mot_de_passe = Flight::request()->data->mot_de_passe;
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM ef_utilisateur WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $mot_de_passe == $user['mot_de_passe']) { // comparaison simple
        $_SESSION['user'] = [
            'id_utilisateur' => $user['id_utilisateur'],
            'nom' => $user['nom'],
            'email' => $user['email'],
            'role' => $user['role'],
            'id_etablissement' => $user['id_etablissement']
        ];
        echo json_encode(['success' => true, 'user' => $_SESSION['user']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Identifiants invalides']);
    }
});

Flight::route('GET /login', function() {
    // Affiche le contenu du fichier login.php
    include_once __DIR__ . '/../login.php';
});

Flight::route('GET /typePret', function() {
    // Affiche le contenu du fichier login.php
    include_once __DIR__ . '/../typePret.php';
});

Flight::route('POST /typePret', function() {
    if (!isset($_SESSION['user'])) {
        Flight::json(['success' => false, 'message' => 'Non authentifié'], 401);
        return;
    }
    $data = Flight::request()->data;
    $db = getDB();
    $id_etablissement = $_SESSION['user']['id_etablissement'];
    $stmt = $db->prepare("INSERT INTO ef_type_pret (nom, taux_annuel, duree_max, montant_min, montant_max, id_etablissement) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $data->nom,
        $data->taux_annuel,
        $data->duree_max,
        $data->montant_min,
        $data->montant_max,
        $id_etablissement
    ]);
    Flight::json(['success' => true, 'message' => 'Type de prêt créé']);
});

// Flight::route('GET /etudiants', function() {
//     $db = getDB();
//     $stmt = $db->query("SELECT * FROM etudiant");
//     Flight::json($stmt->fetchAll(PDO::FETCH_ASSOC));
// });

// Flight::route('GET /etudiants/@id', function($id) {
//     $db = getDB();
//     $stmt = $db->prepare("SELECT * FROM etudiant WHERE id = ?");
//     $stmt->execute([$id]);
//     Flight::json($stmt->fetch(PDO::FETCH_ASSOC));
// });

// Flight::route('POST /etudiants', function() {
//     $data = Flight::request()->data;
//     $db = getDB();
//     $stmt = $db->prepare("INSERT INTO etudiant (nom, prenom, email, age) VALUES (?, ?, ?, ?)");
//     $stmt->execute([$data->nom, $data->prenom, $data->email, $data->age]);
//     Flight::json(['message' => 'Étudiant ajouté', 'id' => $db->lastInsertId()]);
// });

// Flight::route('PUT /etudiants/@id', function($id) {
//     $data = Flight::request()->data;
//     $db = getDB();
//     $stmt = $db->prepare("UPDATE etudiant SET nom = ?, prenom = ?, email = ?, age = ? WHERE id = ?");
//     $stmt->execute([$data->nom, $data->prenom, $data->email, $data->age, $id]);
//     Flight::json(['message' => 'Étudiant modifié']);
// });

// Flight::route('DELETE /etudiants/@id', function($id) {
//     $db = getDB();
//     $stmt = $db->prepare("DELETE FROM etudiant WHERE id = ?");
//     $stmt->execute([$id]);
//     Flight::json(['message' => 'Étudiant supprimé']);
// });



Flight::start();