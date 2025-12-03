<?php
require 'db.php';
$message = "";

// ———————— EDIT MODE ————————
$edit = false;
$edit_data = null;

if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM appointment WHERE appointment_id = ?");
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch();
    if ($edit_data) $edit = true;
}

// ———————— UPDATE APPOINTMENT ————————
if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $datetime = $_POST['appointment_datetime'];
    $reason = $_POST['reason'];
    $status = $_POST['status'];

    $sql = "UPDATE appointment SET patient_id=?, doctor_id=?, appointment_datetime=?, reason=?, status=? WHERE appointment_id=?";
    $pdo->prepare($sql)->execute([$patient_id, $doctor_id, $datetime, $reason, $status, $id]);
    $message = "Appointment updated successfully!";
    $edit = false;
}

// ———————— ADD NEW APPOINTMENT ————————
if (isset($_POST['add']) && !$edit) {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $datetime = $_POST['appointment_datetime'];
    $reason = $_POST['reason'];
    $status = $_POST['status'];

    $sql = "INSERT INTO appointment (patient_id, doctor_id, appointment_datetime, reason, status) 
            VALUES (?, ?, ?, ?, ?)";
    $pdo->prepare($sql)->execute([$patient_id, $doctor_id, $datetime, $reason, $status]);
    $message = "Appointment scheduled successfully!";
}

// ———————— DELETE APPOINTMENT ————————
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM appointment WHERE appointment_id = ?")->execute([$id]);
    header("Location: appointments.php");
    exit;
}

// ———————— SEARCH ————————
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// ———————— GET ALL APPOINTMENTS (ID ascending → new ones at bottom) ————————
$sql = "SELECT a.*,
               p.first_name AS p_first, p.last_name AS p_last,
               d.first_name AS d_first, d.last_name AS d_last
        FROM appointment a
        LEFT JOIN patient p ON a.patient_id = p.patient_id
        LEFT JOIN doctor d ON a.doctor_id = d.doctor_id
        WHERE p.first_name LIKE ? OR p.last_name LIKE ?
           OR d.first_name LIKE ? OR d.last_name LIKE ?
           OR a.reason LIKE ?
        ORDER BY a.appointment_id ASC";

$stmt = $pdo->prepare($sql);
$like = "%$search%";
$stmt->execute([$like, $like, $like, $like, $like]);
$appointments = $stmt->fetchAll();

