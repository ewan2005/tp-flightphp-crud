<?php
require_once __DIR__ . '/../models/Model1.php';
require_once __DIR__ . '/../helpers/Utils.php';

class Controlleur1 
{
    public static function getById($id) {
        $model = Model1::getById($id);
        Flight::json($model);
    }

    public static function create() 
    {
        $data = Flight::request()->data;
        $id = Model1::create($data);
        Flight::json(['message' => 'Fond ajouté', 'id' => $id]);
    }

    public static function getAll() {
        $model = Model1::getAll();
        Flight::json($model);
    }

    public static function getTaux($id){
        $taux = Model1::getTaux($id);
        if ($taux) {
            Flight::json(['taux_annuel' => $taux]);
        } else {
            Flight::halt(404, json_encode(['error' => 'Taux non trouvé pour cet ID']));
        }
    }

    public static function update($id) {
        $data = Flight::request()->data;
        $etab = Model1::getById($id);
    
        if (!$etab || !isset($etab['id_etablissement'])) {
            Flight::halt(404, json_encode(['error' => 'Etablissement introuvable pour cet utilisateur']));
        }
    
        $idEtab = $etab['id_etablissement'];
        Model1::update($idEtab, $data);
    
        Flight::json(['message' => 'Fonds mis à jour', 'id' => $idEtab]);
    }
    public static function createH() {
        $data = Flight::request()->data;
        $id = Model1::createH($data);
        Flight::json(['message' => 'Fond cret']);
    }
    public static function getClients()
    {
        $model = Model1::getAllClient();
        Flight::json($model);
    }
}
