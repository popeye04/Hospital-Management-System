<?php
require 'db.php';

$message = "";
$edit_mode = false;
$edit_patient = null;

// ———————————————— 1. EDIT MODE ————————————————
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM patient WHERE patient_id = ?");
    $stmt->execute([$id]);
    $edit_patient = $stmt->fetch();
    if ($edit_patient) $edit_mode = true;
}

// ———————————————— 2. UPDATE PATIENT ————————————————
if (isset($_POST['update'])) {
    $id            = (int)$_POST['id'];
    $first_name    = trim($_POST['first_name']);
    $last_name     = trim($_POST['last_name']);
    $date_of_birth = $_POST['date_of_birth'];
    $gender        = $_POST['gender'];
    $blood_group   = $_POST['blood_group'];
    $phone         = trim($_POST['phone']);
    $email         = trim($_POST['email'] ?? '');
    $address       = trim($_POST['address'] ?? '');

    if ($first_name && $last_name && $date_of_birth && $gender && $blood_group && $phone) {
        $sql = "UPDATE patient SET 
                first_name=?, last_name=?, date_of_birth=?, gender=?, blood_group=?, 
                phone=?, email=?, address=? WHERE patient_id=?";
        $pdo->prepare($sql)->execute([$first_name, $last_name, $date_of_birth, $gender, $blood_group, $phone, $email, $address, $id]);
        $message = "Patient updated successfully!";
        $edit_mode = false;
    } else {
        $message = "Please fill all required fields!";
    }
}

// ———————————————— 3. ADD NEW PATIENT ————————————————
if (isset($_POST['add']) && !$edit_mode) {
    $first_name    = trim($_POST['first_name']);
    $last_name     = trim($_POST['last_name']);
    $date_of_birth = $_POST['date_of_birth'];
    $gender        = $_POST['gender'];
    $blood_group   = $_POST['blood_group'];
    $phone         = trim($_POST['phone']);
    $email         = trim($_POST['email'] ?? '');
    $address       = trim($_POST['address'] ?? '');

    if ($first_name && $last_name && $date_of_birth && $gender && $blood_group && $phone) {
        $sql = "INSERT INTO patient 
                (first_name, last_name, date_of_birth, gender, blood_group, phone, email, address)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([$first_name, $last_name, $date_of_birth, $gender, $blood_group, $phone, $email, $address]);
        $message = "New patient added successfully!";
    } else {
        $message = "Please fill all required fields!";
    }
}

// ———————————————— 4. DELETE PATIENT ————————————————
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM patient WHERE patient_id = ?")->execute([$id]);
    
    $url = "patients.php";
    if (!empty($_GET['search'])) $url .= "?search=" . urlencode($_GET['search']);
    header("Location: $url");
    exit;
}

// ———————————————— 5. SEARCH & LIST PATIENTS ————————————————
$search = isset($_GET['search']) ? trim($_GET['search']) : "";

$sql = "SELECT * FROM patient 
        WHERE first_name LIKE ? 
           OR last_name LIKE ? 
           OR phone LIKE ? 
           OR email LIKE ?
        ORDER BY patient_id ASC";  // oldest first → newest at bottom
