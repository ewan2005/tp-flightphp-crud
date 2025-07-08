<?php
require_once __DIR__ . '/../models/Model1.php';
require_once __DIR__ . '/../helpers/Utils.php';
require_once __DIR__ . '/../db.php';


class Controlleur1 
{
    public static function getById($id) {
        $model = Model1::getById($id);
        Flight::json($model);
    }

    // public static function getAssuranceById($id) {
    //     $model = Model1::getAssuranceById($id);
    //     if ($model) {
    //         Flight::json(['success' => true, 'data' => $model]);
    //     } else {
    //         Flight::json(['success' => false, 'message' => 'Assurance introuvable'], 404);
    //     }
    // }

    public static function create() 
    {
        $data = Flight::request()->data;
        $id = Model1::create($data);
        Flight::json(['message' => 'Fond ajouté', 'id' => $id]);
    }

    public static function update($id) {
        $data = Flight::request()->data;
        $etab = Model1::getById($id);
    
        if (!$etab || !isset($etab['id_etablissement'])) {
            Flight::halt(404, json_encode(['error' => 'Etablissement introuvable pour cet utilisateur']));
        }
    
        $idEtab = $etab['id_etablissement'];
        Model1::update($idEtab, $data);
    
        Flight::json(['message' => 'Fonds mis à jour', 'id' => $idEtab]);
    }
    public static function createH() {
        $data = Flight::request()->data;
        $id = Model1::createH($data);
        Flight::json(['message' => 'Fond cret']);
    }
    public static function getClients()
    {
        $model = Model1::getAllClient();
        Flight::json($model);
    }
    public static function getPret($id)
    {
        $model = Model1::getAllPretNonFait($id);
        Flight::json($model);
    }
    public static function getEcheance($id)
    {
        $model = Model1::getAllEcheance($id);
        Flight::json($model);
    }
    public static function annuite($idPret)
    {
        $model = Model1::annuiter($idPret);
        if (!$model) {
            Flight::json(['error' => 'Prêt introuvable'], 404);
            return;
        }
    
        $montant = $model['montant'];
        $duree = $model['duree'];
    
        $taux_annuel = 0.12;
        $taux_mensuel = $taux_annuel / 12;
        $taux_assurance = $model['assurance']; 
        $annuite = $montant * ($taux_mensuel / (1 - pow(1 + $taux_mensuel, -$duree)));
        $montant_assurance = $montant * ($taux_assurance / 100);
        
        Flight::json([
            'id_pret' => $idPret,
            'montant_annuite' => round($annuite, 2),
            'montant_assurance' => round($montant_assurance, 2),
            'taux_mensuel' => $taux_mensuel,
            'duree' => $duree
        ]);
    } 

    public static function getAll()
    {
        $model = Model1::getAll();
        Flight::json($model);
    }

