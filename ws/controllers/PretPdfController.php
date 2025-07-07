<?php
require_once __DIR__ . '/../models/Pret.php';
require_once __DIR__ . '/../../fpdf186/fpdf.php';

class PretPdfController {
    public static function generer($id) {
        $pret = Pret::getById($id);
        if (!$pret) {
            http_response_code(404);
            echo "Prêt introuvable";
            exit;
        }
        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, utf8_decode('Attestation de prêt'), 0, 1, 'C');
        $pdf->Ln(10);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, utf8_decode('ID Prêt : ') . $pret['id_pret'], 0, 1);
        $pdf->Cell(0, 10, utf8_decode('Montant : ') . $pret['montant'] . ' Ar', 0, 1);
        $pdf->Cell(0, 10, utf8_decode('Durée : ') . $pret['duree'] . ' mois', 0, 1);
        $pdf->Cell(0, 10, utf8_decode('Date de demande : ') . $pret['date_demande'], 0, 1);
        // Ajoute d'autres infos si besoin
        $pdf->Output('I', "pret_{$pret['id_pret']}.pdf");
    }
}
