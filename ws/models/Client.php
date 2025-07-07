<?php 
require_once __DIR__ . '/../db.php';

class Client {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM ef_client");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM ef_client WHERE id_client = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO ef_client (nom, prenom, date_naissance, email, telephone) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$data->nom, $data->prenom, $data->date_naissance, $data->email, $data->telephone]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE ef_client SET nom = ?, prenom = ?, date_naissance = ?, email = ?, telephone = ? WHERE id_client = ?");
        $stmt->execute([$data->nom, $data->prenom, $data->date_naissance, $data->email, $data->telephone, $id]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM ef_client WHERE id_client = ?");
        $stmt->execute([$id]);
    }
}
?>
