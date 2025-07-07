<?php
require_once __DIR__ . '/../db.php';
class Etablissement {
    public static function getSolde($id_etablissement) {
        $db = getDB();
        $stmt = $db->prepare("SELECT solde FROM ef_etablissement_financier WHERE id_etablissement = ?");
        $stmt->execute([$id_etablissement]);
        $row = $stmt->fetch();
        return $row ? $row['solde'] : 0;
    }
    public static function debiter($id_etablissement, $montant) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE ef_etablissement_financier SET solde = solde - ? WHERE id_etablissement = ?");
        $stmt->execute([$montant, $id_etablissement]);
    }
}
