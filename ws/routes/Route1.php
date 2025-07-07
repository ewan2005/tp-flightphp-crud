<?php
require_once __DIR__ . '/../controllers/Controlleur1.php';

Flight::route('POST /addFond', ['Controlleur1', 'create']);
Flight::route('POST /updateFond/@id', ['Controlleur1', 'update']);
Flight::route('POST /addHistorique', ['Controlleur1', 'createH']);
Flight::route('GET /fond', ['Controlleur1', 'getAll']);
Flight::route('GET /clients', ['Controlleur1', 'getClients']);
