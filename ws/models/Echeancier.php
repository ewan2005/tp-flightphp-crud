<?php
require_once __DIR__ . '/../db.php';
class Echeancier {
public static function generer($id_pret, $montant, $taux_annuel, $duree, $date_debut, $delai_premier_remboursement = 0) {
    $db = getDB();
    
    // Calcul de la mensualité (annuité constante)
    $taux_mensuel = $taux_annuel / 100 / 12;
    $mensualite = $montant * $taux_mensuel / (1 - pow(1 + $taux_mensuel, -$duree));
    
    // Calcul du tableau d'amortissement
    $capital_restant = $montant;
    
    // Date de début des remboursements (date_demande + délai)
    $date_premiere_echeance = new DateTime($date_debut);
    $date_premiere_echeance->modify("+".$delai_premier_remboursement." months");
    
    for ($i = 1; $i <= $duree; $i++) {
        // Calcul des composantes de la mensualité
        $interet = $capital_restant * $taux_mensuel;
        $amortissement = $mensualite - $interet;
        $capital_restant -= $amortissement;
        
        // Calcul de la date d'échéance
        $date_echeance = clone $date_premiere_echeance;
        $date_echeance->modify("+".($i-1)." months");
        
        // Insertion dans la table ef_echeance_pret (plus complète que ef_echeancier)
        $stmt = $db->prepare("
            INSERT INTO ef_echeance_pret 
            (id_pret, mois_numero, date_echeance, montant_annuite, part_interet, part_capital, reste_a_payer) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $id_pret,
            $i,
            $date_echeance->format('Y-m-d'),
            $mensualite,
            $interet,
            $amortissement,
            $capital_restant
        ]);
    }
}
}
