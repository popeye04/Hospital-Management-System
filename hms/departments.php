<?php require 'db.php';

// ADD NEW DEPARTMENT
if (isset($_POST['add'])) {
    $department_name = trim($_POST['department_name']);
    $location        = trim($_POST['location']);
    $next_id = $pdo->query("SELECT COALESCE(MAX(department_id),0)+1 FROM department")->fetchColumn();
    $sql = "INSERT INTO department (department_id, department_name, location) VALUES (?, ?, ?)";
    $pdo->prepare($sql)->execute([$next_id, $department_name, $location]);
    header("Location: departments.php"); exit;
}

// UPDATE
if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $sql = "UPDATE department SET department_name=?, location=? WHERE department_id=?";
    $pdo->prepare($sql)->execute([trim($_POST['department_name']), trim($_POST['location']), $id]);
    header("Location: departments.php"); exit;
}

// DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try { $pdo->prepare("DELETE FROM department WHERE department_id=?")->execute([$id]); }
    catch (Exception $e) { $msg = "Cannot delete — doctors may be assigned!"; }
    header("Location: departments.php" . (isset($msg) ? "?msg=" . urlencode($msg) : "")); exit;
}

// EDIT
$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM department WHERE department_id=?");
    $stmt->execute([$_GET['edit']]);
    $edit = $stmt->fetch();
}

// SEARCH + LIST
$search = $_GET['search'] ?? '';
$sql = "SELECT * FROM department WHERE department_name LIKE ? OR location LIKE ? ORDER BY department_id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute(["%$search%", "%$search%"]);
$departments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Departments</title>
    <meta charset="utf-8">
    <style>
        body {font-family:Arial;margin:40px;background:#f9f9f9;color:#333;}
        table {width:100%;max-width:900px;margin:20px 0;border-collapse:collapse;background:white;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
        th, td {padding:14px 12px;text-align:left;border-bottom:1px solid #ddd;}
        th {background:#007cba;color:white;font-weight:normal;}
        tr:hover {background:#f8fbff;}
        input, button, .btn {padding:11px 14px;margin:6px 0;border-radius:5px;border:none;font-size:15px;}
        input {width:100%;max-width:380px;border:1px solid #ccc;}
        button, .btn {background:#007cba;color:white;cursor:pointer;display:inline-block;text-decoration:none;}
        .btn-edit {background:#f0ad4e;}
        .btn-delete {background:#d9534f;}
        .btn-cancel {background:#888;}
        .form-row {display:flex;gap:15px;flex-wrap:wrap;margin:10px 0;}
        .form-row > * {flex:1;min-width:280px;}
        .msg {color:#d9534f;font-weight:bold;background:#ffe6e6;padding:12px;border-radius:5px;margin:10px 0;}
        hr {border:none;border-top:2px solid #eee;margin:35px 0;}
        h1, h2 {color:#007cba;}
        .menu a {margin-right:20px;color:#007cba;font-weight:bold;text-decoration:none;}
    </style>
</head>
<body>

<h1>Manage Departments</h1>
<div class="menu">
    <a href="index.php">Home</a> |
    <a href="patients.php">Patients</a> |
    <a href="doctors.php">Doctors</a> |
    <a href="departments.php">Departments</a>
</div>
<hr>

<?php if(isset($_GET['msg'])): ?><div class="msg"><?=htmlspecialchars($_GET['msg'])?></div><?php endif; ?>

<form method="GET" style="margin:20px 0;">
    <input type="text" name="search" placeholder="Search department name or location..." value="<?=htmlspecialchars($search)?>" style="width:400px;">
    <button type="submit">Search</button>
    <?php if($search): ?><a href="departments.php" class="btn btn-cancel">Clear</a><?php endif; ?>
</form>

<h2><?= $edit ? 'Edit Department' : 'Add New Department' ?></h2>
<form method="POST">
    <?php if($edit): ?><input type="hidden" name="id" value="<?= $edit['department_id'] ?>"><?php endif; ?>
    <div class="form-row">
        <input name="department_name" value="<?= $edit['department_name']??'' ?>" placeholder="Department Name (e.g. Cardiology)" required>
        <input name="location" value="<?= $edit['location']??'' ?>" placeholder="Location (e.g. Building A, Floor 3)" required>
    </div>
    <div>
        <button name="<?= $edit?'update':'add' ?>" style="width:180px;">
            <?= $edit?'Update':'Add' ?> Department
        </button>
        <?php if($edit): ?> <a href="departments.php" class="btn btn-cancel">Cancel</a> <?php endif; ?>
    </div>
</form>

<hr>

<h2>All Departments (<?= count($departments) ?>)</h2>
<table>
    <tr>
        <th width="80">ID</th>
        <th width="320">Department Name</th>
        <th width="280">Location</th>
        <th width="180">Action</th>
    </tr>
    <?php foreach($departments as $d): ?>
    <tr>
        <td><strong>#<?= $d['department_id'] ?></strong></td>
        <td><?= htmlspecialchars($d['department_name']) ?></td>
        <td><?= htmlspecialchars($d['location']) ?></td>
        <td>
            <a href="?edit=<?= $d['department_id'] ?>" class="btn btn-edit">Edit</a>
            <a href="?delete=<?= $d['department_id'] ?>" 
               onclick="return confirm('Delete this department?')" 
               class="btn btn-delete">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php if(empty($departments) && !$search): ?>
    <p style="font-size:16px;color:#777;">No departments found. Add your first one above!</p>
<?php endif; ?>

<br><br>
<a href="index.php">Back to Home</a>
</body>
</html>