<?php
require_once __DIR__ . '/../models/CompSimul.php';
require_once __DIR__ . '/../helpers/Utils.php';
require_once __DIR__ . '/../db.php';


class CopSimController 
{
    public static function getAllSim()
    {
        $model = CompSimul::getAllSim();
        if (!$model || count($model) === 0) {
            Flight::json(["error" => "Aucune simulation trouvée."], 404);
        } else {
            Flight::json($model);
        }
    }
}
