<?php
require_once __DIR__ . '/../db.php';

class Pret {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM ef_pret");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM ef_pret WHERE id_pret = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO ef_pret (id_client, id_type_pret, montant, duree, date_demande, id_statut, id_agent) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data->id_client,
            $data->id_type_pret,
            $data->montant,
            $data->duree,
            $data->date_demande,
            2, // En attente
            $data->id_agent
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE ef_pret SET id_client=?, id_type_pret=?, montant=?, duree=?, date_demande=?, id_agent=? WHERE id_pret=?");
        $stmt->execute([
            $data->id_client,
            $data->id_type_pret,
            $data->montant,
            $data->duree,
            $data->date_demande,
            $data->id_agent,
            $id
        ]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM ef_pret WHERE id_pret = ?");
        $stmt->execute([$id]);
    }

    public static function clientHasPretActif($id_client) {
        $db = getDB();
        $stmt = $db->prepare("SELECT COUNT(*) FROM ef_pret WHERE id_client = ? AND id_statut IN (1,2)");
        $stmt->execute([$id_client]);
        return $stmt->fetchColumn() > 0;
    }

    public static function valider($id, $id_admin) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE ef_pret SET id_statut = 2, id_agent = ? WHERE id_pret = ?");
        $stmt->execute([$id_admin, $id]);
    }

    public static function rejeter($id, $id_admin, $motif) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE ef_pret SET id_statut = 3, id_agent = ? WHERE id_pret = ?");
        $stmt->execute([$id_admin, $id]);
        // Optionnel : enregistrer le motif dans une table historique ou commentaire
    }
}
