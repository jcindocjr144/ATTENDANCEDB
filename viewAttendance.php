<?php
include "db.php";
session_start();

// Redirect if not instructor
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "instructor") {
  header("Location: signin.php");
  exit;
}

// Fetch attendance records
$result = $conn->query("
  SELECT a.id, u.id_number, a.date, a.status 
  FROM attendance a 
  JOIN users u ON a.student_id = u.id 
  ORDER BY a.date DESC, u.id_number ASC
");

// Fetch summary counts per day
$summary = $conn->query("
  SELECT 
    date,
    SUM(status = 'Present') AS present_count,
    SUM(status = 'Absent') AS absent_count,
    SUM(status = 'Late') AS late_count,
    SUM(status = 'Excused') AS excused_count
  FROM attendance
  GROUP BY date
  ORDER BY date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Attendance Summary</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="card shadow p-4">

      <!-- Back Button -->
      <div class="mb-3">
        <a href="instructorDashboard.php" class="btn btn-outline-secondary">
          ← Back to Dashboard
        </a>
      </div>

      <h2 class="text-center mb-4">Attendance Summary</h2>

      <!-- Daily Summary -->
      <h5 class="fw-bold mb-3">Daily Totals</h5>
      <table class="table table-bordered text-center align-middle">
        <thead class="table-dark">
          <tr>
            <th>Date</th>
            <th>Present</th>
            <th>Absent</th>
            <th>Late</th>
            <th>Excused</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($summary->num_rows > 0): ?>
            <?php while ($sum = $summary->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($sum['date']) ?></td>
                <td><?= $sum['present_count'] ?></td>
                <td><?= $sum['absent_count'] ?></td>
                <td><?= $sum['late_count'] ?></td>
                <td><?= $sum['excused_count'] ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="5" class="text-muted">No attendance records found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>

      <!-- Detailed Records -->
      <h5 class="fw-bold mt-5 mb-3">Detailed Attendance Records</h5>
      <table class="table table-bordered text-center align-middle">
        <thead class="table-secondary">
          <tr>
            <th>ID</th>
            <th>Student ID</th>
            <th>Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['id_number']) ?></td>
                <td><?= htmlspecialchars($row['date']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="4" class="text-muted">No attendance data available.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>

      <a href="recordAttendance.php" class="btn btn-secondary w-100 mt-3">← Record Attendance</a>
    </div>
  </div>
</body>
</html>