// Get patients and doctors
$patients = $pdo->query("SELECT patient_id, first_name, last_name FROM patient ORDER BY first_name")->fetchAll();
$doctors  = $pdo->query("SELECT doctor_id, first_name, last_name FROM doctor ORDER BY first_name")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manage Appointments</title>
    <style>
        body {font-family: Arial; margin: 40px; background: #f9f9f9; color: #000;}
        h1, h2 {color: #0066cc;}
        a {color: #0066cc; text-decoration: none;}
        table {width: 100%; border-collapse: collapse; margin: 20px 0;}
        th, td {border: 1px solid #999; padding: 10px; text-align: left;}
        th {background: #0066cc; color: white;}
        tr:nth-child(even) {background: #f0f8ff;}
        input, select {padding: 8px; margin: 5px; width: 220px; border: 1px solid #999; border-radius: 4px;}
        button, .btn {padding: 8px 15px; background: #0066cc; color: white; border: none; border-radius: 4px; cursor: pointer; margin: 5px 2px;}
        .edit-btn   {background: #f0ad4e;}
        .delete-btn {background: #d9534f;}
        .cancel-btn {background: #888;}
        .msg {padding: 12px; margin: 15px 0; background: #d4edda; color: #155724; font-weight: bold;}
        .status-scheduled {color: #0066cc; font-weight: bold;}
        .status-completed {color: green; font-weight: bold;}
        .status-cancelled {color: red; font-weight: bold;}
    </style>
</head>
<body>

<h1>Manage Appointments</h1>
<a href="index.php">Home</a> | <a href="patients.php">Patients</a> | <a href="doctors.php">Doctors</a> | <a href="appointments.php">Appointments</a>
<hr>

<!-- Search -->
<form method="GET">
    <input type="text" name="search" placeholder="Search by name or reason..." value="<?= htmlspecialchars($search) ?>" size="60">
    <button type="submit">Search</button>
    <?php if($search): ?><a href="appointments.php"><button type="button">Clear</button></a><?php endif; ?>
</form>

<?php if($message): ?>
    <div class="msg">Success: <?= $message ?></div>
<?php endif; ?>

<h2><?= $edit ? "Edit Appointment" : "Schedule New Appointment" ?></h2>
<form method="POST">
    <select name="patient_id" required>
        <option value="">Select Patient</option>
        <?php foreach($patients as $p): ?>
            <option value="<?= $p['patient_id'] ?>" <?= ($edit && $edit_data['patient_id']==$p['patient_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($p['first_name'].' '.$p['last_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="doctor_id" required>
        <option value="">Select Doctor</option>
        <?php foreach($doctors as $d): ?>
            <option value="<?= $d['doctor_id'] ?>" <?= ($edit && $edit_data['doctor_id']==$d['doctor_id']) ? 'selected' : '' ?>>
                Dr. <?= htmlspecialchars($d['first_name'].' '.$d['last_name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <input type="datetime-local" name="appointment_datetime" 
           value="<?= $edit ? str_replace(' ', 'T', $edit_data['appointment_datetime']) : '' ?>" required>
    <input type="text" name="reason" placeholder="Reason" 
           value="<?= $edit ? htmlspecialchars($edit_data['reason']) : '' ?>" required><br>

    <select name="status" required>
        <option value="Scheduled" <?= ($edit && $edit_data['status']=='Scheduled') ? 'selected' : '' ?>>Scheduled</option>
        <option value="Completed" <?= ($edit && $edit_data['status']=='Completed') ? 'selected' : '' ?>>Completed</option>
        <option value="Cancelled" <?= ($edit && $edit_data['status']=='Cancelled') ? 'selected' : '' ?>>Cancelled</option>
    </select>

    <?php if($edit): ?>
        <input type="hidden" name="id" value="<?= $edit_data['appointment_id'] ?>">
        <button type="submit" name="update">Update</button>
        <a href="appointments.php"><button type="button" class="cancel-btn">Cancel</button></a>
    <?php else: ?>
        <button type="submit" name="add">Schedule</button>
    <?php endif; ?>
</form>

<hr>
<h2>All Appointments (<?= count($appointments) ?>)</h2>
<?php if(empty($appointments)): ?>
    <p>No appointments found.</p>
<?php else: ?>
<table>
    <tr>
        <th>ID</th><th>Patient</th><th>Doctor</th><th>Date & Time</th><th>Reason</th><th>Status</th><th>Action</th>
    </tr>
    <?php foreach($appointments as $a): ?>
    <tr>
        <td><strong><?= $a['appointment_id'] ?></strong></td>
        <td><?= htmlspecialchars($a['p_first'].' '.$a['p_last']) ?></td>
        <td>Dr. <?= htmlspecialchars($a['d_first'].' '.$a['d_last']) ?></td>
        <td><?= date('d-m-Y h:i A', strtotime($a['appointment_datetime'])) ?></td>
        <td><?= htmlspecialchars($a['reason']) ?></td>
        <td class="status-<?= strtolower($a['status']) ?>"><?= $a['status'] ?></td>
        <td>
            <a href="?edit=<?= $a['appointment_id'] ?>" class="btn edit-btn">Edit</a>
            <a href="?delete=<?= $a['appointment_id'] ?>" onclick="return confirm('Delete permanently?')" class="btn delete-btn">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<br><br>
<a href="index.php">Back to Home</a>
</body>
</html>