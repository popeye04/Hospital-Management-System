<?php
require 'db.php';

// -----------------------------------------
// EDIT MODE
// -----------------------------------------
$edit = false;
$editData = null;

if (isset($_GET['edit'])) {
    $edit = true;
    $edit_id = $_GET['edit'];

    $stmt = $pdo->prepare("SELECT * FROM prescription WHERE prescription_id = ?");
    $stmt->execute([$edit_id]);
    $editData = $stmt->fetch();

    if (!$editData) {
        $edit = false;
    }
}

// -----------------------------------------
// UPDATE PRESCRIPTION
// -----------------------------------------
if (isset($_POST['update'])) {
    $prescription_id = $_POST['prescription_id'];
    $treatment_id    = $_POST['treatment_id'];
    $medicine_id     = $_POST['medicine_id'];
    $dosage          = $_POST['dosage'];
    $frequency       = $_POST['frequency'];
    $duration        = $_POST['duration'];

    $sql = "UPDATE prescription
            SET treatment_id = ?, medicine_id = ?, dosage = ?, frequency = ?, duration = ?
            WHERE prescription_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $treatment_id, $medicine_id, $dosage, $frequency, $duration, $prescription_id
    ]);

    header("Location: prescriptions.php");
    exit;
}

// -----------------------------------------
// ADD NEW
// -----------------------------------------
if (isset($_POST['add']) && !$edit) {
    $treatment_id = $_POST['treatment_id'];
    $medicine_id  = $_POST['medicine_id'];
    $dosage       = $_POST['dosage'];
    $frequency    = $_POST['frequency'];
    $duration     = $_POST['duration'];

    $sql = "INSERT INTO prescription
            (treatment_id, medicine_id, dosage, frequency, duration)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $treatment_id, $medicine_id, $dosage, $frequency, $duration
    ]);

    header("Location: prescriptions.php");
    exit;
}

// -----------------------------------------
// DELETE
// -----------------------------------------
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM prescription WHERE prescription_id = ?");
    $stmt->execute([$id]);

    header("Location: prescriptions.php");
    exit;
}

// -----------------------------------------
// SEARCH + LIST
// -----------------------------------------
$search = isset($_GET['search']) ? $_GET['search'] : "";

$sql = "SELECT pr.*, m.medicine_name, t.treatment_id
        FROM prescription pr
        LEFT JOIN medicine m ON pr.medicine_id = m.medicine_id
        LEFT JOIN treatment t ON pr.treatment_id = t.treatment_id
        WHERE m.medicine_name LIKE ?
           OR pr.dosage LIKE ?
           OR pr.frequency LIKE ?
           OR pr.duration LIKE ?
        ORDER BY pr.prescription_id ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute(["%$search%", "%$search%", "%$search%", "%$search%"]);
$prescriptions = $stmt->fetchAll();