$stmt = $pdo->prepare($sql);
$stmt->execute(["%$search%", "%$search%", "%$search%", "%$search%"]);
$patients = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manage Patients</title>
    <style>
        body {font-family: Arial;background: #f0f8ff;margin: 40px;line-height: 1.6;}
        h1,h2 {color: #0066cc;}
        input,select,button,.btn{padding:10px 16px;margin:5px;font-size:16px;border-radius:5px;}
        input,select{border:1px solid #99ccff;width:260px;}
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

<h1>Manage Patients</h1>

<p>
    <a href="index.php">Home</a> |
    <a href="patients.php">Patients</a> |
    <a href="doctors.php">Doctors</a> |
    <a href="lab_tests.php">Lab Tests</a>
</p>
<hr>

<!-- Search -->
<form method="GET">
    <input type="text" name="search" placeholder="Search by name, phone, email..." 
           value="<?= htmlspecialchars($search) ?>" size="50">
    <button type="submit">Search</button>
    <?php if ($search): ?>
        <a href="patients.php"><button type="button">Clear</button></a>
    <?php endif; ?>
</form>

<!-- Success/Error Message -->
<?php if ($message): ?>
    <div class="msg <?= strpos($message, 'success') !== false ? 'success' : 'error' ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<!-- Add or Edit Form -->
<h2><?= $edit_mode ? "Edit Patient" : "Add New Patient" ?></h2>
<form method="POST">
    <input name="first_name" placeholder="First Name" value="<?= $edit_mode ? htmlspecialchars($edit_patient['first_name']) : '' ?>" required>
    <input name="last_name"  placeholder="Last Name"  value="<?= $edit_mode ? htmlspecialchars($edit_patient['last_name']) : '' ?>" required>
    <input type="date" name="date_of_birth" value="<?= $edit_mode ? $edit_patient['date_of_birth'] : '' ?>" required>
    
    <select name="gender" required>
        <option value="">Gender</option>
        <option <?= $edit_mode && $edit_patient['gender']=='M'?'selected':'' ?>>M</option>
        <option <?= $edit_mode && $edit_patient['gender']=='F'?'selected':'' ?>>F</option>
        <option <?= $edit_mode && $edit_patient['gender']=='Other'?'selected':'' ?>>Other</option>
    </select>

    <select name="blood_group" required>
        <option value="">Blood Group</option>
        <?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
            <option <?= $edit_mode && $edit_patient['blood_group']==$bg?'selected':'' ?>><?= $bg ?></option>
        <?php endforeach; ?>
    </select>

    <input name="phone" placeholder="Phone" value="<?= $edit_mode ? $edit_patient['phone'] : '' ?>" required>
    <input type="email" name="email" placeholder="Email (optional)" value="<?= $edit_mode ? ($edit_patient['email']??'') : '' ?>">
    <input name="address" placeholder="Address (optional)" value="<?= $edit_mode ? ($edit_patient['address']??'') : '' ?>">

    <?php if ($edit_mode): ?>
        <input type="hidden" name="id" value="<?= $edit_patient['patient_id'] ?>">
        <button type="submit" name="update">Update Patient</button>
        <a href="patients.php<?= $search ? '?search='.urlencode($search) : '' ?>" class="btn cancel-btn">Cancel</a>
    <?php else: ?>
        <button type="submit" name="add">Add Patient</button>
    <?php endif; ?>
</form>

<hr>

<h2>All Patients (<?= count($patients) ?>)</h2>

<?php if (empty($patients)): ?>
    <p>No patients found. Add one above!</p>
<?php else: ?>
    <table>
        <tr>
            <th>No.</th>           <!-- Perfect 1, 2, 3... -->
            <th>Full Name</th>
            <th>Date of Birth</th>
            <th>Gender</th>
            <th>Blood Group</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Address</th>
            <th>Actions</th>
        </tr>
        <?php $serial = 1; foreach ($patients as $p): ?>
        <tr>
            <td><strong><?= $serial++ ?></strong></td>
            <td><strong><?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?></strong></td>
            <td><?= date('d-m-Y', strtotime($p['date_of_birth'])) ?></td>
            <td><?= $p['gender'] ?></td>
            <td><?= $p['blood_group'] ?></td>
            <td><?= htmlspecialchars($p['phone']) ?></td>
            <td><?= htmlspecialchars($p['email'] ?? '') ?></td>
            <td><?= htmlspecialchars($p['address'] ?? '') ?></td>
            <td>
                <a href="?edit=<?= $p['patient_id'] ?>&search=<?= urlencode($search) ?>" class="btn edit-btn">Edit</a>
                <a href="?delete=<?= $p['patient_id'] ?>&search=<?= urlencode($search) ?>"
                   onclick="return confirm('Delete this patient forever?')"
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