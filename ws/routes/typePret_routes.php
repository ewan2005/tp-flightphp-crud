<?php 
require_once __DIR__ . '/../controllers/typePretController.php';

// Flight::route('GET /typePret',function(){
//     Flight::render('typePret');
// });

Flight::route('GET /typePret',['typePretController','getAll']);
Flight::route('GET /typePret/@id/taux', ['typePretController','getTaux']);
Flight::route('POST /typePret', function() {
    try {
        session_start();
        if (!isset($_SESSION['user'])) {
            Flight::json(['success' => false, 'message' => 'Non authentifié'], 401);
            return;
        }
        $data = Flight::request()->data;
        $id_etablissement = $_SESSION['user']['id_etablissement'];
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO ef_type_pret (nom, taux_annuel, duree_max, montant_min, montant_max, id_etablissement) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data->nom,
            $data->taux_annuel,
            $data->duree_max,
            $data->montant_min,
            $data->montant_max,
            $id_etablissement
        ]);
        Flight::json(['success' => true]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()]);
    }
});

?>