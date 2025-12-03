<?php
require 'db.php';
$message = "";

// ———————— EDIT MODE ————————
$edit = false;
$edit_data = null;

if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = $pdo->query("SELECT * FROM staff WHERE staff_id = $id");
    $edit_data = $result->fetch();
    if ($edit_data) $edit = true;
}

// ———————— UPDATE STAFF ————————
if (isset($_POST['update'])) {
    $id          = $_POST['id'];
    $first_name  = $_POST['first_name'];
    $last_name   = $_POST['last_name'];
    $dob         = $_POST['dob'];
    $gender      = $_POST['gender'];
    $category    = $_POST['category'];
    $phone       = $_POST['phone'];
    $email       = $_POST['email'];
    $address     = $_POST['address'];
    $joined_date = $_POST['joined_date'];
    $salary      = $_POST['salary'];

    $sql = "UPDATE staff SET 
            first_name='$first_name',
            last_name='$last_name',
            dob='$dob',
            gender='$gender',
            category='$category',
            phone='$phone',
            email='$email',
            address='$address',
            joined_date='$joined_date',
            salary='$salary'
            WHERE staff_id='$id'";

    $pdo->query($sql);
    $message = "Staff updated successfully!";
    $edit = false;
}

// ———————— ADD NEW STAFF ————————
if (isset($_POST['add']) && !$edit) {
    $first_name  = $_POST['first_name'];
    $last_name   = $_POST['last_name'];
    $dob         = $_POST['dob'];
    $gender      = $_POST['gender'];
    $category    = $_POST['category'];
    $phone       = $_POST['phone'];
    $email       = $_POST['email'];
    $address     = $_POST['address'];
    $joined_date = $_POST['joined_date'];
    $salary      = $_POST['salary'];

    $sql = "INSERT INTO staff 
            (first_name, last_name, dob, gender, category, phone, email, address, joined_date, salary)
            VALUES ('$first_name','$last_name','$dob','$gender','$category','$phone','$email','$address','$joined_date','$salary')";

    $pdo->query($sql);
    $message = "Staff added successfully!";
}

// ———————— DELETE STAFF ————————
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->query("DELETE FROM staff WHERE staff_id = $id");
    header("Location: staff.php");
    exit;
}

// ———————— SEARCH ————————
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

// ———————— GET ALL STAFF ————————
// Show in ascending order by staff_id → 1, 2, 3, 4... (oldest ID at top, newest at bottom)
$sql = "SELECT * FROM staff 
        WHERE first_name LIKE '%$search%' 
           OR last_name LIKE '%$search%' 
           OR phone LIKE '%$search%' 
           OR category LIKE '%$search%'
        ORDER BY staff_id ASC";   // This makes IDs go 1,2,3... from top to bottom

