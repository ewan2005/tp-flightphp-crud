<?php
require_once __DIR__ . '/../controllers/SimulationController.php';


Flight::route('GET /simulation', ['PretController', 'getAll']);
Flight::route('GET /prets/@id', ['PretController', 'getById']);

Flight::route('POST /simulation', ['SimulationController', 'create']);

// // Route pour créer une simulation de prêt
// Flight::route('POST /simulation', function(){
//     $data = json_decode(Flight::request()->getBody());
//     SimulationController::create($data);
// });

