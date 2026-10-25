<?php
session_start();
$host = "127.0.0.1:3307";
$user = "root";
$password = "";
$database = "problem_reporter";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$status = isset($_POST['status']) ? trim($_POST['status']) : "";

$allowed = ["pending", "in process", "resolved"];
if ($id && in_array($status, $allowed)) {
    $stmt = $conn->prepare("UPDATE problems SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    $stmt->close();
}

// Redirect back to problem detail
header("Location: problem_detail.php?id=" . $id);
exit;
?>
