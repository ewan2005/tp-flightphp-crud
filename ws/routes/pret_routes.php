<?php
require_once __DIR__ . '/../controllers/PretController.php';
require_once __DIR__ . '/../controllers/PretPdfController.php';

Flight::route('GET /prets', ['PretController', 'getAll']);
Flight::route('GET /prets/@id', ['PretController', 'getById']);
Flight::route('POST /prets', ['PretController', 'create']);
Flight::route('PUT /prets/@id', ['PretController', 'update']);
Flight::route('DELETE /prets/@id', ['PretController', 'delete']);
Flight::route('PUT /prets/@id/valider', ['PretController', 'valider']);
Flight::route('PUT /prets/@id/rejeter', ['PretController', 'rejeter']);
Flight::route('GET /prets/@id/pdf', ['PretPdfController', 'generer']);
