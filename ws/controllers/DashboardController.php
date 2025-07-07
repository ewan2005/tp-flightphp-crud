<?php
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/Pret.php';
require_once __DIR__ . '/../models/TypePret.php';

class DashboardController {
    public static function getStats() {
        $nbClients = count(Client::getAll());
        $nbPrets = count(Pret::getAll());
        $montantTotal = 0;
        $interets = 0;
        foreach (Pret::getAll() as $pret) {
            $montantTotal += $pret['montant'];
            if (isset($pret['id_type_pret'])) {
                $taux = TypePret::getTauxTypePret($pret['id_type_pret']);
                $interets += $pret['montant'] * ($taux/100) * ($pret['duree']/12);
            }
        }
        return [
            'nbClients' => $nbClients,
            'nbPrets' => $nbPrets,
            'montantTotal' => $montantTotal,
            'interets' => $interets
        ];
    }
}
