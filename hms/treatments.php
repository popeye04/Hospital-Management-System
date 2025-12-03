<?php
require 'db.php';
$message = "";
$edit_mode = false;
$edit_treatment = null;

// ———————————————— 1. EDIT MODE ————————————————
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM treatment WHERE treatment_id = ?");
    $stmt->execute([$id]);
    $edit_treatment = $stmt->fetch();
    if ($edit_treatment) $edit_mode = true;
}

// ———————————————— 2. UPDATE TREATMENT ————————————————
if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $admission_id = $_POST['admission_id'];
    $doctor_id = $_POST['doctor_id'];
    $treating_date = $_POST['treating_date'];
    $details = trim($_POST['details']);
    if ($admission_id && $doctor_id && $treating_date && $details) {
        $sql = "UPDATE treatment SET
                admission_id=?, doctor_id=?, treating_date=?, details=?
                WHERE treatment_id=?";
        $pdo->prepare($sql)->execute([$admission_id, $doctor_id, $treating_date, $details, $id]);
        $message = "Treatment updated successfully!";
        $edit_mode = false;
    } else {
        $message = "Please fill all fields!";
    }
}

// ———————————————— 3. ADD NEW TREATMENT ————————————————
if (isset($_POST['add']) && !$edit_mode) {
    $admission_id = $_POST['admission_id'];
    $doctor_id = $_POST['doctor_id'];
    $treating_date = $_POST['treating_date'];
    $details = trim($_POST['details']);
    if ($admission_id && $doctor_id && $treating_date && $details) {
        $sql = "INSERT INTO treatment (admission_id, doctor_id, treating_date, details)
                VALUES (?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([$admission_id, $doctor_id, $treating_date, $details]);
        $message = "New treatment added successfully!";
    } else {
        $message = "Please fill all fields!";
    }
}

// ———————————————— 4. DELETE TREATMENT ————————————————
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM treatment WHERE treatment_id = ?")->execute([$id]);
    $url = "treatments.php";
    if (!empty($_GET['search'])) $url .= "?search=" . urlencode($_GET['search']);
    header("Location: $url");
    exit;
}

// ———————————————— 5. SEARCH & LIST TREATMENTS ————————————————
$search = isset($_GET['search']) ? trim($_GET['search']) : "";

// CHANGED: Now oldest first → newest last (so new entries appear at the bottom)
$sql = "SELECT t.*,
               p.first_name AS p_first, p.last_name AS p_last,
               d.first_name AS d_first, d.last_name AS d_last,
               a.room_number
        FROM treatment t
        LEFT JOIN admission a ON t.admission_id = a.admission_id
        LEFT JOIN patient p ON a.patient_id = p.patient_id
        LEFT JOIN doctor d ON t.doctor_id = d.doctor_id
        WHERE p.first_name LIKE ? OR p.last_name LIKE ?
           OR d.first_name LIKE ? OR d.last_name LIKE ?
           OR t.details LIKE ?
        ORDER BY t.treating_date ASC, t.treatment_id ASC";  // ← This is the fix!

$stmt = $pdo->prepare($sql);
$like = "%$search%";
$stmt->execute([$like, $like, $like, $like, $like]);
$treatments = $stmt->fetchAll();

