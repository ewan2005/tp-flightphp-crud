<?php
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../helpers/Utils.php';


class ClientController {
    public static function getAll() {
        $clients = Client::getAll();
        Flight::json($clients);
    }

    // public static function getById($id) {
    //     $client = Client::getById($id);
    //     Flight::json($client);
    // }

    public static function getById($id) {
        require_once __DIR__ . '/../models/Client.php';
        $client = Client::getById($id);
        if ($client) {
            Flight::json(['success' => true, 'data' => $client]);
        } else {
            Flight::json(['success' => false, 'message' => 'Client introuvable']);
        }
    }

    public static function create() {
        $data = Flight::request()->data;
        $id = Client::create($data);
        Flight::json(['message' => 'Client ajouté', 'id' => $id]);
    }

    public static function update($id) {
        $data = Flight::request()->data;
        Client::update($id, $data);
        Flight::json(['message' => 'Client modifié']);
    }

    public static function delete($id) {
        Client::delete($id);
        Flight::json(['message' => 'Client supprimé']);
    }
}

?>