<?php
require 'db.php';

$message = "";
$edit_mode = false;
$edit_report = null;

// ———————————————— EDIT MODE ————————————————
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT lr.*, lt.test_name, p.first_name, p.last_name, a.room_number
                           FROM lab_report lr
                           JOIN lab_test lt ON lr.test_id = lt.test_id
                           JOIN patient p ON lr.patient_id = p.patient_id
                           LEFT JOIN admission a ON lr.admission_id = a.admission_id
                           WHERE lr.report_id = ?");
    $stmt->execute([$id]);
    $edit_report = $stmt->fetch();
    if ($edit_report) $edit_mode = true;
}

// ———————————————— UPDATE ————————————————
if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $test_id = (int)$_POST['test_id'];
    $admission_id = $_POST['admission_id'] === '' ? null : (int)$_POST['admission_id'];
    $patient_id = (int)$_POST['patient_id'];
    $result = trim($_POST['result']);

    if ($test_id && $patient_id && $result !== '') {
        $sql = "UPDATE lab_report SET test_id=?, admission_id=?, patient_id=?, result=? WHERE report_id=?";
        $pdo->prepare($sql)->execute([$test_id, $admission_id, $patient_id, $result, $id]);
        $message = "Report updated successfully!";
        $edit_mode = false;
    } else {
        $message = "Error: Please fill all fields.";
    }
}

// ———————————————— ADD NEW ————————————————
if (isset($_POST['save']) && !$edit_mode) {
    $test_id = (int)$_POST['test_id'];
    $admission_id = $_POST['admission_id'] === '' ? null : (int)$_POST['admission_id'];
    $patient_id = (int)$_POST['patient_id'];
    $result = trim($_POST['result']);

    if ($test_id && $patient_id && $result !== '') {
        $sql = "INSERT INTO lab_report (test_id, admission_id, patient_id, result) VALUES (?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([$test_id, $admission_id, $patient_id, $result]);
        $message = "New lab report saved successfully!";
    } else {
        $message = "Error: Please select admission/test and enter result.";
    }
}

// ———————————————— DELETE ————————————————
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM lab_report WHERE report_id = ?")->execute([$id]);
    $url = "lab_reports.php" . (!empty($_GET['search']) ? "?search=" . urlencode($_GET['search']) : "");
    header("Location: $url");
    exit;
}

// ———————————————— SEARCH & LIST ————————————————
$search = trim($_GET['search'] ?? '');

$sql = "SELECT lr.*, lt.test_name, p.first_name, p.last_name, a.room_number
        FROM lab_report lr
        JOIN lab_test lt ON lr.test_id = lt.test_id
        JOIN patient p ON lr.patient_id = p.patient_id
        LEFT JOIN admission a ON lr.admission_id = a.admission_id
        WHERE p.first_name LIKE ? OR p.last_name LIKE ? OR lt.test_name LIKE ? OR lr.result LIKE ?
        ORDER BY lr.report_id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute(["%$search%", "%$search%", "%$search%", "%$search%"]);
$reports = $stmt->fetchAll();

