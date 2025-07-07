<?php
require_once __DIR__ . '/../models/Interet.php';
class InteretController {
    public static function getParMois() {
        $mois_debut = intval(Flight::request()->query['mois_debut']);
        $annee_debut = intval(Flight::request()->query['annee_debut']);
        $mois_fin = intval(Flight::request()->query['mois_fin']);
        $annee_fin = intval(Flight::request()->query['annee_fin']);
        $result = Interet::getInteretsParMois($mois_debut, $annee_debut, $mois_fin, $annee_fin);
        Flight::json($result);
    }
}
