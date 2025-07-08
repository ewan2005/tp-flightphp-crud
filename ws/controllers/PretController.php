<?php
require_once __DIR__ . '/../models/Pret.php';
require_once __DIR__ . '/../models/TypePret.php';
require_once __DIR__ . '/../models/Echeancier.php';
require_once __DIR__ . '/../models/Etablissement.php';

class PretController {
    public static function getAll() {
        $prets = Pret::getAll();
        Flight::json($prets);
    }

    public static function getById($id) {
        $pret = Pret::getById($id);
        Flight::json($pret);
    }

    public static function create() {
        $data = Flight::request()->data;
        // 1. Vérifier le plafond du type de prêt
        $typePret = TypePret::getById($data->id_type_pret);
        if (!$typePret) {
            Flight::json(['success'=>false, 'message'=>'Type de prêt introuvable'], 400);
            return;
        }
        if ($data->montant < $typePret['montant_min'] || $data->montant > $typePret['montant_max']) {
            Flight::json(['success'=>false, 'message'=>'Le montant demandé n\'est pas autorisé pour ce type de prêt'], 400);
            return;
        }
        // 3. Vérifier les fonds de l'établissement
        $solde = Etablissement::getSolde($typePret['id_etablissement']);
        if ($solde < $data->montant) {
            Flight::json(['success'=>false, 'message'=>'Fonds insuffisants dans l\'établissement'], 400);
            return;
        }

        if ($data -> duree > $typePret['duree_max']){
            Flight::json(['success'=>false,'message'=>'duree ne peut pas etre superieur au max'],400);
        }
        // 4. Créer le prêt
        $id = Pret::create($data);
        // 5. Débiter l'établissement
        Etablissement::debiter($typePret['id_etablissement'], $data->montant);
        // 6. Générer l'échéancier
        Echeancier::generer($id, $data->montant, $typePret['taux_annuel'], $data->duree, $data->date_demande);
        Flight::json(['success'=>true, 'message' => 'Demande de prêt enregistrée', 'id' => $id]);
    }

    public static function update($id) {
        $data = Flight::request()->data;
        Pret::update($id, $data);
        Flight::json(['message' => 'Prêt modifié']);
    }

    public static function delete($id) {
        Pret::delete($id);
        Flight::json(['message' => 'Prêt supprimé']);
    }

    public static function valider($id) {
        $id_admin = Flight::request()->data->id_admin;
        // Récupérer le prêt
        $pret = Pret::getById($id);
        if (!$pret) {
            Flight::json(['success'=>false, 'message'=>'Prêt introuvable'], 404);
            return;
        }
        // Récupérer le type de prêt
        $typePret = TypePret::getById($pret['id_type_pret']);
        if (!$typePret) {
            Flight::json(['success'=>false, 'message'=>'Type de prêt introuvable'], 404);
            return;
        }
        // Débiter l'établissement
        Etablissement::debiter($typePret['id_etablissement'], $pret['montant']);
        // Valider le prêt
        Pret::valider($id, $id_admin);
        Flight::json(['message' => 'Prêt validé et fonds débités']);
    }

    public static function rejeter($id) {
        $id_admin = Flight::request()->data->id_admin;
        $motif = Flight::request()->data->motif_rejet;
        Pret::rejeter($id, $id_admin, $motif);
        Flight::json(['message' => 'Prêt rejeté']);
    }
}
