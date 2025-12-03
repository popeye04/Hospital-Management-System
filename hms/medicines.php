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

    $stmt = $pdo->prepare("SELECT * FROM medicine WHERE medicine_id = ?");
    $stmt->execute([$edit_id]);
    $editData = $stmt->fetch();

    if (!$editData) {
        $edit = false;
    }
}

// -----------------------------------------
// UPDATE MEDICINE
// -----------------------------------------
if (isset($_POST['update'])) {
    $medicine_id   = $_POST['medicine_id'];
    $medicine_name = $_POST['medicine_name'];
    $description   = $_POST['description'];
    $price         = $_POST['price'];

    $sql = "UPDATE medicine
            SET medicine_name = ?, description = ?, price = ?
            WHERE medicine_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $medicine_name, $description, $price, $medicine_id
    ]);

    header("Location: manage_medicines.php");
    exit;
}

// -----------------------------------------
// ADD NEW MEDICINE
// -----------------------------------------
if (isset($_POST['add']) && !$edit) {
    $medicine_name = $_POST['medicine_name'];
    $description   = $_POST['description'];
    $price         = $_POST['price'];

    $sql = "INSERT INTO medicine (medicine_name, description, price)
            VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$medicine_name, $description, $price]);

    header("Location: manage_medicines.php");
    exit;
}

// -----------------------------------------
// DELETE MEDICINE
// -----------------------------------------
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM medicine WHERE medicine_id = ?");
    $stmt->execute([$id]);

    header("Location: manage_medicines.php");
    exit;
}

// -----------------------------------------
// SEARCH + LIST
// -----------------------------------------
$search = isset($_GET['search']) ? $_GET['search'] : "";

$sql = "SELECT * FROM medicine
        WHERE medicine_name LIKE ? OR description LIKE ? OR price LIKE ?
        ORDER BY medicine_id ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute(["%$search%", "%$search%", "%$search%"]);
$medicines = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Medicines</title>
    <style>
        body { font-family: Arial; margin: 40px; background: #f9f9f9; }
        table { width:100%; border-collapse: collapse; margin-top:20px; }
        th, td { border:1px solid #ccc; padding:10px; }
        th { background:#007cba; color:white; }
        input, textarea { padding:8px; margin:5px; width:230px; border:1px solid #999; border-radius:4px; }
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

<h1>Manage Medicines</h1>

<div class="menu">
    <a href="index.php">Home</a> |
    <a href="patients.php">Patients</a> |
    <a href="treatments.php">Treatments</a> |
    <a href="manage_medicines.php">Medicines</a>
</div>

<hr>

<form method="GET">
    <input type="text" name="search" placeholder="Search medicines..."
           value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search</button>
    <?php if ($search): ?>
        <a href="manage_medicines.php" class="btn cancel-btn">Clear</a>
    <?php endif; ?>
</form>

<hr>

<h2><?= $edit ? "Edit Medicine" : "Add New Medicine" ?></h2>

<form method="POST">
    <?php if ($edit): ?>
        <input type="hidden" name="medicine_id" value="<?= $editData['medicine_id'] ?>">
    <?php endif; ?>

    <input type="text" name="medicine_name" placeholder="Medicine Name"
           value="<?= $edit ? htmlspecialchars($editData['medicine_name']) : "" ?>" required>

    <textarea name="description" placeholder="Description" rows="3"><?= $edit ? htmlspecialchars($editData['description']) : "" ?></textarea>

    <input type="number" step="0.01" name="price" placeholder="Price"
           value="<?= $edit ? htmlspecialchars($editData['price']) : "" ?>" required>

    <?php if ($edit): ?>
        <button type="submit" name="update">Update</button>
        <a href="manage_medicines.php" class="btn cancel-btn">Cancel</a>
    <?php else: ?>
        <button type="submit" name="add">Add Medicine</button>
    <?php endif; ?>
</form>

<hr>

<h2>All Medicines (<?= count($medicines) ?>)</h2>

<?php if (count($medicines) == 0): ?>
    <p>No medicines found.</p>
<?php else: ?>
<table>
    <tr>
        <th>ID</th>
        <th>Medicine Name</th>
        <th>Description</th>
        <th>Price</th>
        <th>Action</th>
    </tr>

    <?php foreach($medicines as $m): ?>
    <tr>
        <td><?= $m['medicine_id'] ?></td>
        <td><?= htmlspecialchars($m['medicine_name']) ?></td>
        <td><?= htmlspecialchars($m['description']) ?></td>
        <td>$<?= number_format($m['price'], 2) ?></td>
        <td>
            <a href="manage_medicines.php?edit=<?= $m['medicine_id'] ?>" class="btn edit-btn">Edit</a>
            <a href="manage_medicines.php?delete=<?= $m['medicine_id'] ?>" 
               class="btn delete-btn" onclick="return confirm('Delete this medicine?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<br><br>
<a href="index.php">Back to Homepage</a>

</body>
</html>
