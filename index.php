<?php
// index.php
?>
<!DOCTYPE html>
<html>
<head>
    <title>File Uploader</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container py-4">
    <h2 class="mb-4">Upload Invoices</h2>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <input type="file" name="ubl_files[]" multiple accept=".xml" class="form-control mb-3">
        <button type="submit" class="btn btn-primary">Upload</button>
    </form>

    <?php if (isset($_GET['success'])): ?>
        <?php session_start(); ?>
        <h3 class="mt-5">Overview</h3>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Invoice Number</th>
                    <th>Issue Date</th>
                    <th>Customer Name</th>
                    <th>Amount excl. Tax</th>
                    <th>Tax</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['invoices'] as $invoice): ?>
                    <tr>
                        <td><?= htmlspecialchars($invoice['number']) ?></td>
                        <td><?= htmlspecialchars($invoice['date']) ?></td>
                        <td><?= htmlspecialchars($invoice['customer']) ?></td>
                        <td>€<?= htmlspecialchars($invoice['exclTax']) ?></td>
                        <td>€<?= htmlspecialchars($invoice['tax']) ?></td>
                        <td>€<?= htmlspecialchars($invoice['total']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="export.php?format=csv" class="btn btn-success me-2">Export as CSV</a>
        <a href="export.php?format=pdf" class="btn btn-danger">Export as PDF</a>
    <?php endif; ?>
</body>
</html>

<script>
    <?php if (isset($_SESSION['invoices'])): ?>
    const invoiceData = <?php echo json_encode($_SESSION['invoices']); ?>;
    sessionStorage.setItem('invoices', JSON.stringify(invoiceData));
    <?php endif; ?>
</script>

