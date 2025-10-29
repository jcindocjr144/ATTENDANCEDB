<?php
include "db.php";
session_start();

if ($_SESSION["role"] !== "instructor") {
  header("Location: signin.php");
  exit;
}

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $id_number = trim($_POST["id_number"]);
  $password = trim($_POST["password"]);
  $role = "student";

  $conn->begin_transaction();
  try {
    $stmt = $conn->prepare("INSERT INTO users (id_number, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $id_number, $password, $role);
    $stmt->execute();

    $user_id = $conn->insert_id;

    $stmt2 = $conn->prepare("INSERT INTO students (user_id) VALUES (?)");
    $stmt2->bind_param("i", $user_id);
    $stmt2->execute();

    $conn->commit();
    $msg = "✅ Student added successfully!";
  } catch (Exception $e) {
    $conn->rollback();
    $msg = "❌ Error: Could not add student. ID number may already exist.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Student</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
  <div class="container show">
    <div class="header">ADD STUDENT</div>
    <form method="POST">
      <div class="input-box">
        <input type="text" name="id_number" placeholder="Student ID Number" required>
      </div>
      <div class="input-box">
        <input type="password" name="password" placeholder="Password" required>
      </div>
      <button type="submit" class="btn-login w-100">Add Student</button>
      <button type="button" class="btn-login w-100 mt-2" onclick="location.href='InstructorDashboard.php'">Back</button>
      <?php if (!empty($msg)) echo "<p class='mt-3 text-center fw-bold text-success'>$msg</p>"; ?>
    </form>
    <div class="footer">@Cordova Public College</div>
  </div>
</body>
</html>
