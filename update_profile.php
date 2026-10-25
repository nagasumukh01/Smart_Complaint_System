<?php
session_start();
if (!isset($_SESSION['citizen_id'])) {
    header("Location: login.html");
    exit();
}

$conn = new mysqli("127.0.0.1:3307", "root", "", "problem_reporter");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_SESSION['citizen_id'];
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$age = $_POST['age'] ?? null;
$occupation = $_POST['occupation'] ?? null;
$bio = $_POST['bio'] ?? null;

// Check if file uploaded
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
    $filename = basename($_FILES['profile_image']['name']);
    $target = "uploads/" . $filename;
    move_uploaded_file($_FILES['profile_image']['tmp_name'], $target);

    $stmt = $conn->prepare("UPDATE citizens SET name=?, email=?, phone=?, age=?, occupation=?, bio=?, profile_image=? WHERE citizen_id=?");
    $stmt->bind_param("sssssssi", $name, $email, $phone, $age, $occupation, $bio, $target, $id);
} else {
    $stmt = $conn->prepare("UPDATE citizens SET name=?, email=?, phone=?, age=?, occupation=?, bio=? WHERE citizen_id=?");
    $stmt->bind_param("ssssssi", $name, $email, $phone, $age, $occupation, $bio, $id);
}

$stmt->execute();
$stmt->close();

header("Location: citizen_profile.php");
exit();
?>
