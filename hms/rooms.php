<?php
require 'db.php';


$edit = false;
$editData = null;
if (isset($_GET['edit'])) {
    $edit = true;
    $room_to_edit = $_GET['edit'];

    // fetch the room row for this room number
    $stmt = $pdo->prepare("SELECT * FROM room WHERE room_number = ?");
    $stmt->execute([$room_to_edit]);
    $editData = $stmt->fetch(); // fetch single row (or false if not found)

    // If not found, cancel edit mode
    if (!$editData) {
        $edit = false;
    }
}

// -------------------------------
// UPDATE ROOM
// -------------------------------
if (isset($_POST['update'])) {
    // take values from form
    $room_number   = $_POST['room_number']; // primary key (readonly in form)
    $room_type     = $_POST['room_type'];
    $department_id = $_POST['department_id'];
    $availability  = $_POST['availability'];

    // simple update query using prepared statement
    $sql = "UPDATE room
            SET room_type = ?, department_id = ?, availability = ?
            WHERE room_number = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$room_type, $department_id, $availability, $room_number]);

    // after update, go back to rooms list (no longer in edit mode)
    header("Location: rooms.php");
    exit;
}

// -------------------------------
// ADD NEW ROOM
// -------------------------------
if (isset($_POST['add']) && !$edit) {
    $room_number   = $_POST['room_number'];
    $room_type     = $_POST['room_type'];
    $department_id = $_POST['department_id'];
    $availability  = $_POST['availability'];

    $sql = "INSERT INTO room (room_number, room_type, department_id, availability)
            VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$room_number, $room_type, $department_id, $availability]);

    // redirect to avoid resubmission
    header("Location: rooms.php");
    exit;
}

// -------------------------------
// DELETE ROOM
// -------------------------------
if (isset($_GET['delete'])) {
    $room = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM room WHERE room_number = ?");
    $stmt->execute([$room]);

    header("Location: rooms.php");
    exit;
}

// -------------------------------
// SEARCH
// -------------------------------
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

// -------------------------------
// GET ALL ROOMS + department_name (searchable)
// -------------------------------
$sql = "SELECT r.*, d.department_name
        FROM room r
        LEFT JOIN department d ON r.department_id = d.department_id
        WHERE r.room_number LIKE ?
           OR r.room_type LIKE ?
           OR d.department_name LIKE ?
           OR r.availability LIKE ?
        ORDER BY r.room_number ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute(["%$search%", "%$search%", "%$search%", "%$search%"]);
$rooms = $stmt->fetchAll();

// -------------------------------
// GET DEPARTMENTS FOR DROPDOWN
// -------------------------------
$departments = $pdo->query("SELECT department_id, department_name FROM department ORDER BY department_name")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manage Rooms</title>
    <style>
        body { font-family: Arial; margin: 40px; background: #f9f9f9; color: #000; }
        h1, h2 { color: #0066cc; }
        a { color: #0066cc; text-decoration: none; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #999; padding: 10px; text-align: left; }
        th { background: #0066cc; color: white; }
        input, select { padding: 8px; margin: 5px; width: 200px; border: 1px solid #999; border-radius: 4px; }
        button, .btn { padding: 8px 15px; background: #0066cc; color: white; border: none; border-radius: 4px; cursor: pointer; margin: 5px 2px; text-decoration: none; display: inline-block; }
        .edit-btn { background: #f0ad4e; color: #000; }
        .delete-btn { background: #d9534f; }
        .cancel-btn { background: #888; }
        .msg { padding: 12px; margin: 15px 0; background: #d4edda; color: #155724; font-weight: bold; border: 1px solid #c3e6cb; }
        .menu a { margin-right: 20px; font-size: 18px; }
    </style>
</head>
<body>

<h1>Manage Rooms</h1>

<!-- Menu -->
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
    <input type="text" name="search" placeholder="Search by room number, type, department..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search</button>
    <?php if ($search): ?>
        <a href="rooms.php" class="btn cancel-btn">Clear</a>
    <?php endif; ?>
</form>

<hr>

<!-- ADD / EDIT FORM -->
<h2><?= $edit ? "Edit Room" : "Add New Room" ?></h2>

<form method="POST">
    <!-- room_number is PK: readonly in edit mode -->
    <input type="number" name="room_number" placeholder="Room Number" required
           value="<?= $edit ? htmlspecialchars($editData['room_number']) : '' ?>"
           <?= $edit ? 'readonly' : '' ?>>

    <select name="room_type" required>
        <option value="">Room Type</option>
        <option <?= $edit && $editData['room_type'] == "General" ? "selected" : "" ?>>General</option>
        <option <?= $edit && $editData['room_type'] == "Private" ? "selected" : "" ?>>Private</option>
        <option <?= $edit && $editData['room_type'] == "ICU" ? "selected" : "" ?>>ICU</option>
        <option <?= $edit && $editData['room_type'] == "Emergency" ? "selected" : "" ?>>Emergency</option>
    </select>

    <select name="department_id" required>
        <option value="">Select Department</option>
        <?php foreach ($departments as $d): ?>
            <option value="<?= $d['department_id'] ?>"
                <?= $edit && $editData['department_id'] == $d['department_id'] ? "selected" : "" ?>>
                <?= htmlspecialchars($d['department_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="availability" required>
        <option value="Available" <?= $edit && $editData['availability'] == "Available" ? "selected" : "" ?>>Available</option>
        <option value="Occupied" <?= $edit && $editData['availability'] == "Occupied" ? "selected" : "" ?>>Occupied</option>
    </select>

    <?php if ($edit): ?>
        <button type="submit" name="update">Update Room</button>
        <a href="rooms.php" class="btn cancel-btn">Cancel</a>
    <?php else: ?>
        <button type="submit" name="add">Add Room</button>
    <?php endif; ?>
</form>

<hr>

<!-- LIST ROOMS -->
<h2>All Rooms (<?= count($rooms) ?>)</h2>

<?php if (count($rooms) == 0): ?>
    <p>No rooms found. Add one above!</p>
<?php else: ?>
<table>
    <tr>
        <th>Room Number</th>
        <th>Room Type</th>
        <th>Department</th>
        <th>Availability</th>
        <th>Action</th>
    </tr>
    <?php foreach ($rooms as $r): ?>
    <tr>
        <td><strong><?= htmlspecialchars($r['room_number']) ?></strong></td>
        <td><?= htmlspecialchars($r['room_type']) ?></td>
        <td><?= htmlspecialchars($r['department_name'] ?? '—') ?></td>
        <td><?= htmlspecialchars($r['availability']) ?></td>
        <td>
            <a class="btn edit-btn" href="rooms.php?edit=<?= $r['room_number'] ?>">Edit</a>
            <a class="btn delete-btn" href="rooms.php?delete=<?= $r['room_number'] ?>"
               onclick="return confirm('Delete Room <?= $r['room_number'] ?> permanently?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<br><br>
<a href="index.php">Back to Homepage</a>

</body>
</html>