// Load admissions (for dropdown) — only room numbers shown
$admissions = $pdo->query("
    SELECT a.admission_id, a.patient_id, a.room_number,
           p.first_name, p.last_name
    FROM admission a
    JOIN patient p ON a.patient_id = p.patient_id
    ORDER BY a.admission_date DESC
")->fetchAll();

$tests = $pdo->query("SELECT test_id, test_name FROM lab_test ORDER BY test_name")->fetchAll();

// Get patient name when admission is selected
$patient_name = '';
$patient_id_for_form = $edit_mode ? $edit_report['patient_id'] : 0;

if (isset($_POST['admission_id']) && $_POST['admission_id'] !== '') {
    foreach ($admissions as $a) {
        if ($a['admission_id'] == $_POST['admission_id']) {
            $patient_id_for_form = $a['patient_id'];
            $patient_name = $a['first_name'] . ' ' . $a['last_name'];
            break;
        }
    }
}
if ($edit_mode) {
    $patient_name = $edit_report['first_name'] . ' ' . $edit_report['last_name'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lab Reports</title>
    <style>
        body {font-family: Arial;background: #f0f8ff;margin: 40px;line-height: 1.6;}
        h1,h2 {color: #0066cc;}
        input,select,textarea,button,.btn{padding:10px 16px;margin:5px;font-size:16px;border-radius:5px;}
        input,select,textarea{border:1px solid #99ccff;width:320px;}
        textarea{height:120px;}
        button,.btn{background:#0066cc;color:white;border:none;cursor:pointer;}
        button:hover,.btn:hover{background:#004499;}
        .edit-btn{background:#f0ad4e;}.edit-btn:hover{background:#ec971f;}
        .delete-btn{background:#d9534f;}.delete-btn:hover{background:#c9302c;}
        .cancel-btn{background:#888;}
        table{width:100%;border-collapse:collapse;margin:20px 0;}
        th,td{border:1px solid #99ccff;padding:12px;text-align:left;}
        th{background:#0066cc;color:white;}
        tr:nth-child(even){background:#f9f9ff;}
        .msg{padding:15px;margin:15px 0;border-radius:5px;font-weight:bold;}
        .success{background:#d4edda;color:#155724;}
        .error{background:#f8d7da;color:#721c24;}
    </style>
</head>
<body>

<h1>Lab Reports</h1>
<a href="index.php">Home</a> | <a href="lab_tests.php">Tests</a> | <a href="lab_reports.php">Reports</a>
<hr>

<form method="GET">
    <input type="text" name="search" placeholder="Search patient, test, result..." value="<?=htmlspecialchars($search)?>" size="60">
    <button>Search</button>
    <?php if($search): ?><a href="lab_reports.php"><button type="button">Clear</button></a><?php endif; ?>
</form>

<?php if($message): ?>
    <div class="msg <?=strpos($message,'success')!==false?'success':'error'?>"><?=htmlspecialchars($message)?></div>
<?php endif; ?>

<!-- YOUR EXACT REQUEST — CLEAN & WORKING -->
<h2><?= $edit_mode ? "Edit Lab Report" : "Add New Lab Report" ?></h2>
<form method="POST">
    <select name="admission_id">
        <option value="">Select Admission</option>
        <?php foreach($admissions as $a): ?>
            <option value="<?= $a['admission_id'] ?>"
                <?= (isset($_POST['admission_id']) && $_POST['admission_id'] == $a['admission_id']) || 
                   ($edit_mode && $edit_report['admission_id'] == $a['admission_id']) ? 'selected' : '' ?>>
                Room <?= htmlspecialchars($a['room_number'] ?: '—') ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="test_id" required>
        <option value="">Select Test</option>
        <?php foreach($tests as $t): ?>
            <option value="<?= $t['test_id'] ?>"
                <?= ($edit_mode && $edit_report['test_id'] == $t['test_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($t['test_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input type="hidden" name="patient_id" value="<?= $patient_id_for_form ?>">

    <?php if ($patient_name): ?>
        <p><strong>Patient:</strong> <?= htmlspecialchars($patient_name) ?></p>
    <?php endif; ?>

    <textarea name="result" placeholder="Enter lab result..." required><?= 
        $edit_mode ? htmlspecialchars($edit_report['result']) : '' 
    ?></textarea>

    <?php if ($edit_mode): ?>
        <input type="hidden" name="id" value="<?= $edit_report['report_id'] ?>">
        <button name="update">Update Report</button>
        <a href="lab_reports.php" class="btn cancel-btn">Cancel</a>
    <?php else: ?>
        <button name="save">Save Report</button>
    <?php endif; ?>
</form>
<hr>

<h2>All Reports (<?= count($reports) ?>)</h2>
<?php if(empty($reports)): ?>
    <p>No reports found.</p>
<?php else: ?>
<table>
    <tr><th>No.</th><th>Patient</th><th>Test</th><th>Room</th><th>Result</th><th>Actions</th></tr>
    <?php $no = 1; foreach($reports as $r): ?>
    <tr>
        <td><strong><?= $no++ ?></strong></td>
        <td><strong><?= htmlspecialchars($r['first_name'].' '.$r['last_name']) ?></strong></td>
        <td><?= htmlspecialchars($r['test_name']) ?></td>
        <td><?= $r['room_number'] ?: '—' ?></td>
        <td style="white-space:pre-wrap;word-wrap:break-word;max-width:400px;">
            <?= nl2br(htmlspecialchars($r['result'])) ?>
        </td>
        <td>
            <a href="?edit=<?= $r['report_id'] ?>" class="btn edit-btn">Edit</a>
            <a href="?delete=<?= $r['report_id'] ?>" onclick="return confirm('Delete forever?')" class="btn delete-btn">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<br><br><a href="index.php">Back to Home</a>
</body>
</html>