$result = $pdo->query($sql);
$staff = $result->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Staff</title>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial; margin: 40px; background: #f9f9f9; color: #000; }
        h1, h2 { color: #0066cc; }
        a { color: #0066cc; text-decoration: none; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #999; padding: 10px; text-align: left; }
        th { background: #0066cc; color: white; }
        tr:nth-child(even) { background: #f0f8ff; }

        input, select {
            padding: 8px; margin: 5px; width: 200px;
            border: 1px solid #999; border-radius: 4px;
        }
        button, .btn {
            padding: 8px 15px; background: #0066cc; color: white;
            border: none; border-radius: 4px; cursor: pointer; margin: 5px 2px;
        }
        .edit-btn   { background: #f0ad4e; }
        .delete-btn { background: #d9534f; }
        .cancel-btn { background: #888; }
        .msg { padding: 12px; margin: 15px 0; background: #d4edda; color: #155724; font-weight: bold; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>

<h1>Manage Staff</h1>

<a href="index.php">Home</a> |
<a href="patients.php">Patients</a> |
<a href="doctors.php">Doctors</a> |
<a href="staff.php">Staff</a>

<hr>

<!-- Search -->
<form method="GET">
    <input type="text" name="search" placeholder="Search staff..." value="<?= $search ?>">
    <button type="submit">Search</button>
    <?php if($search): ?>
        <a href="staff.php"><button type="button">Clear</button></a>
    <?php endif; ?>
</form>

<?php if($message): ?>
    <div class="msg">Success: <?= $message ?></div>
<?php endif; ?>

<h2><?= $edit ? "Edit Staff Member" : "Add New Staff" ?></h2>
<form method="POST">
    <input type="text" name="first_name" placeholder="First Name" value="<?= $edit ? $edit_data['first_name'] : '' ?>" required>
    <input type="text" name="last_name" placeholder="Last Name" value="<?= $edit ? $edit_data['last_name'] : '' ?>" required><br>

    <input type="date" name="dob" value="<?= $edit ? $edit_data['dob'] : '' ?>" required>
    <select name="gender" required>
        <option value="">Gender</option>
        <option <?= ($edit && $edit_data['gender']=='M') ? 'selected' : '' ?>>M</option>
        <option <?= ($edit && $edit_data['gender']=='F') ? 'selected' : '' ?>>F</option>
        <option <?= ($edit && $edit_data['gender']=='Other') ? 'selected' : '' ?>>Other</option>
    </select><br>

    <select name="category" required>
        <option value="">Category</option>
        <option <?= ($edit && $edit_data['category']=='Nurse') ? 'selected' : '' ?>>Nurse</option>
        <option <?= ($edit && $edit_data['category']=='Ward Boy') ? 'selected' : '' ?>>Ward Boy</option>
        <option <?= ($edit && $edit_data['category']=='Cleaner') ? 'selected' : '' ?>>Cleaner</option>
        <option <?= ($edit && $edit_data['category']=='Technician') ? 'selected' : '' ?>>Technician</option>
        <option <?= ($edit && $edit_data['category']=='Receptionist') ? 'selected' : '' ?>>Receptionist</option>
        <option <?= ($edit && $edit_data['category']=='Pharmacist') ? 'selected' : '' ?>>Pharmacist</option>
        <option <?= ($edit && $edit_data['category']=='Other') ? 'selected' : '' ?>>Other</option>
    </select><br>

    <input type="text" name="phone" placeholder="Phone" value="<?= $edit ? $edit_data['phone'] : '' ?>" required>
    <input type="email" name="email" placeholder="Email" value="<?= $edit ? $edit_data['email'] : '' ?>"><br>

    <input type="text" name="address" placeholder="Address" value="<?= $edit ? $edit_data['address'] : '' ?>">
    <input type="date" name="joined_date" value="<?= $edit ? $edit_data['joined_date'] : date('Y-m-d') ?>" required><br>

    <input type="number" name="salary" placeholder="Salary" step="0.01" value="<?= $edit ? $edit_data['salary'] : '' ?>" required>

    <?php if($edit): ?>
        <input type="hidden" name="id" value="<?= $edit_data['staff_id'] ?>">
        <button type="submit" name="update">Update Staff</button>
        <a href="staff.php"><button type="button" class="cancel-btn">Cancel</button></a>
    <?php else: ?>
        <button type="submit" name="add">Add Staff</button>
    <?php endif; ?>
</form>

<hr>

<h2>All Staff Members (<?= count($staff) ?>)</h2>

<?php if(count($staff) == 0): ?>
    <p>No staff found.</p>
<?php else: ?>
<table>
    <tr>
        <th>Staff ID</th>
        <th>Name</th>
        <th>Gender</th>
        <th>DOB</th>
        <th>Category</th>
        <th>Phone</th>
        <th>Salary</th>
        <th>Joined</th>
        <th>Action</th>
    </tr>
    <?php foreach($staff as $s): ?>
    <tr>
        <td><strong><?= $s['staff_id'] ?></strong></td>
        <td><?= $s['first_name'] ?> <?= $s['last_name'] ?></td>
        <td><?= $s['gender'] ?></td>
        <td><?= date('d-m-Y', strtotime($s['dob'])) ?></td>
        <td><?= $s['category'] ?></td>
        <td><?= $s['phone'] ?></td>
        <td>$<?= number_format($s['salary'], 2) ?></td>
        <td><?= date('d-m-Y', strtotime($s['joined_date'])) ?></td>
        <td>
            <a href="staff.php?edit=<?= $s['staff_id'] ?>" class="btn edit-btn">Edit</a>
            <a href="staff.php?delete=<?= $s['staff_id'] ?>" 
               onclick="return confirm('Delete this staff permanently?')" 
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