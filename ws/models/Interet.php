<?php
require_once __DIR__ . '/../db.php';
class Interet {
    /**
     * Retourne les intérêts gagnés par mois pour l'établissement financier, filtrés par période.
     * @param int $mois_debut
     * @param int $annee_debut
     * @param int $mois_fin
     * @param int $annee_fin
     * @return array
     */
    public static function getInteretsParMois($mois_debut, $annee_debut, $mois_fin, $annee_fin) {
        $db = getDB();
        $date_debut = sprintf('%04d-%02d-01', $annee_debut, $mois_debut);
        $date_fin = date('Y-m-t', strtotime(sprintf('%04d-%02d-01', $annee_fin, $mois_fin)));
        $sql = "SELECT YEAR(date_demande) as annee, MONTH(date_demande) as mois, SUM(montant * (tp.taux_annuel/100) * (duree/12)) as interet_gagne
                FROM ef_pret p
                JOIN ef_type_pret tp ON p.id_type_pret = tp.id_type_pret
                WHERE p.id_statut = 2 AND p.date_demande BETWEEN ? AND ?
                GROUP BY annee, mois
                ORDER BY annee, mois";
        $stmt = $db->prepare($sql);
        $stmt->execute([$date_debut, $date_fin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
