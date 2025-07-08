<?php
require_once __DIR__ . '/../controllers/SoldeMensuelController.php';
Flight::route('GET /solde-mensuel', ['SoldeMensuelController', 'getSoldeMensuel']);
