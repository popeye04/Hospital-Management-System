<?php
require 'db.php';

$message = "";
$edit_mode = false;
$edit_doctor = null;

// ———————————————— 1. EDIT MODE ————————————————
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM doctor WHERE doctor_id = ?");
    $stmt->execute([$id]);
    $edit_doctor = $stmt->fetch();
    if ($edit_doctor) $edit_mode = true;
}

// ———————————————— 2. UPDATE DOCTOR ————————————————
if (isset($_POST['update'])) {
    $id         = (int)$_POST['id'];
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $gender     = $_POST['gender'];
    $dob        = $_POST['dob'];
    $phone      = trim($_POST['phone']);
    $email      = trim($_POST['email'] ?? '');
    $address    = trim($_POST['address'] ?? '');
    $specialty  = trim($_POST['specialty']);
    $dept_id    = (int)$_POST['department_id'];

    if ($first_name && $last_name && $gender && $dob && $phone && $specialty && $dept_id) {
        $sql = "UPDATE doctor SET 
                first_name=?, last_name=?, gender=?, dob=?, phone=?, email=?, address=?, specialty=?, department_id=?
                WHERE doctor_id=?";
        $pdo->prepare($sql)->execute([$first_name, $last_name, $gender, $dob, $phone, $email, $address, $specialty, $dept_id, $id]);
        $message = "Doctor updated successfully!";
        $edit_mode = false;
    } else {
        $message = "Please fill all required fields!";
    }
}

// ———————————————— 3. ADD NEW DOCTOR ————————————————
if (isset($_POST['add']) && !$edit_mode) {
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $gender     = $_POST['gender'];
    $dob        = $_POST['dob'];
    $phone      = trim($_POST['phone']);
    $email      = trim($_POST['email'] ?? '');
    $address    = trim($_POST['address'] ?? '');
    $specialty  = trim($_POST['specialty']);
    $dept_id    = (int)$_POST['department_id'];

    if ($first_name && $last_name && $gender && $dob && $phone && $specialty && $dept_id) {
        $sql = "INSERT INTO doctor 
                (first_name, last_name, gender, dob, phone, email, address, specialty, department_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([$first_name, $last_name, $gender, $dob, $phone, $email, $address, $specialty, $dept_id]);
        $message = "New doctor added successfully!";
    } else {
        $message = "Please fill all required fields!";
    }
}

// ———————————————— 4. DELETE DOCTOR ————————————————
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM doctor WHERE doctor_id = ?")->execute([$id]);
    $url = "doctors.php" . (!empty($_GET['search']) ? "?search=" . urlencode($_GET['search']) : "");
    header("Location: $url");
    exit;
}

// ———————————————— 5. SEARCH & LIST DOCTORS ————————————————
$search = isset($_GET['search']) ? trim($_GET['search']) : "";

// Fixed column name → department_name
$sql = "SELECT d.*, dep.department_name 
        FROM doctor d
        LEFT JOIN department dep ON d.department_id = dep.department_id
        WHERE d.first_name LIKE ? 
           OR d.last_name LIKE ? 
           OR d.phone LIKE ? 
           OR d.email LIKE ? 
           OR d.specialty LIKE ?
        ORDER BY d.doctor_id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute(["%$search%","%$search%","%$search%","%$search%","%$search%"]);
$doctors = $stmt->fetchAll();

// Get departments for dropdown
$departments = $pdo->query("SELECT department_id, department_name FROM department ORDER BY department_name")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manage Doctors</title>
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

<h1>Manage Doctors</h1>
<p>
    <a href="index.php">Home</a> |
    <a href="patients.php">Patients</a> |
    <a href="doctors.php">Doctors</a>
</p>
<hr>

<form method="GET">
    <input type="text" name="search" placeholder="Search doctor..." 
           value="<?=htmlspecialchars($search)?>" size="50">
    <button type="submit">Search</button>
    <?php if($search): ?><a href="doctors.php"><button type="button">Clear</button></a><?php endif; ?>
