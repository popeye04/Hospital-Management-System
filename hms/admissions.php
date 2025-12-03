<?php
require 'db.php';

$message = "";
$edit_mode = false;
$edit_data = null;

// Load record for editing
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT a.*, p.first_name, p.last_name 
                           FROM admission a 
                           JOIN patient p ON a.patient_id = p.patient_id 
                           WHERE a.admission_id = ?");
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch();
    if ($edit_data) $edit_mode = true;
}

// Update existing admission
if (isset($_POST['update'])) {
    $id             = (int)$_POST['id'];
    $patient_id     = (int)$_POST['patient_id'];
    $room           = $_POST['room_number'];
    $admit_date     = $_POST['admission_date'];
    $discharge_date = $_POST['discharge_date'] ?: null;
    $diagnosis      = trim($_POST['diagnosis']);

    $sql = "UPDATE admission SET 
            patient_id=?, room_number=?, admission_date=?, discharge_date=?, diagnosis=?
            WHERE admission_id=?";
    $pdo->prepare($sql)->execute([$patient_id, $room, $admit_date, $discharge_date, $diagnosis, $id]);
    $message = "Admission updated successfully!";
    $edit_mode = false;
}

// Add new admission
if (isset($_POST['add']) && !$edit_mode) {
    $patient_id     = (int)$_POST['patient_id'];
    $room           = $_POST['room_number'];
    $admit_date     = $_POST['admission_date'];
    $discharge_date = $_POST['discharge_date'] ?: null;
    $diagnosis      = trim($_POST['diagnosis']);

    $sql = "INSERT INTO admission (patient_id, room_number, admission_date, discharge_date, diagnosis)
            VALUES (?, ?, ?, ?, ?)";
    $pdo->prepare($sql)->execute([$patient_id, $room, $admit_date, $discharge_date, $diagnosis]);
    $message = "Patient admitted successfully!";
}

// Delete admission
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM admission WHERE admission_id = ?")->execute([$id]);
    $url = "admissions.php";
    if (!empty($_GET['search'])) $url .= "?search=" . urlencode($_GET['search']);
    header("Location: $url");
    exit;
}

// Search & list all admissions
$search = trim($_GET['search'] ?? '');

$sql = "SELECT a.*, p.first_name, p.last_name, r.room_type
        FROM admission a
        LEFT JOIN patient p ON a.patient_id = p.patient_id
        LEFT JOIN room r ON a.room_number = r.room_number
        WHERE p.first_name LIKE ? OR p.last_name LIKE ? 
           OR a.room_number LIKE ? OR a.diagnosis LIKE ?
        ORDER BY a.admission_id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute(["%$search%", "%$search%", "%$search%", "%$search%"]);
$admissions = $stmt->fetchAll();

