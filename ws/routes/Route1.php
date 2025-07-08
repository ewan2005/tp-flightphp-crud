<?php
require_once __DIR__ . '/../controllers/Controlleur1.php';

Flight::route('POST /addFond', ['Controlleur1', 'create']);
Flight::route('POST /updateFond/@id', ['Controlleur1', 'update']);
Flight::route('POST /addHistorique', ['Controlleur1', 'createH']);

Flight::route('GET /clients', ['Controlleur1', 'getClients']);
Flight::route('GET /Pret/@id', ['Controlleur1', 'getPret']);
Flight::route('GET /echeance/@id', ['Controlleur1', 'getEcheance']);

Flight::route('GET /Annuite/@idPret',['Controlleur1','annuite']);
Flight::route('POST /traitement_annuite',['Controlleur1','traitement_annuite']);
Flight::route('GET /fond', ['Controlleur1', 'getAll']);
// Flight::route('GET /assurance/@id', ['Controlleur1', 'getAssuranceById']);