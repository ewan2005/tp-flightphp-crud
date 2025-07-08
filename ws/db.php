<?php
/* $bdd = mysqli_connect('172.80.237.54', 'ETU003389', 'u803RF8V', 'db_s2_ETU003389');*/
function getDB() {
    $host = 'localhost';
    $dbname = 'etablissement';
    $username = 'root';
    $password = '';

    // $host = 'localhost';
    // $dbname = 'db_s2_ETU003389';
    // $username = 'ETU003389';
    // $password = 'u803RF8V';

    try {
        return new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    } catch (PDOException $e) {
        die(json_encode(['error' => $e->getMessage()]));
    }
}