// Dropdown data
$patients = $pdo->query("SELECT patient_id, first_name, last_name FROM patient ORDER BY first_name")->fetchAll();
$rooms    = $pdo->query("SELECT room_number, room_type FROM room ORDER BY room_number")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manage Patient Admissions</title>
    <style>
        body {font-family: Arial;background: #f0f8ff;margin: 40px;line-height: 1.6;}
        h1,h2 {color: #0066cc;}
        input,select,button,.btn{padding:10px 16px;margin:5px;font-size:16px;border-radius:5px;}
        input,select{border:1px solid #99ccff;width:300px;}
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
        .status-admitted{background:#fff3cd;color:#856404;padding:5px 10px;border-radius:4px;}
        .status-discharged{background:#d4edda;color:#155724;padding:5px 10px;border-radius:4px;}
    </style>
</head>
<body>

<h1>Manage Patient Admissions</h1>
<p>
    <a href="index.php">Home</a> |
    <a href="patients.php">Patients</a> |
    <a href="doctors.php">Doctors</a> |
    <a href="admissions.php">Admissions</a>
</p>
<hr>

<form method="GET">
    <input type="text" name="search" placeholder="Search patient, room, diagnosis..." 
           value="<?=htmlspecialchars($search)?>" size="60">
    <button type="submit">Search</button>
    <?php if($search): ?>
        <a href="admissions.php"><button type="button">Clear</button></a>
    <?php endif; ?>
</form>

<?php if($message): ?>
    <div class="msg <?=strpos($message,'success')!==false?'success':'error'?>">
        <?=htmlspecialchars($message)?>
    </div>
<?php endif; ?>

<h2><?= $edit_mode ? "Edit Admission" : "Admit New Patient" ?></h2>
<form method="POST">
    <select name="patient_id" required>
        <option value="">Select Patient</option>
        <?php foreach($patients as $p): ?>
            <option value="<?= $p['patient_id'] ?>"
                <?= $edit_mode && $edit_data['patient_id']==$p['patient_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($p['first_name'].' '.$p['last_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="room_number" required>
        <option value="">Select Room</option>
        <?php foreach($rooms as $r): ?>
            <option value="<?= $r['room_number'] ?>"
                <?= $edit_mode && $edit_data['room_number']==$r['room_number'] ? 'selected' : '' ?>>
                Room <?= $r['room_number'] ?> (<?= $r['room_type'] ?>)
            </option>
        <?php endforeach; ?>
    </select>

    <input type="date" name="admission_date" 
           value="<?= $edit_mode ? $edit_data['admission_date'] : date('Y-m-d') ?>" required>

    <input type="date" name="discharge_date" 
           value="<?= $edit_mode && $edit_data['discharge_date'] && $edit_data['discharge_date']!='0000-00-00' ? $edit_data['discharge_date'] : '' ?>">

    <input type="text" name="diagnosis" placeholder="Diagnosis" 
           value="<?= $edit_mode ? htmlspecialchars($edit_data['diagnosis']) : '' ?>" required>

    <?php if($edit_mode): ?>
        <input type="hidden" name="id" value="<?= $edit_data['admission_id'] ?>">
        <button type="submit" name="update">Update Admission</button>
        <a href="admissions.php<?= $search?'?search='.urlencode($search):'' ?>" class="btn cancel-btn">Cancel</a>
    <?php else: ?>
        <button type="submit" name="add">Admit Patient</button>
    <?php endif; ?>
</form>

<hr>

<h2>All Admissions (<?= count($admissions) ?>)</h2>

<?php if(empty($admissions)): ?>
    <p>No admissions found.</p>
<?php else: ?>
<table>
    <tr>
        <th>No.</th>
        <th>Patient Name</th>
        <th>Room</th>
        <th>Admitted</th>
        <th>Discharged</th>
        <th>Diagnosis</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php $no = 1; foreach($admissions as $a): ?>
    <tr>
        <td><strong><?= $no++ ?></strong></td>
        <td><strong><?= htmlspecialchars($a['first_name'].' '.$a['last_name']) ?></strong></td>
        <td>Room <?= $a['room_number'] ?> (<?= $a['room_type'] ?? 'Standard' ?>)</td>
        <td><?= date('d-m-Y', strtotime($a['admission_date'])) ?></td>
        <td><?= $a['discharge_date'] && $a['discharge_date']!='0000-00-00' ? date('d-m-Y', strtotime($a['discharge_date'])) : '—' ?></td>
        <td><?= htmlspecialchars($a['diagnosis']) ?></td>
        <td>
            <?php if(!$a['discharge_date'] || $a['discharge_date']==='0000-00-00'): ?>
                <span class="status-admitted">Admitted</span>
            <?php else: ?>
                <span class="status-discharged">Discharged</span>
            <?php endif; ?>
        </td>
        <td>
            <a href="?edit=<?= $a['admission_id'] ?>&search=<?= urlencode($search) ?>" class="btn edit-btn">Edit</a>
            <a href="?delete=<?= $a['admission_id'] ?>&search=<?= urlencode($search) ?>" 
               onclick="return confirm('Delete forever?')" class="btn delete-btn">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<br><br>
<a href="index.php">Back to Home</a>

</body>
</html>