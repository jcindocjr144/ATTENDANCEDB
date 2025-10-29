<?php
include "db.php";
session_start();
if (!isset($_SESSION["user_id"])) {
  header("Location: signin.php");
  exit;
}

$user_id = $_SESSION["user_id"];
$role = $_SESSION["role"];

if ($role === "student" && $_SERVER["REQUEST_METHOD"] === "POST") {
  $instructor_id = intval($_POST["instructor_id"]);
  $stmt = $conn->prepare("INSERT INTO attendance (student_id, instructor_id) VALUES (?, ?)");
  $stmt->bind_param("ii", $user_id, $instructor_id);
  $stmt->execute();
  $msg = "Attendance recorded!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Attendance</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container show">
    <div class="header">ATTENDANCE CHECK</div>
    <?php if ($role === "student"): ?>
      <form method="POST">
        <p>Enter Instructor ID Number:</p>
        <div class="input-box"><input type="number" name="instructor_id" required></div>
        <button class="btn" type="submit">Submit Attendance</button>
      </form>
      <?php if (!empty($msg)) echo "<p style='color:lime;'>$msg</p>"; ?>
    <?php else: ?>
      <p>Attendance Records</p>
      <table border="1" style="width:100%;color:white;border-collapse:collapse;">
        <tr><th>#</th><th>Student ID</th><th>Date</th></tr>
        <?php
        $stmt = $conn->prepare("SELECT a.id, u.id_number, a.created_at FROM attendance a JOIN users u ON a.student_id=u.id WHERE a.instructor_id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $i = 1;
        while ($row = $res->fetch_assoc()) {
          echo "<tr><td>{$i}</td><td>{$row['id_number']}</td><td>{$row['created_at']}</td></tr>";
          $i++;
        }
        ?>
      </table>
    <?php endif; ?>
    <button class="btn" onclick="window.location.href='dashboard.php'">Home</button>
  </div>
</body>
</html>
