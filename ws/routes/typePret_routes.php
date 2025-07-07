<?php 
require_once __DIR__ . '/../controllers/typePretController.php';

Flight::route('GET /typePret',function(){
    Flight::render('typePret');
});

Flight::route('POST /typePret',function(){
    $data = Flight::request()->data;
    $result = TypePretController::createTypePret($data);
    Flight::json($result, $result['success'] ? 200 : 400);
});

?>