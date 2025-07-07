<?php 
require_once __DIR__ . '/../controllers/typePretController.php';

Flight::route('GET /typePret',function(){
    Flight::render('typePret');
});

?>