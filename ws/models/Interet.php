<?php
require_once __DIR__ . '/../db.php';
class Interet {

    public static function getInteretsParMois($mois_debut, $annee_debut, $mois_fin, $annee_fin) {
        $db = getDB();
        $date_debut = sprintf('%04d-%02d-01', $annee_debut, $mois_debut);
        $date_fin = date('Y-m-t', strtotime(sprintf('%04d-%02d-01', $annee_fin, $mois_fin)));
        $sql = "SELECT p.id_pret, p.id_client, p.montant, p.duree, p.date_demande, tp.taux_annuel, 
                       (p.montant * (tp.taux_annuel/100) * (p.duree/12)) AS interet_gagne
                FROM ef_pret p
                JOIN ef_type_pret tp ON p.id_type_pret = tp.id_type_pret
                WHERE p.id_statut = 2 AND p.date_demande BETWEEN ? AND ?
                ORDER BY p.date_demande";
        $stmt = $db->prepare($sql);
        $stmt->execute([$date_debut, $date_fin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getInteretsParMoisAgg($mois_debut, $annee_debut, $mois_fin, $annee_fin) {
        $db = getDB();
        $start = new DateTime(sprintf('%04d-%02d-01', $annee_debut, $mois_debut));
        $end = new DateTime(sprintf('%04d-%02d-01', $annee_fin, $mois_fin));
        $end->modify('last day of this month');
        $result = [];
        $sql = "SELECT p.id_pret, p.montant, p.duree, p.date_demande, tp.taux_annuel
                FROM ef_pret p
                JOIN ef_type_pret tp ON p.id_type_pret = tp.id_type_pret
                WHERE p.id_statut = 2
                  AND p.date_demande <= ?
                  AND DATE_ADD(p.date_demande, INTERVAL p.duree MONTH) > ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$end->format('Y-m-d'), $start->format('Y-m-d')]);
        $prets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $mois_courant = clone $start;
        $mois_periode = 1;
        while ($mois_courant <= $end) {
            $annee = (int)$mois_courant->format('Y');
            $mois = (int)$mois_courant->format('n');
            $interet_gagne = 0;
            foreach ($prets as $pret) {
                $date_debut_pret = new DateTime($pret['date_demande']);
                $date_fin_pret = (clone $date_debut_pret)->modify('+' . $pret['duree'] . ' months');
                // Correction : comparer sur l'année et le mois
                $debut_annee = (int)$date_debut_pret->format('Y');
                $debut_mois = (int)$date_debut_pret->format('n');
                $fin_annee = (int)$date_fin_pret->format('Y');
                $fin_mois = (int)$date_fin_pret->format('n');
                $mois_courant_annee = (int)$mois_courant->format('Y');
                $mois_courant_mois = (int)$mois_courant->format('n');
                $debut = $debut_annee * 12 + $debut_mois;
                $fin = $fin_annee * 12 + $fin_mois;
                $courant = $mois_courant_annee * 12 + $mois_courant_mois;
                if ($courant >= $debut && $courant < $fin) {
                    $interet_mensuel = $pret['montant'] * ($pret['taux_annuel']/100) / 12;
                    $interet_gagne += $interet_mensuel;
                }
            }
            $result[] = [
                'mois_periode' => $mois_periode,
                'mois' => $mois,
                'annee' => $annee,
                'interet_gagne' => $interet_gagne
            ];
            $mois_periode++;
            $mois_courant->modify('+1 month');
        }
        return $result;
    }
}
