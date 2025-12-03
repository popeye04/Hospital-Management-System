<?php require 'db.php'; 

// -----------------------------------------
// ADD NEW INVOICE
// -----------------------------------------
if(isset($_POST['add'])) {
    $admission_id = $_POST['admission_id'];
    $patient_id   = $_POST['patient_id'];
    $amount       = $_POST['amount'];
    $status       = $_POST['status'];
    $issued_date  = $_POST['issued_date'];

    $sql = "INSERT INTO invoice 
            (admission_id, patient_id, amount, issued_date, status) 
            VALUES (?, ?, ?, ?, ?)";
    
    $pdo->prepare($sql)->execute([$admission_id, $patient_id, $amount, $issued_date, $status]);
    header("Location: invoices.php");
    exit;
}

// -----------------------------------------
// DELETE INVOICE
// -----------------------------------------
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM invoice WHERE invoice_id = ?")->execute([$id]);
    header("Location: invoices.php");
    exit;
}

// -----------------------------------------
// SEARCH + LIST
// -----------------------------------------
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT i.*, p.first_name, p.last_name
        FROM invoice i
        JOIN patient p ON i.patient_id = p.patient_id
        WHERE p.first_name LIKE ? OR p.last_name LIKE ? 
           OR i.invoice_id LIKE ? OR i.status LIKE ?
        ORDER BY i.invoice_id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute(["%$search%","%$search%","%$search%","%$search%"]);
$invoices = $stmt->fetchAll();

// -----------------------------------------
// Admissions dropdown
// -----------------------------------------
$admissions = $pdo->query("
    SELECT a.admission_id, a.patient_id, p.first_name, p.last_name, a.room_number
    FROM admission a
    JOIN patient p ON a.patient_id = p.patient_id
    ORDER BY a.admission_id ASC
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Invoices</title>
    <style>
        body { font-family: Arial; margin: 40px; background: #f9f9f9; }
        table, th, td { border: 1px solid #ccc; padding: 12px; border-collapse: collapse; width: 100%; }
        th { background: #007cba; color: white; }
        th.invoice-id, td.invoice-id { width: 80px; }
        th.patient, td.patient { width: 200px; }
        th.admission-id, td.admission-id { width: 100px; }
        th.amount, td.amount { width: 100px; }
        th.issued-date, td.issued-date { width: 120px; }
        th.status, td.status { width: 100px; }
        th.action, td.action { width: 120px; }
        input, select { padding: 10px; margin: 6px 3px; width: 280px; border: 1px solid #ddd; border-radius: 4px; }
        button, .btn { padding: 10px 16px; background: #007cba; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-delete { background: #d9534f; }
        .Paid { color: green; font-weight: bold; }
        .Unpaid, .Pending { color: red; font-weight: bold; }
        .Pending { color: orange; }
        .menu a { margin-right: 20px; color: #007cba; font-size: 18px; text-decoration: none; }
        hr { margin: 30px 0; border: 1px solid #eee; }
    </style>
</head>
<body>

<h1>Manage Invoices</h1>

<div class="menu">
    <a href="index.php">Home</a> |
    <a href="patients.php">Patients</a> |
    <a href="admissions.php">Admissions</a> |
    <a href="invoices.php">Invoices</a>
</div>
<hr>

<!-- SEARCH FORM -->
<form method="GET">
    <input type="text" name="search" placeholder="Search patient or invoice..." 
           value="<?=htmlspecialchars($search)?>" size="60">
    <button type="submit">Search</button>
    <?php if($search): ?><a href="invoices.php" class="btn" style="background:gray;">Clear</a><?php endif; ?>
</form>

<!-- ADD NEW INVOICE -->
<h2>Generate New Invoice</h2>
<form method="POST">
    <select name="admission_id" required>
        <option value="">Select Admission</option>
        <?php foreach($admissions as $a): ?>
            <option value="<?= $a['admission_id'] ?>">
                <?= htmlspecialchars($a['first_name'].' '.$a['last_name']) ?> (Room <?= $a['room_number'] ?? '—' ?>)
            </option>
        <?php endforeach; ?>
    </select>

    <?php
    $selected_patient_id = '';
    if(isset($_POST['admission_id'])) {
        foreach($admissions as $a) {
            if($a['admission_id'] == $_POST['admission_id']) {
                $selected_patient_id = $a['patient_id'];
                break;
            }
        }
    }
    ?>
    <input type="hidden" name="patient_id" value="<?= $selected_patient_id ?>">

    <input type="number" step="0.01" name="amount" placeholder="Amount" required min="0.01">

    <input type="date" name="issued_date" value="<?= date('Y-m-d') ?>" required>

    <select name="status" required>
        <option value="Pending">Pending</option>
        <option value="Paid">Paid</option>
        <option value="Unpaid">Unpaid</option>
    </select>

    <button type="submit" name="add">Generate Invoice</button>
</form>

<hr>

<!-- LIST ALL INVOICES -->
<h2>All Invoices (<?= count($invoices) ?>)</h2>
<?php if(count($invoices) == 0): ?>
    <p>No invoices found.</p>
<?php else: ?>
<table>
    <tr>
        <th class="invoice-id">Invoice ID</th>
        <th class="patient">Patient</th>
        <th class="admission-id">Admission ID</th>
        <th class="amount">Amount</th>
        <th class="issued-date">Issued Date</th>
        <th class="status">Status</th>
        <th class="action">Action</th>
    </tr>
    <?php foreach($invoices as $i): ?>
    <tr>
        <td class="invoice-id"><strong>#<?= str_pad($i['invoice_id'], 5, '0', STR_PAD_LEFT) ?></strong></td>
        <td class="patient"><?= htmlspecialchars($i['first_name'].' '.$i['last_name']) ?></td>
        <td class="admission-id"><?= $i['admission_id'] ?></td>
        <td class="amount">$<?= number_format($i['amount'], 2) ?></td>
        <td class="issued-date"><?= $i['issued_date'] ?></td>
        <td class="status <?= htmlspecialchars($i['status']) ?>"><?= htmlspecialchars($i['status']) ?></td>
        <td class="action">
            <a href="invoices.php?delete=<?= $i['invoice_id'] ?>" 
               class="btn btn-delete" 
               onclick="return confirm('Delete this invoice?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<br><br>
<a href="index.php">Back to Homepage</a>

</body>
</html>
