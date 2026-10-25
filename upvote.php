<?php
session_start();
$problem_id = $_POST['id'];

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'citizen') {
    header("Location: view_problems.php?vote=not_allowed");
    exit;
}

$citizen_id = $_SESSION['citizen_id'];

$conn = new mysqli("127.0.0.1:3307", "root", "", "problem_reporter");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 🔁 Remove any existing vote by this citizen on this problem
$stmt = $conn->prepare("DELETE FROM votes WHERE citizen_id = ? AND problem_id = ?");
$stmt->bind_param("ii", $citizen_id, $problem_id);
$stmt->execute();
$stmt->close();

// 👍 Insert the new upvote
$stmt = $conn->prepare("INSERT INTO votes (citizen_id, problem_id, vote_type) VALUES (?, ?, 'up')");
$stmt->bind_param("ii", $citizen_id, $problem_id);
$stmt->execute();
$stmt->close();
$conn->close();

$redirect = $_SERVER['HTTP_REFERER'] ?? 'view_problems.php';
header("Location: $redirect");
exit;