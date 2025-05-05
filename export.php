<?php
// export.php

session_start();
require 'vendor/autoload.php'; // Ensure Dompdf is installed via Composer

use Dompdf\Dompdf;

$invoices = $_SESSION['invoices'] ?? [];
$format = $_GET['format'];

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
    // PDF Export Logic
    $dompdf = new Dompdf();

    // Generate the HTML for the PDF
    $html = '<h1>Invoice Overview</h1>';
    $html .= '<table border="1" cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse;">';
    $html .= '<thead>
                <tr>
                    <th>Invoice Number</th>
                    <th>Issue Date</th>
                    <th>Customer Name</th>
                    <th>Amount excl. Tax</th>
                    <th>Tax Amount</th>
                    <th>Total Amount</th>
                </tr>
              </thead>';
    $html .= '<tbody>';
    foreach ($invoices as $invoice) {
        $html .= '<tr>
                    <td>' . htmlspecialchars($invoice['number']) . '</td>
                    <td>' . htmlspecialchars($invoice['date']) . '</td>
                    <td>' . htmlspecialchars($invoice['customer']) . '</td>
                    <td>€' . htmlspecialchars($invoice['exclTax']) . '</td>
                    <td>€' . htmlspecialchars($invoice['tax']) . '</td>
                    <td>€' . htmlspecialchars($invoice['total']) . '</td>
                  </tr>';
    }
    $html .= '</tbody></table>';

    // Load the HTML into Dompdf
    $dompdf->loadHtml($html);

    // Set paper size and orientation
    $dompdf->setPaper('A4', 'portrait');

    // Render the PDF
    $dompdf->render();

    // Output the PDF for download
    $dompdf->stream('invoices.pdf', ['Attachment' => true]);
    exit;
}

exit('Unsupported export format.');
