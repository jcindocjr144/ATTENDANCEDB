<?php
session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "instructor") {
    header("Location: signin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Instructor Dashboard</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">

  <div class="container show" id="dashboardPage">
    <div class="header">INSTRUCTOR DASHBOARD</div>
    <p>Manage Students and Attendance</p>
    <div class="d-flex flex-column gap-3">
      <a href="addStudent.php" class="btn-login">Add Student</a>
      <a href="recordAttendance.php" class="btn-login">Record Attendance</a>
      <a href="viewAttendance.php" class="btn-login">View Attendance</a>
      <a href="logout.php" class="btn-login">Logout</a>
    </div>
    <div class="footer">@Cordova Public College</div>
  </div>

</body>
</html>
