<?php
require 'db.php';

$message = "";
$edit_mode = false;
$edit_id = $edit_name = $edit_price = "";

// ———————————————— 1. EDIT MODE ————————————————
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM lab_test WHERE test_id = ?");
    $stmt->execute([$edit_id]);
    $row = $stmt->fetch();
    if ($row) {
        $edit_mode = true;
        $edit_name = $row['test_name'];
        $edit_price = $row['price'];
    }
}

// ———————————————— 2. UPDATE ————————————————
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = trim($_POST['test_name']);
    $price = $_POST['price'];

    if ($name && $price >= 0) {
        $stmt = $pdo->prepare("UPDATE lab_test SET test_name = ?, price = ? WHERE test_id = ?");
        $stmt->execute([$name, $price, $id]);
        $message = "Test updated successfully!";
        $edit_mode = false;
    } else {
        $message = "Please fill all fields!";
    }
}

// ———————————————— 3. ADD NEW ————————————————
if (isset($_POST['add']) && !$edit_mode) {
    $name = trim($_POST['test_name']);
    $price = $_POST['price'];

    if ($name && $price >= 0) {
        $stmt = $pdo->prepare("INSERT INTO lab_test (test_name, price) VALUES (?, ?)");
        $stmt->execute([$name, $price]);
        $message = "New test added successfully!";
    } else {
        $message = "Please fill all fields!";
    }
}

// ———————————————— 4. DELETE ————————————————
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM lab_test WHERE test_id = ?");
    $stmt->execute([$id]);

    $url = "lab_tests.php";
    if (!empty($_GET['search'])) $url .= "?search=" . urlencode($_GET['search']);
    header("Location: $url");
    exit;
}

// ———————————————— 5. SEARCH & FETCH ALL ————————————————
$search = isset($_GET['search']) ? trim($_GET['search']) : "";

$sql = "SELECT * FROM lab_test 
        WHERE test_name LIKE ? 
        ORDER BY test_id ASC";   // oldest first → newest at bottom
$stmt = $pdo->prepare($sql);
$stmt->execute(["%$search%"]);
$tests = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manage Lab Tests</title>
    <style>
        body { font-family: Arial; background: #f0f8ff; margin: 40px; }
        h1, h2 { color: #0066cc; }
        input, button, .btn { padding: 10px 16px; margin: 5px; font-size: 16px; border-radius: 5px; }
        input { border: 1px solid #99ccff; width: 280px; }
        button, .btn { background: #0066cc; color: white; border: none; text-decoration: none; cursor: pointer; }
        button:hover, .btn:hover { background: #004499; }
        .edit-btn   { background: #f0ad4e; }
        .edit-btn:hover { background: #ec971f; }
        .delete-btn { background: #d9534f; }
        .delete-btn:hover { background: #c9302c; }
        .cancel-btn { background: #888; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #99ccff; padding: 12px; text-align: left; }
        th { background: #0066cc; color: white; }
        tr:nth-child(even) { background: #f9f9ff; }
        .msg { padding: 15px; margin: 15px 0; border-radius: 5px; font-weight: bold; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<h1>Manage Lab Tests</h1>

<p>
    <a href="index.php">Home</a> |
    <a href="patients.php">Patients</a> |
    <a href="lab_tests.php">Lab Tests</a>
</p>
<hr>

<!-- Search -->
<form method="GET">
    <input type="text" name="search" placeholder="Search test name..." 
           value="<?= htmlspecialchars($search) ?>" size="40">
    <button type="submit">Search</button>
    <?php if ($search): ?>
        <a href="lab_tests.php"><button type="button">Clear</button></a>
    <?php endif; ?>
</form>

<?php if ($message): ?>
    <div class="msg <?= strpos($message, 'success') !== false ? 'success' : 'error' ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<!-- Add/Edit Form -->
<h2><?= $edit_mode ? "Edit Test" : "Add New Test" ?></h2>
<form method="POST">
    <input type="text" name="test_name" placeholder="Test Name" 
           value="<?= $edit_mode ? htmlspecialchars($edit_name) : '' ?>" required>
    <input type="number" step="0.01" name="price" placeholder="Price" 
           value="<?= $edit_mode ? $edit_price : '' ?>" required min="0">

    <?php if ($edit_mode): ?>
        <input type="hidden" name="id" value="<?= $edit_id ?>">
        <button type="submit" name="update">Update Test</button>
        <a href="lab_tests.php<?= $search ? '?search='.urlencode($search) : '' ?>" 
           class="btn cancel-btn">Cancel</a>
    <?php else: ?>
        <button type="submit" name="add">Add Test</button>
    <?php endif; ?>
</form>

<hr>

<h2>All Lab Tests (<?= count($tests) ?>)</h2>

<?php if (empty($tests)): ?>
    <p>No tests found. Add one above!</p>
<?php else: ?>
    <table>
        <tr>
            <th>No.</th>         
            <th>Test Name</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
        <?php 
        $serial = 1;  // Start counting from 1
        foreach ($tests as $t): 
        ?>
        <tr>
            <td><strong><?= $serial++ ?></strong></td>  <!-- Shows 1,2,3,4... -->
            <td><?= htmlspecialchars($t['test_name']) ?></td>
            <td><strong>$<?= number_format($t['price'], 2) ?></strong></td>
            <td>
                <a href="?edit=<?= $t['test_id'] ?>&search=<?= urlencode($search) ?>" 
                   class="btn edit-btn">Edit</a>
                <a href="?delete=<?= $t['test_id'] ?>&search=<?= urlencode($search) ?>"
                   onclick="return confirm('Delete \"<?= addslashes($t['test_name']) ?>\" forever?')"
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