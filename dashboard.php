<?php
include "db.php";
session_start();
if (!isset($_SESSION["user_id"])) header("Location: signin.php");

$role = $_SESSION["role"];
$id = $_SESSION["user_id"];

if ($role === "student" && $_SERVER["REQUEST_METHOD"] === "POST") {
  $instructor_id = 1;
  $stmt = $conn->prepare("INSERT INTO attendance (student_id, instructor_id) VALUES (?, ?)");
  $stmt->bind_param("ii", $id, $instructor_id);
  $stmt->execute();
  $msg = "Attendance Recorded!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-primary">
  <div class="container-fluid">
    <span class="navbar-brand">Attendance System</span>
    <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
  </div>
</nav>

<div class="container mt-5 text-center">
  <?php if ($role === "student"): ?>
    <h3>Welcome Student #<?= $_SESSION["id_number"] ?></h3>
    <form method="POST" class="mt-4">
      <button class="btn btn-success btn-lg">Record Attendance</button>
    </form>
    <?php if (!empty($msg)) echo "<p class='text-success mt-3'>$msg</p>"; ?>
  <?php else: ?>
    <h3>Welcome Instructor</h3>
    <div class="mt-4">
      <a href="addStudent.php" class="btn btn-primary btn-lg me-2">Add Student</a>
      <a href="viewAttendance.php" class="btn btn-success btn-lg">View Attendance</a>
    </div>
  <?php endif; ?>
</div>
</body>
</html>
