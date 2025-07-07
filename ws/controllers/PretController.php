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
        // 2. Vérifier qu'il n'y a pas déjà un prêt actif pour ce client
        if (Pret::clientHasPretActif($data->id_client)) {
            Flight::json(['success'=>false, 'message'=>'Ce client a déjà un prêt actif non remboursé'], 400);
            return;
        }
        // 3. Vérifier les fonds de l'établissement
        $solde = Etablissement::getSolde($typePret['id_etablissement']);
        if ($solde < $data->montant) {
            Flight::json(['success'=>false, 'message'=>'Fonds insuffisants dans l\'établissement'], 400);
            return;
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
}
