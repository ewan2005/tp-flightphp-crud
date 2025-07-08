<?php
require_once __DIR__ . '/../controllers/CopSimController.php';

Flight::route('GET /allSimulation', ['CopSimController', 'getAllSim']);