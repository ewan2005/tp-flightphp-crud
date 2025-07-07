<?php
require_once __DIR__ . '/../db.php';
class Echeancier {
    public static function generer($id_pret, $montant, $taux_annuel, $duree, $date_debut) {
        $db = getDB();
        $mensualite = ($montant * ($taux_annuel/100) / 12) / (1 - pow(1 + ($taux_annuel/100)/12, -$duree));
        for ($i = 1; $i <= $duree; $i++) {
            $date_echeance = date('Y-m-d', strtotime("$date_debut +$i month"));
            $stmt = $db->prepare("INSERT INTO ef_echeancier (id_pret, numero_echeance, date_echeance, montant) VALUES (?, ?, ?, ?)");
            $stmt->execute([$id_pret, $i, $date_echeance, $mensualite]);
        }
    }
}
