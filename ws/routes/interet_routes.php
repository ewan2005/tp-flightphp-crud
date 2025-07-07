<?php
require_once __DIR__ . '/../controllers/InteretController.php';
Flight::route('GET /interets', ['InteretController', 'getParMois']);
Flight::route('GET /prets/interets', ['InteretController', 'getPretsDetail']);
