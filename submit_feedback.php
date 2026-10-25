<?php
session_start();
if (!isset($_SESSION['citizen_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("127.0.0.1:3307", "root", "", "problem_reporter");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $feedback = trim($_POST['feedback']);
    $citizen_id = $_SESSION['citizen_id'];

    if (!empty($feedback)) {
        $stmt = $conn->prepare("INSERT INTO feedbacks (citizen_id, message) VALUES (?, ?)");
        $stmt->bind_param("is", $citizen_id, $feedback);
        $stmt->execute();
        $stmt->close();
        $_SESSION['feedback_success'] = "Feedback submitted successfully!";
    }

    $conn->close();
    header("Location: citizen_dashboard.php");
    exit();
}
?>
