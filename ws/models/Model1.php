<?php
require_once __DIR__ . '/../db.php';

class Model1 {

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT id_etablissement FROM ef_utilisateur WHERE id_utilisateur = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // public static function getAssuranceById($id) {
    //     $db = getDB();
    //     $stmt = $db->prepare("SELECT assurance FROM ef_pret WHERE id_pret = ?");
    //     $stmt->execute([$id]);
    //     return $stmt->fetch(PDO::FETCH_ASSOC);
    // }

    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM ef_etablissement_financier");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    public static function getAllClient()
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM ef_client");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function getAllPretNonFait($id)
    {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT 
                ep.id_pret,
                ep.id_client,
                ep.montant,
                ep.duree,
                ep.date_demande,
                ec.id_echeance,
                ec.mois_numero,
                ec.date_echeance,
                ec.montant_annuite,
                ec.part_interet,
                ec.part_capital,
                ec.reste_a_payer,
                ec.est_paye
            FROM ef_pret ep
            LEFT JOIN ef_echeance_pret ec ON ep.id_pret = ec.id_pret
            WHERE ep.id_client = ?
            AND (ec.est_paye = FALSE OR ec.est_paye IS NULL) GROUP BY ep.id_pret
            ORDER BY ep.id_pret, ec.mois_numero 
        ");
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllEcheance($id)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM ef_echeance_pret WHERE id_pret = ?");
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 

    public static function annuiter($idPret) 
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT montant, duree,assurance FROM ef_pret WHERE id_pret = ?");
        $stmt->execute([$idPret]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getInfoPret($idPret)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT montant, duree, date_demande,assurance FROM ef_pret WHERE id_pret = ?");
        $stmt->execute([$idPret]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } 
}
