<?php
require 'vendor/autoload.php';
require 'db.php';
require 'routes/etudiant_routes.php';
require 'routes/pret_routes.php';
require 'routes/login_routes.php';
require 'routes/typePret_routes.php';
require 'routes/Route1.php';
require 'routes/client_routes.php';
require 'routes/agent_routes.php';
require 'routes/client_routes.php';
require 'routes/interet_routes.php';
require 'routes/solde_mensuel_routes.php';

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

Flight::start();