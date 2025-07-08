<?php
require_once __DIR__ . '/../models/Pret.php';
require_once __DIR__ . '/../models/Model1.php';
require_once __DIR__ . '/../../fpdf186/fpdf.php';

class PretPdfController {
    public static function generer($id) {
        $pret = Pret::getById($id);
        $echeances = Model1::getAllEcheance($id);
        
        if (!$pret) {
            http_response_code(404);
            echo "Prêt introuvable";
            exit;
        }

        $pdf = new \FPDF();
        $pdf->AddPage();
        
        // Titre
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, utf8_decode('Attestation de prêt'), 0, 1, 'C');
        $pdf->Ln(10);
        
        // Informations de base du prêt
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, utf8_decode('ID Prêt : ') . $pret['id_pret'], 0, 1);
        $pdf->Cell(0, 10, utf8_decode('Montant : ') . number_format($pret['montant'], 2, '.', ' ') . ' Ar', 0, 1);
        $pdf->Cell(0, 10, utf8_decode('Durée : ') . $pret['duree'] . ' mois', 0, 1);
        $pdf->Cell(0, 10, utf8_decode('Date de demande : ') . $pret['date_demande'], 0, 1);
        $pdf->Ln(10);
        
        // Tableau des échéances
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, utf8_decode('Échéances du prêt'), 0, 1, 'C');
        $pdf->Ln(5);
        
        // En-têtes du tableau
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(30, 10, utf8_decode('Date'), 1);
        $pdf->Cell(25, 10, utf8_decode('Annuité'), 1);
        $pdf->Cell(20, 10, utf8_decode('Assurance'), 1);
        $pdf->Cell(20, 10, utf8_decode('Intérêt'), 1);
        $pdf->Cell(25, 10, utf8_decode('Capital'), 1);
        $pdf->Cell(25, 10, utf8_decode('Reste'), 1);
        $pdf->Cell(20, 10, utf8_decode('Payé?'), 1);
        $pdf->Ln();
        
        // Contenu du tableau
        $pdf->SetFont('Arial', '', 8);
        foreach ($echeances as $echeance) {
            // Conversion 1/0 en Oui/Non
            $estPaye = ($echeance['est_paye'] == 1) ? 'Oui' : 'Non';
            
            $pdf->Cell(30, 10, $echeance['date_echeance'], 1);
            $pdf->Cell(25, 10, number_format($echeance['montant_annuite'], 2, '.', ' '), 1);
            $pdf->Cell(20, 10, number_format($echeance['assurance'], 2, '.', ' '), 1);
            $pdf->Cell(20, 10, number_format($echeance['part_interet'], 2, '.', ' '), 1);
            $pdf->Cell(25, 10, number_format($echeance['part_capital'], 2, '.', ' '), 1);
            $pdf->Cell(25, 10, number_format($echeance['reste_a_payer'], 2, '.', ' '), 1);
            $pdf->Cell(20, 10, utf8_decode($estPaye), 1);
            $pdf->Ln();
        }
        
        $pdf->Output('I', "pret_{$pret['id_pret']}.pdf");
    }
}