<?php
include "db.php";
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "instructor") {
  header("Location: signin.php");
  exit;
}

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $date = $_POST['date'];
  $instructor_id = $_SESSION['user_id'];

  if (!empty($_POST['attendance'])) {
    foreach ($_POST['attendance'] as $student_id => $status) {
      if (!in_array($status, ['Present','Absent','Late'])) continue;

      $stmt = $conn->prepare("INSERT INTO attendance (student_id, instructor_id, date, status)
                              VALUES (?, ?, ?, ?)
                              ON DUPLICATE KEY UPDATE status = VALUES(status)");
      $stmt->bind_param("iiss", $student_id, $instructor_id, $date, $status);
      $stmt->execute();
    }
    $msg = "Attendance recorded successfully!";
  } else {
    $msg = "No attendance data submitted.";
  }
}

$students = $conn->query("SELECT * FROM users WHERE role='student' ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Record Attendance</title>
  <link href="style.css" rel="stylesheet">
</head>
<body>
  <div class="container show">
    <div class="header">RECORD ATTENDANCE</div>

    <?php if (!empty($msg)): ?>
      <div class="alert alert-success text-center mt-3"><?= $msg ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="input-box text-center">
        <label for="date" class="form-label fw-bold">Select Date:</label>
        <input type="date" id="date" name="date" required>
      </div>

      <table class="table table-bordered table-striped mt-3">
        <thead class="table-dark text-center">
          <tr>
            <th>Student ID Number</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($students->num_rows > 0): ?>
            <?php while ($s = $students->fetch_assoc()): ?>
              <tr>
                <td class="text-center"><?= htmlspecialchars($s['id_number']) ?></td>
                <td>
                  <select name="attendance[<?= $s['id'] ?>]" class="form-select" required>
                    <option value="Present">Present</option>
                    <option value="Absent">Absent</option>
                    <option value="Late">Late</option>
                  </select>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="2" class="text-center text-muted">No students found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>

      <button type="submit" class="btn-login w-100 mt-3">Submit Attendance</button>
      <button type="button" class="btn-login w-100 mt-2" onclick="location.href='attendanceSummary.php'">View Summary</button>
  <button type="button" class="btn-login w-100 mt-2" onclick="location.href='instructorDashboard.php'">← Back to Dashboard</button>
</form>
    <div class="footer">@Cordova Public College</div>
  </div>
</body>
</html>
