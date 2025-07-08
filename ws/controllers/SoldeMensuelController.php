<?php
require_once __DIR__ . '/../models/Model1.php';

class SoldeMensuelController {
    public static function getSoldeMensuel() {
        $db = getDB();
        $mois_debut = intval($_GET['mois_debut'] ?? 1);
        $annee_debut = intval($_GET['annee_debut'] ?? date('Y'));
        $mois_fin = intval($_GET['mois_fin'] ?? 12);
        $annee_fin = intval($_GET['annee_fin'] ?? date('Y'));
        $id_etablissement = 1; // à adapter si multi-établissement

        $start = new DateTime(sprintf('%04d-%02d-01', $annee_debut, $mois_debut));
        $end = new DateTime(sprintf('%04d-%02d-01', $annee_fin, $mois_fin));
        $end->modify('last day of this month');

        // Solde initial
        $stmt = $db->prepare("SELECT solde FROM ef_etablissement_financier WHERE id_etablissement = ?");
        $stmt->execute([$id_etablissement]);
        $solde_initial = floatval($stmt->fetchColumn());

        // Prêts accordés par mois
        $prets = $db->prepare("SELECT YEAR(date_demande) as annee, MONTH(date_demande) as mois, SUM(montant) as total_pret
            FROM ef_pret WHERE id_statut IN (1,2) AND id_agent > 0 AND date_demande BETWEEN ? AND ? AND id_type_pret IN (SELECT id_type_pret FROM ef_type_pret WHERE id_etablissement = ?)
            GROUP BY annee, mois");
        $prets->execute([$start->format('Y-m-d'), $end->format('Y-m-d'), $id_etablissement]);
        $prets_par_mois = [];
        foreach ($prets as $row) {
            $prets_par_mois[$row['annee'].'-'.$row['mois']] = floatval($row['total_pret']);
        }

        // Remboursements reçus par mois
        $remb = $db->prepare("SELECT YEAR(date_remboursement) as annee, MONTH(date_remboursement) as mois, SUM(montant) as total_remb
            FROM remboursement WHERE date_remboursement BETWEEN ? AND ?
            GROUP BY annee, mois");
        $remb->execute([$start->format('Y-m-d'), $end->format('Y-m-d')]);
        $remb_par_mois = [];
        foreach ($remb as $row) {
            $remb_par_mois[$row['annee'].'-'.$row['mois']] = floatval($row['total_remb']);
        }

        // Calcul du solde mois par mois
        $result = [];
        $solde = $solde_initial;
        $period = clone $start;
        while ($period <= $end) {
            $key = $period->format('Y-n');
            $pret = $prets_par_mois[$key] ?? 0;
            $rembourse = $remb_par_mois[$key] ?? 0;
            $solde = $solde - $pret + $rembourse;
            $result[] = [
                'mois' => $period->format('n'),
                'annee' => $period->format('Y'),
                'solde' => $solde
            ];
            $period->modify('+1 month');
        }
        Flight::json($result);
    }
}
