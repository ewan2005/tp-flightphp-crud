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
            'interets' => $interets,
            'monthly' => [
                'clients' => [2, 4, 3, 6, 5, 7, 4, 6, 5, 3, 4, 3],
                'prets' => [1, 3, 2, 4, 3, 5, 2, 4, 3, 3, 2, 3],
                'montants' => [150000, 320000, 210000, 380000, 430000, 460000, 400000, 470000, 480000, 500000, 520000, 530000]
            ]
        ];
    }
}
