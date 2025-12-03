<?php
require 'db.php';

// ---------------------------
// EDIT MODE
// ---------------------------
$edit = false;
$editData = null;

if (isset($_GET['edit'])) {
    $edit = true;
    $ward_to_edit = $_GET['edit'];

    $stmt = $pdo->prepare("SELECT * FROM ward WHERE ward_id = ?");
    $stmt->execute([$ward_to_edit]);
    $editData = $stmt->fetch();

    if (!$editData) {
        $edit = false;
    }
}

// ---------------------------
// UPDATE WARD
// ---------------------------
if (isset($_POST['update'])) {
    $ward_id       = $_POST['ward_id'];
    $ward_name     = $_POST['ward_name'];
    $department_id = $_POST['department_id'];
    $total_beds    = $_POST['total_beds'];

    $sql = "UPDATE ward
            SET ward_name = ?, department_id = ?, total_beds = ?
            WHERE ward_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$ward_name, $department_id, $total_beds, $ward_id]);

    header("Location: wards.php");
    exit;
}

// ---------------------------
// ADD NEW WARD
// ---------------------------
if (isset($_POST['add']) && !$edit) {
    $ward_name     = $_POST['ward_name'];
    $department_id = $_POST['department_id'];
    $total_beds    = $_POST['total_beds'];

    $sql = "INSERT INTO ward (ward_name, department_id, total_beds)
            VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$ward_name, $department_id, $total_beds]);

    header("Location: wards.php");
    exit;
}

// ---------------------------
// DELETE
// ---------------------------
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $stmt = $pdo->prepare("DELETE FROM ward WHERE ward_id = ?");
    $stmt->execute([$id]);

    header("Location: wards.php");
    exit;
}

// ---------------------------
// SEARCH
// ---------------------------
$search = isset($_GET['search']) ? $_GET['search'] : "";

$sql = "SELECT w.*, d.department_name
        FROM ward w
        LEFT JOIN department d ON w.department_id = d.department_id
        WHERE w.ward_name LIKE ?
           OR d.department_name LIKE ?
        ORDER BY w.ward_id ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute(["%$search%", "%$search%"]);
$wards = $stmt->fetchAll();

// department list
$departments = $pdo->query("SELECT department_id, department_name FROM department ORDER BY department_name")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Wards</title>
    <style>
        body { font-family: Arial; margin: 40px; background: #f9f9f9; color:#000; }
        h1, h2 { color:#0066cc; }
        table { width:100%; border-collapse:collapse; margin-top:20px; }
        th, td { border:1px solid #999; padding:10px; }
        th { background:#0066cc; color:white; }
        input, select { padding:8px; margin:5px; width:200px; border:1px solid #999; border-radius:4px; }
        button, .btn { padding:8px 15px; background:#0066cc; color:white; 
                       border:none; border-radius:4px; cursor:pointer; text-decoration:none; }
        .delete-btn { background:#d9534f; }
        .edit-btn { background:#f0ad4e; color:#000; }
        .cancel-btn { background:gray; }
        .menu a { margin-right:20px; text-decoration:none; color:#0066cc; font-size:18px; }
    </style>
</head>
<body>

<h1>Manage Wards</h1>

<div class="menu">
    <a href="index.php">Home</a> |
    <a href="patients.php">Patients</a> |
    <a href="doctors.php">Doctors</a> |
    <a href="staff.php">Staff</a> |
    <a href="departments.php">Departments</a> |
    <a href="wards.php">Wards</a> |
    <a href="rooms.php">Rooms</a>
</div>

<hr>

<!-- SEARCH -->
<form method="GET">
    <input type="text" name="search" placeholder="Search ward or department..."
           value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search</button>

    <?php if ($search): ?>
        <a href="wards.php" class="btn cancel-btn">Clear</a>
    <?php endif; ?>
</form>

<hr>

<h2><?= $edit ? "Edit Ward" : "Add New Ward" ?></h2>

<form method="POST">

    <!-- ID only used when editing -->
    <?php if ($edit): ?>
        <input type="hidden" name="ward_id" value="<?= $editData['ward_id'] ?>">
    <?php endif; ?>

    <input type="text" name="ward_name" placeholder="Ward Name"
        value="<?= $edit ? htmlspecialchars($editData['ward_name']) : "" ?>" required>

    <select name="department_id" required>
        <option value="">Select Department</option>
        <?php foreach ($departments as $d): ?>
            <option value="<?= $d['department_id'] ?>"
                <?= $edit && $editData['department_id'] == $d['department_id'] ? "selected" : "" ?>>
                <?= htmlspecialchars($d['department_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input type="number" name="total_beds" placeholder="Total Beds"
        value="<?= $edit ? htmlspecialchars($editData['total_beds']) : "" ?>" required>

    <?php if ($edit): ?>
        <button type="submit" name="update">Update Ward</button>
        <a href="wards.php" class="btn cancel-btn">Cancel</a>
    <?php else: ?>
        <button type="submit" name="add">Add Ward</button>
    <?php endif; ?>
</form>

<hr>

<h2>All Wards (<?= count($wards) ?>)</h2>

<?php if (count($wards) == 0): ?>
    <p>No wards found. Add one above!</p>
<?php else: ?>
<table>
    <tr>
        <th>ID</th>
        <th>Ward Name</th>
        <th>Department</th>
        <th>Total Beds</th>
        <th>Action</th>
    </tr>

    <?php foreach ($wards as $w): ?>
    <tr>
        <td><?= $w['ward_id'] ?></td>
        <td><?= htmlspecialchars($w['ward_name']) ?></td>
        <td><?= htmlspecialchars($w['department_name'] ?? '—') ?></td>
        <td><?= $w['total_beds'] ?></td>
        <td>
            <a href="wards.php?edit=<?= $w['ward_id'] ?>" class="btn edit-btn">Edit</a>
            <a href="wards.php?delete=<?= $w['ward_id'] ?>"
               class="btn delete-btn"
               onclick="return confirm('Delete this ward permanently?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<br><br>
<a href="index.php">Back to Homepage</a>

</body>
</html>