</form>

<?php if($message): ?>
    <div class="msg <?=strpos($message,'success')!==false?'success':'error'?>">
        <?=htmlspecialchars($message)?>
    </div>
<?php endif; ?>

<h2><?= $edit_mode ? "Edit Doctor" : "Add New Doctor" ?></h2>
<form method="POST">
    <input name="first_name" placeholder="First Name" value="<?= $edit_mode?htmlspecialchars($edit_doctor['first_name']):'' ?>" required>
    <input name="last_name"  placeholder="Last Name"  value="<?= $edit_mode?htmlspecialchars($edit_doctor['last_name']):'' ?>" required>
    <select name="gender" required>
        <option value="">Gender</option>
        <option <?= $edit_mode && $edit_doctor['gender']=='M'?'selected':'' ?>>M</option>
        <option <?= $edit_mode && $edit_doctor['gender']=='F'?'selected':'' ?>>F</option>
        <option <?= $edit_mode && $edit_doctor['gender']=='Other'?'selected':'' ?>>Other</option>
    </select>
    <input type="date" name="dob" value="<?= $edit_mode?$edit_doctor['dob']:'' ?>" required>
    <input name="phone" placeholder="Phone" value="<?= $edit_mode?$edit_doctor['phone']:'' ?>" required>
    <input type="email" name="email" placeholder="Email (optional)" value="<?= $edit_mode?$edit_doctor['email']:'' ?>">
    <input name="address" placeholder="Address (optional)" value="<?= $edit_mode?$edit_doctor['address']:'' ?>">
    <input name="specialty" placeholder="Specialty" value="<?= $edit_mode?$edit_doctor['specialty']:'' ?>" required>

    <select name="department_id" required>
        <option value="">Select Department</option>
        <?php foreach($departments as $d): ?>
            <option value="<?= $d['department_id'] ?>"
                <?= $edit_mode && $edit_doctor['department_id']==$d['department_id']?'selected':'' ?>>
                <?= htmlspecialchars($d['department_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <?php if($edit_mode): ?>
        <input type="hidden" name="id" value="<?= $edit_doctor['doctor_id'] ?>">
        <button type="submit" name="update">Update Doctor</button>
        <a href="doctors.php<?= $search?'?search='.urlencode($search):'' ?>" class="btn cancel-btn">Cancel</a>
    <?php else: ?>
        <button type="submit" name="add">Add Doctor</button>
    <?php endif; ?>
</form>

<hr>
<h2>All Doctors (<?= count($doctors) ?>)</h2>

<?php if(empty($doctors)): ?>
    <p>No doctors found.</p>
<?php else: ?>
    <table>
        <tr>
            <th>No.</th>
            <th>Full Name</th>
            <th>Gender</th>
            <th>Date of Birth</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Specialty</th>
            <th>Department</th>
            <th>Actions</th>
        </tr>
        <?php $serial = 1; foreach($doctors as $d): ?>
        <tr>
            <td><strong><?= $serial++ ?></strong></td>
            <td><strong><?= htmlspecialchars($d['first_name'].' '.$d['last_name']) ?></strong></td>
            <td><?= $d['gender'] ?></td>
            <td><?= date('d-m-Y', strtotime($d['dob'])) ?></td>
            <td><?= htmlspecialchars($d['phone']) ?></td>
            <td><?= htmlspecialchars($d['email'] ?? '') ?></td>
            <td><?= htmlspecialchars($d['specialty']) ?></td>
            <td><?= htmlspecialchars($d['department_name'] ?? '—') ?></td>
            <td>
                <a href="?edit=<?= $d['doctor_id'] ?>&search=<?= urlencode($search) ?>" class="btn edit-btn">Edit</a>
                <a href="?delete=<?= $d['doctor_id'] ?>&search=<?= urlencode($search) ?>"
                   onclick="return confirm('Delete Dr. <?= addslashes($d['first_name'].' '.$d['last_name']) ?> forever?')"
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