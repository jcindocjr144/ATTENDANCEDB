<?php
include "db.php";
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "student") {
  header("Location: signin.php");
  exit;
}

$user_id = $_SESSION["user_id"];
$msg = "";

$stmt = $conn->prepare("SELECT id_number, name FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_name'])) {
  $newName = trim($_POST["name"]);
  if (!empty($newName)) {
    $conn->begin_transaction();
    try {
      $updateUser = $conn->prepare("UPDATE users SET name = ? WHERE id = ?");
      $updateUser->bind_param("si", $newName, $user_id);
      $updateUser->execute();

      $conn->commit();
      $msg = "Name updated successfully!";
      $user["name"] = $newName;
    } catch (Exception $e) {
      $conn->rollback();
      $msg = "Error updating name.";
    }
  } else {
    $msg = "Name cannot be empty.";
  }
}

$selectedMonth = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
$startDate = date('Y-m-01', strtotime($selectedMonth));
$endDate = date('Y-m-t', strtotime($selectedMonth));

$sql = "SELECT date, status FROM attendance
        WHERE student_id = ? AND date BETWEEN ? AND ?
        ORDER BY date DESC";

$attStmt = $conn->prepare($sql);
$attStmt->bind_param("iss", $user_id, $startDate, $endDate);
$attStmt->execute();
$result = $attStmt->get_result();

$attendance = [];
$totals = ['Present' => 0, 'Absent' => 0, 'Late' => 0, 'Excused' => 0];

while ($row = $result->fetch_assoc()) {
  $attendance[] = $row;
  if (isset($totals[$row['status']])) $totals[$row['status']]++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="card shadow p-4 mx-auto" style="max-width: 700px;">
    <h2 class="text-center mb-3">Student Dashboard</h2>

    <div class="alert alert-info text-center">
      ID Number: <strong><?= htmlspecialchars($user["id_number"]) ?></strong>
    </div>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label fw-bold">Your Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user["name"]) ?>" class="form-control" placeholder="Enter your name">
      </div>
      <button type="submit" name="update_name" class="btn btn-primary w-100">Update Name</button>
    </form>

    <?php if (!empty($msg)): ?>
      <div class="alert alert-success text-center mt-3"><?= $msg ?></div>
    <?php endif; ?>

    <hr>

    <form method="GET" class="mb-3 text-center">
      <label for="month" class="form-label fw-bold">Select Month:</label>
      <input type="month" id="month" name="month" value="<?= $selectedMonth ?>" class="form-control d-inline-block w-auto mx-2">
      <button class="btn btn-primary">View</button>
    </form>

    <div class="card p-3 mt-3">
      <h5 class="fw-bold text-center">Monthly Summary — <?= date('F Y', strtotime($selectedMonth)) ?></h5>
      <ul class="list-group mt-2">
        <li class="list-group-item d-flex justify-content-between">
          <span>Present</span> <span class="fw-bold text-success"><?= $totals['Present'] ?></span>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span>Absent</span> <span class="fw-bold text-danger"><?= $totals['Absent'] ?></span>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span>Late</span> <span class="fw-bold text-warning"><?= $totals['Late'] ?></span>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span>Excused</span> <span class="fw-bold text-info"><?= $totals['Excused'] ?></span>
        </li>
      </ul>
    </div>

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
              <td><?= htmlspecialchars($row["date"]) ?></td>
              <td>
                <span class="badge
                  <?php
                    if ($row['status'] === 'Present') echo 'bg-success';
                    elseif ($row['status'] === 'Absent') echo 'bg-danger';
                    elseif ($row['status'] === 'Late') echo 'bg-warning text-dark';
                    else echo 'bg-info text-dark';
                  ?>">
                  <?= htmlspecialchars($row["status"]) ?>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="alert alert-secondary text-center mt-3">No attendance records found for this month.</div>
    <?php endif; ?>

    <a href="signin.php" class="btn btn-secondary w-100 mt-3">Log Out</a>
  </div>
</div>
</body>
</html>
