<?php
include "db.php";
session_start();
if (!isset($_SESSION["user_id"])) exit("unauthorized");

$schoolId = trim($_POST["schoolId"]);
$user_id = $_SESSION["user_id"];

if ($schoolId !== "") {
  $stmt = $conn->prepare("INSERT INTO attendance (user_id, school_id, created_at) VALUES (?, ?, NOW())");
  $stmt->bind_param("is", $user_id, $schoolId);
  $stmt->execute();
  echo "success";
}
$date = date('Y-m-d'); 
$status = $_POST['status']; 
$student_id = $_POST['student_id'];

$stmt = $conn->prepare("INSERT INTO attendance (student_id, date, status) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $student_id, $date, $status);
$stmt->execute();

?>
