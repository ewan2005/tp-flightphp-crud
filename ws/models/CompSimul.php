<?php
require_once __DIR__ . '/../db.php';

class CompSimul {

    public static function getAllSim() {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM simulation");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
}

