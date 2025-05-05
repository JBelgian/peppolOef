<?php
// export.php

session_start();
$invoices = $_SESSION['invoices'] ?? [];
$format = $_GET['format'] ?? 'csv';

if (!isset($invoices) || empty($invoices)) {
    die('No invoices available for export.');
}

if (isset($format) && $format === 'csv') {
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="invoices.csv"');

    $output = fopen('php://output', 'w');

    // Write the CSV header row
    fputcsv($output, ['Invoice Number', 'Issue Date', 'Customer Name', 'Amount excl. Tax', 'Tax Amount', 'Total Amount']);

    // Write each invoice row
    foreach ($invoices as $invoice) {
        fputcsv($output, [
            $invoice['number'],
            $invoice['date'],
            $invoice['customer'],
            $invoice['exclTax'],
            $invoice['tax'],
            $invoice['total']
        ]);
    }

    fclose($output);
    exit;
}

if ($format === 'pdf') {
    require_once __DIR__ . '/lib/fpdf.php';

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Invoice Summary', 0, 1, 'C');

    $pdf->SetFont('Arial', 'B', 10);
    $headers = ['Number', 'Date', 'Customer', 'Supplier', 'Total', 'Currency'];
    foreach ($headers as $header) {
        $pdf->Cell(32, 10, $header, 1);
    }
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 10);
    foreach ($invoices as $inv) {
        $pdf->Cell(32, 10, $inv['number'], 1);
        $pdf->Cell(32, 10, $inv['date'], 1);
        $pdf->Cell(32, 10, $inv['customer'], 1);
        $pdf->Cell(32, 10, $inv['supplier'], 1);
        $pdf->Cell(32, 10, $inv['total'], 1);
        $pdf->Cell(32, 10, $inv['currency'], 1);
        $pdf->Ln();
    }

    $pdf->Output();
    exit;
}

exit('Unsupported export format.');
