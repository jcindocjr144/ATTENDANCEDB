<?php
include "db.php";
session_start();

if (!isset($_SESSION["role"]) || !in_array($_SESSION["role"], ["instructor","admin"])) {
    header("Location: signin.php");
    exit;
}

$student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;
if ($student_id <= 0) {
    die("Invalid student ID.");
}

$stmt = $conn->prepare("SELECT u.id_number, u.name FROM users u JOIN students s ON u.id = s.user_id WHERE s.id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
if (!$student) die("Student not found.");

$attendance = [];
$attStmt = $conn->prepare("SELECT date, status FROM attendance WHERE student_id = ? ORDER BY date DESC");
$attStmt->bind_param("i", $student_id);
$attStmt->execute();
$res = $attStmt->get_result();
while ($row = $res->fetch_assoc()) {
    $attendance[] = $row;
}

$summary = ["Present"=>0, "Absent"=>0, "Late"=>0];
foreach ($attendance as $row) {
    if (isset($summary[$row['status']])) $summary[$row['status']]++;
}
$totalDays = array_sum($summary);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Attendance Summary</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="card shadow p-4 mx-auto" style="max-width: 700px;">
    <h2 class="text-center mb-3">Attendance Summary</h2>
    <div class="alert alert-info text-center">
        <strong><?= htmlspecialchars($student['name']) ?></strong> (ID: <?= htmlspecialchars($student['id_number']) ?>)
    </div>

    <div class="row text-center mt-3">
      <div class="col-md-4">
        <div class="card bg-success text-white mb-2 p-2">Present: <?= $summary["Present"] ?></div>
      </div>
      <div class="col-md-4">
        <div class="card bg-danger text-white mb-2 p-2">Absent: <?= $summary["Absent"] ?></div>
      </div>
      <div class="col-md-4">
        <div class="card bg-warning text-dark mb-2 p-2">Late: <?= $summary["Late"] ?></div>
      </div>
    </div>
    <div class="text-center mb-3">Total Days Recorded: <?= $totalDays ?></div>

    <?php if (count($attendance) > 0): ?>
      <table class="table table-striped mt-3">
        <thead class="table-dark">
          <tr>
            <th>Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($attendance as $row): ?>
            <tr>
              <td><?= htmlspecialchars($row['date']) ?></td>
              <td><?= htmlspecialchars($row['status']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="alert alert-secondary text-center mt-3">No attendance records found.</div>
    <?php endif; ?>

    <a href="studentList.php" class="btn btn-secondary w-100 mt-3">← Back to Students</a>
  </div>
</div>
</body>
</html>