    public static function traitement_annuite()
    {
        $db = getDB();
        $idClient = $_POST["idClient"] ?? null;
        $idPret = $_POST['idPret'] ?? null;
        $montantRecu = floatval($_POST['montant'] ?? 0);

        try {
            if (!$idClient || !$idPret || $montantRecu <= 0) {
                throw new Exception("Paramètres invalides.");
            }

            // Récupérer l'établissement de l'utilisateur
            $etabRow = Model1::getById($idClient);
            if (!$etabRow || !isset($etabRow['id_etablissement'])) {
                throw new Exception("Établissement non trouvé pour l'utilisateur.");
            }
            $idEtablissement = $etabRow['id_etablissement'];

            $db->beginTransaction();
            $model = Model1::getInfoPret($idPret);
            // Vérifie si les échéances existent déjà
            $stmt = $db->prepare("SELECT COUNT(*) FROM ef_echeance_pret WHERE id_pret = ?");
            $stmt->execute([$idPret]);
            $nbEcheances = (int) $stmt->fetchColumn();

            if ($nbEcheances === 0) {
                $pret = Model1::getInfoPret($idPret);
                if (!$pret) {
                    throw new Exception("Prêt introuvable.");
                }

                $montant = (float) $pret['montant'];
                $duree = (int) $pret['duree'];
                $dateDemande = new DateTime($pret['date_demande']);
                $tauxAnnuel = 0.10;

                $i = $tauxAnnuel / 12;
                $A = round($montant * ($i / (1 - pow(1 + $i, -$duree))), 2);

                $capitalRestant = $montant;
                $dateEcheance = clone $dateDemande;
                $dateEcheance->modify('+1 month');

                    for ($mois = 1; $mois <= $duree; $mois++) {
                        $interet = round($capitalRestant * $i, 2);
                        $partCapital = round($A - $interet, 2);
                        $reste = round($capitalRestant - $partCapital, 2);

                        // Correction ici : calcul de la mensualité d'assurance
                        $tauxAssurance = floatval($model['assurance']); // ex: 1 pour 1%
                        $mensualiteAssurance = round($montant * ($tauxAssurance / 100), 2);

                        // Le montant total à payer pour cette échéance
                        $montantTotalEcheance = $A + $mensualiteAssurance;

                        $stmtInsert = $db->prepare("
                            INSERT INTO ef_echeance_pret
                            (id_pret, mois_numero, date_echeance, montant_annuite, part_interet, part_capital, reste_a_payer, assurance, est_paye)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, FALSE)
                        ");
                        $stmtInsert->execute([
                            $idPret,
                            $mois,
                            $dateEcheance->format('Y-m-d'),
                            $A,
                            $interet,
                            $partCapital,
                            $reste > 0 ? $reste : 0,
                            $mensualiteAssurance
                        ]); 

                        $capitalRestant = $reste > 0 ? $reste : 0;
                        $dateEcheance->modify('+1 month');
                    }
            }

            // Remboursement des échéances tant que possible
            $stmt = $db->prepare("SELECT * FROM ef_echeance_pret WHERE id_pret = ? AND est_paye = FALSE ORDER BY mois_numero ASC");
            $stmt->execute([$idPret]);
            $echeances = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$echeances || count($echeances) === 0) {
                throw new Exception("Toutes les échéances sont déjà remboursées.");
            }

            $echeancesPayees = 0;
            $montantInitial = $montantRecu;

            foreach ($echeances as $echeance) {
                $montantEcheance = floatval($echeance['montant_annuite']);

                if ($montantRecu >= $montantEcheance) {
                    // Marquer comme payée
                    $update = $db->prepare("
                        UPDATE ef_echeance_pret
                        SET est_paye = TRUE, reste_a_payer = 0
                        WHERE id_echeance = ?
                    ");
                    $update->execute([$echeance['id_echeance']]);

                    // Insérer remboursement
                    $insert = $db->prepare("
                        INSERT INTO remboursement (id_pret, id_echeance, montant)
                        VALUES (?, ?, ?)
                    ");
                    $insert->execute([$idPret, $echeance['id_echeance'], $montantEcheance]);

                    $montantRecu -= $montantEcheance;
                    $echeancesPayees++;
                } else {
                    break; // Montant insuffisant pour une autre échéance
                }
            }

            if ($echeancesPayees === 0) {
                throw new Exception("Montant insuffisant pour rembourser une échéance.");
            }

            // Mise à jour du solde dans ef_etablissement_financier
            $montantTotalRembourse = $montantInitial - $montantRecu;
            Model1::update($idEtablissement, (object)["solde" => $montantTotalRembourse]);

            $db->commit();

            Flight::json([
                "success" => true,
                "message" => "$echeancesPayees échéance(s) remboursée(s) avec succès.",
                "reste" => $montantRecu
            ]);
        } catch (Exception $e) {
            $db->rollBack();
            Flight::json([
                "success" => false,
                "error" => $e->getMessage()
            ], 500);
        }
    }

}
