<?php
require 'vendor/autoload.php';
require 'db.php';
require 'routes/etudiant_routes.php';
require 'routes/pret_routes.php';
require 'routes/login_routes.php';
require 'routes/typePret_routes.php';
require 'routes/client_routes.php';

Flight::start();