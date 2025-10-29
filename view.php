<?php
$conn = new mysqli("localhost", "root", "", "attendance_db");
if ($conn->connect_error) die("Connection failed");
$result = $conn->query("SELECT * FROM attendance ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Attendance Records</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container show">
    <div class="header">ATTENDANCE RECORDS</div>
    <table border="1" cellpadding="10" cellspacing="0" style="width:100%; text-align:center;">
      <tr>
        <th>No.</th>
        <th>School ID</th>
        <th>Date & Time</th>
      </tr>
      <?php
      $no = 1;
      while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$no}</td>
                <td>{$row['school_id']}</td>
                <td>{$row['created_at']}</td>
              </tr>";
        $no++;
      }
      ?>
    </table>
    <button class="btn" onclick="window.location.href='index.php'">Back</button>
    <div class="footer">@Cordova Public College</div>
  </div>
</body>
</html>