// Dropdowns
$admissions = $pdo->query("
    SELECT a.admission_id, p.first_name, p.last_name, a.room_number
    FROM admission a
    JOIN patient p ON a.patient_id = p.patient_id
    WHERE a.discharge_date IS NULL
    ORDER BY p.first_name
")->fetchAll();
$doctors = $pdo->query("SELECT doctor_id, first_name, last_name FROM doctor ORDER BY first_name")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manage Treatments</title>
    <style>
        body {font-family: Arial;background: #f0f8ff;margin: 40px;line-height: 1.6;}
        h1,h2 {color: #0066cc;}
        input,select,textarea,button,.btn{padding:10px 16px;margin:5px;font-size:16px;border-radius:5px;}
        input,select,textarea{border:1px solid #99ccff;width:320px;}
        button,.btn{background:#0066cc;color:white;border:none;cursor:pointer;text-decoration:none;display:inline-block;}
        button:hover,.btn:hover{background:#004499;}
        .edit-btn{background:#f0ad4e;}.edit-btn:hover{background:#ec971f;}
        .delete-btn{background:#d9534f;}.delete-btn:hover{background:#c9302c;}
        .cancel-btn{background:#888;}
        table{width:100%;border-collapse:collapse;margin:20px 0;}
        th,td{border:1px solid #99ccff;padding:12px;text-align:left;}
        th{background:#0066cc;color:white;}
        tr:nth-child(even){background:#f9f9ff;}
        .msg{padding:15px;margin:15px 0;border-radius:5px;font-weight:bold;}
        .success{background:#d4edda;color:#155724;border:1px solid #c3e6cb;}
        .error{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;}
    </style>
</head>
<body>
<h1>Manage Treatments</h1>
<p>
    <a href="index.php">Home</a> |
    <a href="patients.php">Patients</a> |
    <a href="doctors.php">Doctors</a> |
    <a href="admissions.php">Admissions</a> |
    <a href="treatments.php">Treatments</a>
</p>
<hr>
<!-- Search -->
<form method="GET">
    <input type="text" name="search" placeholder="Search by patient, doctor, or details..."
           value="<?= htmlspecialchars($search) ?>" size="60">
    <button type="submit">Search</button>
    <?php if ($search): ?>
        <a href="treatments.php"><button type="button">Clear</button></a>
    <?php endif; ?>
</form>

<!-- Message -->
<?php if ($message): ?>
    <div class="msg <?= strpos($message, 'success') !== false ? 'success' : 'error' ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<!-- Add or Edit Form -->
<h2><?= $edit_mode ? "Edit Treatment" : "Add New Treatment" ?></h2>
<form method="POST">
    <select name="admission_id" required>
        <option value="">Select Admitted Patient</option>
        <?php foreach($admissions as $a): ?>
            <option value="<?= $a['admission_id'] ?>"
                <?= $edit_mode && $edit_treatment['admission_id']==$a['admission_id'] ? 'selected' : '' ?>>
                <?= $a['first_name'] ?> <?= $a['last_name'] ?> (Room <?= $a['room_number'] ?>)
            </option>
        <?php endforeach; ?>
    </select>
    <select name="doctor_id" required>
        <option value="">Select Doctor</option>
        <?php foreach($doctors as $d): ?>
            <option value="<?= $d['doctor_id'] ?>"
                <?= $edit_mode && $edit_treatment['doctor_id']==$d['doctor_id'] ? 'selected' : '' ?>>
                Dr. <?= $d['first_name'] ?> <?= $d['last_name'] ?>
            </option>
        <?php endforeach; ?>
    </select>
    <input type="date" name="treating_date"
           value="<?= $edit_mode ? $edit_treatment['treating_date'] : date('Y-m-d') ?>" required>
    <textarea name="details" placeholder="Treatment details" required><?= $edit_mode ? htmlspecialchars($edit_treatment['details']) : '' ?></textarea>
    <?php if ($edit_mode): ?>
        <input type="hidden" name="id" value="<?= $edit_treatment['treatment_id'] ?>">
        <button type="submit" name="update">Update Treatment</button>
        <a href="treatments.php<?= $search ? '?search='.urlencode($search) : '' ?>" class="btn cancel-btn">Cancel</a>
    <?php else: ?>
        <button type="submit" name="add">Add New</button>
    <?php endif; ?>
</form>

<hr>
<h2>All Treatments (<?= count($treatments) ?>)</h2>
<?php if (empty($treatments)): ?>
    <p>No treatments found.</p>
<?php else: ?>
    <table>
        <tr>
            <th>No.</th>
            <th>Patient</th>
            <th>Doctor</th>
            <th>Treating Date</th>
            <th>Details</th>
            <th>Actions</th>
        </tr>
        <?php $no = 1; foreach ($treatments as $t): ?>
        <tr>
            <td><strong><?= $no++ ?></strong></td>
            <td><strong><?= htmlspecialchars($t['p_first'] . ' ' . $t['p_last']) ?></strong></td>
            <td>Dr. <?= htmlspecialchars($t['d_first'] . ' ' . $t['d_last']) ?></td>
            <td><?= date('d-m-Y', strtotime($t['treating_date'])) ?></td>
            <td><?= htmlspecialchars($t['details']) ?></td>
            <td>
                <a href="?edit=<?= $t['treatment_id'] ?>&search=<?= urlencode($search) ?>" class="btn edit-btn">Edit</a>
                <a href="?delete=<?= $t['treatment_id'] ?>&search=<?= urlencode($search) ?>"
                   onclick="return confirm('Delete this treatment permanently?')"
                   class="btn delete-btn">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<br><br>
<a href="index.php">Back to Home</a>
</body>
</html>