<?php
session_start();

if (isset($_FILES['ubl_files'])) {
    $uploadedFiles = $_FILES['ubl_files'];
    $invoices = [];

    for ($i = 0; $i < count($uploadedFiles['name']); $i++) {
        if ($uploadedFiles['error'][$i] === UPLOAD_ERR_OK) {
            $tmpName = $uploadedFiles['tmp_name'][$i];
            $fileName = $uploadedFiles['name'][$i];

            // Move the uploaded file to a desired directory (e.g., "uploads/")
            $destination = __DIR__ . '/uploads/' . $fileName;
            if (move_uploaded_file($tmpName, $destination)) {
                // Parse the XML file to extract invoice data (example logic)
                $xml = simplexml_load_file($destination);

                $namespaces = $xml->getNamespaces(true);
                $cbc = $xml->children($namespaces['cbc']);
                $cac = $xml->children($namespaces['cac']);

                // Basic info
                $invoiceNumber = (string) $cbc->ID;
                $issueDate = (string) $cbc->IssueDate;

                // Customer name
                $xmlCustomerName = $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyName/cbc:Name');
                $customerName = isset($xmlCustomerName[0]) ? (string)$xmlCustomerName[0] : 'Unknown';

                // Total excluding tax
                $xmlExlTax = $xml->xpath('//cac:LegalMonetaryTotal/cbc:TaxExclusiveAmount');
                $totalExcludingTax = isset($xmlExlTax[0]) ? (float)$xmlExlTax[0] : 0;

                // Total
                $xmlTotalAmount = $xml->xpath('//cac:LegalMonetaryTotal/cbc:PayableAmount');
                $totalAmount = isset($xmlTotalAmount[0]) ? (float)$xmlTotalAmount[0] : 0;

                // tax amount
                $taxAmount = $totalAmount - $totalExcludingTax;

                $invoices[] = [
                    'number' => (string) $invoiceNumber,
                    'date' => (string) $issueDate,
                    'customer' => (string) $customerName,
                    'exclTax' => (string) $totalExcludingTax,
                    'tax' => (string) $taxAmount,
                    'total' => (string) $totalAmount
                ];
            }
        }
    }

    // Store the invoices in the session
    $_SESSION['invoices'] = $invoices;

    // Redirect back to the index page with a success flag
    header('Location: index.php?success=1');
    exit;
} else {
    // Handle the case where no files were uploaded
    header('Location: index.php?success=0');
    exit;
}
