<?php
$conn = new mysqli("localhost", "root", "", "attendance_db");
if ($conn->connect_error) die("Connection failed");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $schoolId = trim($_POST["schoolId"]);
  if ($schoolId !== "") {
    $stmt = $conn->prepare("INSERT INTO attendance (school_id, created_at) VALUES (?, NOW())");
    $stmt->bind_param("s", $schoolId);
    $stmt->execute();
    echo "success";
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Attendance Check</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container show" id="homePage">
    <div class="header">ATTENDANCE CHECK</div>
    <p>Enter School ID Number</p>
    <div class="input-box">
      <input type="text" id="schoolId">
    </div>
    <button class="btn" onclick="submitForm()">Submit</button>
    <div class="footer">@Cordova Public College</div>
  </div>

  <div class="container" id="resultPage">
    <div class="header">ATTENDANCE CHECK</div>
    <p id="resultMsg">Recorded.</p>
    <button class="btn" onclick="goHome()">Home</button>
    <button class="btn" onclick="window.location.href='view.php'">View</button>
    <div class="footer">@Cordova Public College</div>
  </div>

  <script>
    function submitForm() {
      const id = document.getElementById("schoolId").value.trim();
      if (!id) return alert("Enter School ID Number");
      const xhr = new XMLHttpRequest();
      xhr.open("POST", "index.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onload = function() {
        if (this.responseText === "success") {
          document.getElementById("homePage").classList.remove("show");
          document.getElementById("resultPage").classList.add("show");
        } else {
          alert("Error saving attendance");
        }
      }
      xhr.send("schoolId=" + encodeURIComponent(id));
    }

    function goHome() {
      document.getElementById("resultPage").classList.remove("show");
      document.getElementById("homePage").classList.add("show");
      document.getElementById("schoolId").value = "";
    }
  </script>
</body>
</html>
