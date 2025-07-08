<?php
require_once __DIR__ . '/../models/Pret.php';

class SimulationController {
    public static function create() {
        $data = Flight::request()->data;
        $id = Pret::createSimulation($data);
        if ($id) {
            Flight::json(['success' => true, 'id_simulation' => $id]);
        } else {
            Flight::json(['success' => false, 'error' => 'Erreur lors de la sauvegarde de la simulation'], 500);
        }
    }
}
