<?php
require_once __DIR__ . '/../db.php';

class TypePret {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM ef_type_pret");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM ef_type_pret WHERE id_type_pret = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("
            INSERT INTO ef_type_pret (nom, taux_annuel, duree_max, montant_min, montant_max, id_etablissement) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data->nom,
            $data->taux_annuel,
            $data->duree_max,
            $data->montant_min,
            $data->montant_max,
            $data->id_etablissement
        ]);
        return $db->lastInsertId();
    }

    public static function getTauxTypePret($id){
        $db = getDB();
        $stmt = $db->prepare("SELECT taux_annuel FROM ef_type_pret WHERE id_type_pret = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("
            UPDATE ef_type_pret 
            SET nom = ?, taux_annuel = ?, duree_max = ?, montant_min = ?, montant_max = ?, id_etablissement = ?
            WHERE id_type_pret = ?
        ");
        $stmt->execute([
            $data->nom,
            $data->taux_annuel,
            $data->duree_max,
            $data->montant_min,
            $data->montant_max,
            $data->id_etablissement,
            $id
        ]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM ef_type_pret WHERE id_type_pret = ?");
        $stmt->execute([$id]);
    }
}
