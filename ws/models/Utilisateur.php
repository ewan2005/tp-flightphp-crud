<?php 


require_once __DIR__ . '/../db.php';
class Utilisateur {
    public static function findByEmail($email) {
        require_once __DIR__ . '/../db.php';
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM ef_utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function getAllAgents() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM ef_utilisateur WHERE role = 'agent'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>