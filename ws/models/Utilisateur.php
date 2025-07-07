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
}

?>