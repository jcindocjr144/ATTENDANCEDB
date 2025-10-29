<?php
include('db.php');
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "instructor") {
  header("Location: signin.php");
  exit;
}

$selectedMonth = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
$startDate = date('Y-m-01', strtotime($selectedMonth));
$endDate = date('Y-m-t', strtotime($selectedMonth));

$sql = "SELECT a.date, u.id_number, a.status 
        FROM attendance a
        JOIN users u ON a.student_id = u.id
        WHERE a.date BETWEEN ? AND ?
        ORDER BY a.date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();

$attendance = [];
$totals = ['Present' => 0, 'Absent' => 0, 'Late' => 0, 'Excused' => 0];

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $attendance[] = $row;
    if (isset($totals[$row['status']])) $totals[$row['status']]++;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Attendance Summary</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="card shadow p-4">
      <h2 class="text-center mb-4">Attendance Summary — <?= date('F Y', strtotime($selectedMonth)) ?></h2>

      <form method="get" class="mb-3 text-center">
        <label for="month" class="form-label fw-bold">Select Month:</label>
        <input type="month" id="month" name="month" value="<?= $selectedMonth ?>" class="form-control d-inline-block w-auto mx-2">
        <button class="btn btn-primary">View</button>
        <a href="recordAttendance.php" class="btn btn-secondary ms-2">← Back to Record</a>
      </form>

      <table class="table table-hover table-bordered align-middle">
  <thead class="table-dark text-center">
    <tr>
      <th>Date</th>
      <th>Student ID</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
    <?php if (count($attendance) > 0): ?>
      <?php foreach ($attendance as $row): ?>
        <tr class="align-middle">
          <td class="text-center"><?= htmlspecialchars($row['date']) ?></td>
          <td class="text-center"><?= htmlspecialchars($row['id_number']) ?></td>
          <td class="text-center">
            <span class="badge
              <?php
                if ($row['status'] === 'Present') echo 'bg-success';
                elseif ($row['status'] === 'Absent') echo 'bg-danger';
                elseif ($row['status'] === 'Late') echo 'bg-warning text-dark';
                else echo 'bg-info text-dark';
              ?>">
              <?= htmlspecialchars($row['status']) ?>
            </span>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="3" class="text-center text-muted">No attendance records for this month.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<div class="card mt-4 shadow-sm p-3">
  <h5 class="fw-bold mb-3 text-center">Monthly Summary</h5>
  <div class="d-flex justify-content-around text-center">
    <div>
      <div class="badge bg-success fs-6">Present</div>
      <div class="fw-bold text-success mt-1"><?= $totals['Present'] ?></div>
    </div>
    <div>
      <div class="badge bg-danger fs-6">Absent</div>
      <div class="fw-bold text-danger mt-1"><?= $totals['Absent'] ?></div>
    </div>
    <div>
      <div class="badge bg-warning text-dark fs-6">Late</div>
      <div class="fw-bold text-warning mt-1"><?= $totals['Late'] ?></div>
    </div>
    <div>
      <div class="badge bg-info text-dark fs-6">Excused</div>
      <div class="fw-bold text-info mt-1"><?= $totals['Excused'] ?></div>
    </div>
  </div>
</div>

</body>
</html>
