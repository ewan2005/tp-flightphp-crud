<?php
require_once __DIR__ . '/../controllers/UtilisateurController.php';

Flight::route('GET /agents', ['UtilisateurController', 'getAllAgents']);