// -----------------------------------------
// DROPDOWNS
// -----------------------------------------
$treatments = $pdo->query("
    SELECT t.treatment_id, p.first_name, p.last_name
    FROM treatment t
    JOIN admission a ON t.admission_id = a.admission_id
    JOIN patient p ON a.patient_id = p.patient_id
    ORDER BY t.treatment_id ASC
")->fetchAll();

$medicines = $pdo->query("
    SELECT medicine_id, medicine_name
    FROM medicine
    ORDER BY medicine_id ASC
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Prescriptions</title>
    <style>
        body { font-family: Arial; margin: 40px; background: #f9f9f9; }
        table { width:100%; border-collapse: collapse; margin-top:20px; }
        th, td { border:1px solid #ccc; padding:10px; }
        th { background:#007cba; color:white; }
        input, select { padding:8px; margin:5px; width:230px; border:1px solid #999; border-radius:4px; }
        button, .btn {
            padding:8px 15px; color:white; border:none; border-radius:4px;
            cursor:pointer; text-decoration:none; background:#007cba;
        }
        .delete-btn { background:#d9534f; }
        .edit-btn   { background:#f0ad4e; color:black; }
        .cancel-btn { background:gray; }
        .menu a { margin-right:20px; text-decoration:none; color:#007cba; }
    </style>
</head>
<body>

<h1>Manage Prescriptions</h1>

<div class="menu">
    <a href="index.php">Home</a> |
    <a href="patients.php">Patients</a> |
    <a href="treatments.php">Treatments</a> |
    <a href="prescriptions.php">Prescriptions</a>
</div>

<hr>

<!-- SEARCH -->
<form method="GET">
    <input type="text" name="search" placeholder="Search..."
           value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search</button>

    <?php if ($search): ?>
        <a href="prescriptions.php" class="btn cancel-btn">Clear</a>
    <?php endif; ?>
</form>

<hr>

<!-- ADD / EDIT FORM -->
<h2><?= $edit ? "Edit Prescription" : "Issue Prescription" ?></h2>

<form method="POST">

    <?php if ($edit): ?>
        <input type="hidden" name="prescription_id" value="<?= $editData['prescription_id'] ?>">
    <?php endif; ?>

    <!-- treatment -->
    <select name="treatment_id" required>
        <option value="">Select Treatment</option>
        <?php foreach($treatments as $t): ?>
            <option value="<?= $t['treatment_id'] ?>"
                <?= $edit && $editData['treatment_id'] == $t['treatment_id'] ? "selected" : "" ?>>
                Treatment #<?= $t['treatment_id'] ?> - 
                <?= htmlspecialchars($t['first_name'].' '.$t['last_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <!-- medicine -->
    <select name="medicine_id" required>
        <option value="">Select Medicine</option>
        <?php foreach($medicines as $m): ?>
            <option value="<?= $m['medicine_id'] ?>"
                <?= $edit && $editData['medicine_id'] == $m['medicine_id'] ? "selected" : "" ?>>
                <?= htmlspecialchars($m['medicine_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input type="text" name="dosage" placeholder="Dosage"
           value="<?= $edit ? htmlspecialchars($editData['dosage']) : "" ?>" required>

    <input type="text" name="frequency" placeholder="Frequency"
           value="<?= $edit ? htmlspecialchars($editData['frequency']) : "" ?>" required>

    <input type="text" name="duration" placeholder="Duration"
           value="<?= $edit ? htmlspecialchars($editData['duration']) : "" ?>" required>

    <?php if ($edit): ?>
        <button type="submit" name="update">Update</button>
        <a href="prescriptions.php" class="btn cancel-btn">Cancel</a>
    <?php else: ?>
        <button type="submit" name="add">Issue Prescription</button>
    <?php endif; ?>
</form>

<hr>

<!-- LIST -->
<h2>All Prescriptions (<?= count($prescriptions) ?>)</h2>

<?php if (count($prescriptions) == 0): ?>
    <p>No prescriptions yet.</p>
<?php else: ?>
<table>
    <tr>
        <th>ID</th>
        <th>Treatment</th>
        <th>Medicine</th>
        <th>Dosage</th>
        <th>Frequency</th>
        <th>Duration</th>
        <th>Action</th>
    </tr>

    <?php foreach($prescriptions as $p): ?>
    <tr>
        <td><?= $p['prescription_id'] ?></td>
        <td><?= $p['treatment_id'] ?></td>
        <td><?= htmlspecialchars($p['medicine_name']) ?></td>
        <td><?= htmlspecialchars($p['dosage']) ?></td>
        <td><?= htmlspecialchars($p['frequency']) ?></td>
        <td><?= htmlspecialchars($p['duration']) ?></td>
        <td>
            <a href="prescriptions.php?edit=<?= $p['prescription_id'] ?>" class="btn edit-btn">Edit</a>
            <a href="prescriptions.php?delete=<?= $p['prescription_id'] ?>"
               class="btn delete-btn"
               onclick="return confirm('Delete this prescription?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<br><br>
<a href="index.php">Back to Homepage</a>

</body>
</html>
