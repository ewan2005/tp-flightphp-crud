<?php
require_once __DIR__ . '/../db.php';

class Model1 {

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT id_etablissement FROM ef_utilisateur WHERE id_utilisateur = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO ef_ajout_fonds (id_utilisateur, montant, date_ajout) VALUES (?, ?, ?)");
        $stmt->execute([$data->id_utilisateur, $data->montant, $data->date_ajout]);
        return $db->lastInsertId();
    }
    public static function createH($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO ef_historique_transaction (id_utilisateur, montant, description, date) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data->id_utilisateur, $data->montant, $data->description, $data->date]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
    
        if (!isset($data->solde) || !is_numeric($data->solde)) {
            throw new InvalidArgumentException("Le champ 'solde' doit être un nombre.");
        }
    
        $solde = (float)$data->solde;
    
        $stmt = $db->prepare("UPDATE ef_etablissement_financier SET solde = solde + ? WHERE id_etablissement = ?");
        $stmt->execute([$solde, (int)$id]);
    }

}
