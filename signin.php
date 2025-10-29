<?php
include "db.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $id_number = trim($_POST["id_number"]);
  $password = trim($_POST["password"]);

  $stmt = $conn->prepare("SELECT * FROM users WHERE id_number=? AND password=?");
  $stmt->bind_param("ss", $id_number, $password);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $_SESSION["user_id"] = $user["id"];
    $_SESSION["role"] = $user["role"];

    if ($user["role"] === "instructor") {
      header("Location: instructorDashboard.php");
    } else {
      header("Location: studentDashboard.php");
    }
    exit;
  } else {
    $error = "Invalid ID number or password.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container show">
    <div class="header">SIGN IN</div>
    <form method="POST">
      <div class="input-box"><input type="text" name="id_number" placeholder="ID Number" required></div>
      <div class="input-box"><input type="password" name="password" placeholder="Password" required></div>
      <button class="btn" type="submit">Sign In</button>
      <p style="margin-top:10px;">No account? 
      <a href="signup.php" style="color:white;text-decoration:underline;">Sign Up</a></p>
      <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    </form>
  </div>
</body>
</html>
