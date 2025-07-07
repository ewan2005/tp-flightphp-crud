<?php
require_once __DIR__ . '/../db.php';
class Echeancier {
    /**
     * Calcule et retourne un échéancier (tableau d'échéances) sans insertion en base.
     * @param float $montant
     * @param float $taux_annuel
     * @param int $duree
     * @param string $date_debut (format Y-m-d)
     * @return array
     */
    public static function generer($montant, $taux_annuel, $duree, $date_debut) {
        $echeances = [];
        $taux_mensuel = ($taux_annuel / 100) / 12;
        $mensualite = ($montant * $taux_mensuel) / (1 - pow(1 + $taux_mensuel, -$duree));
        for ($i = 1; $i <= $duree; $i++) {
            $date_echeance = date('Y-m-d', strtotime("$date_debut +$i month"));
            $echeances[] = [
                'numero' => $i,
                'date' => $date_echeance,
                'montant' => round($mensualite, 2)
            ];
        }
        return $echeances;
    }
}
