<?php
session_start();
$conn = new mysqli("127.0.0.1:3307", "root", "", "problem_reporter");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $imagePath = '';

    // Check if email exists
    $check = $conn->prepare("SELECT * FROM citizens WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already registered. Please log in.'); window.location='login.html';</script>";
        exit();
    }

    // Upload profile image
    if (!empty($_FILES['profile_image']['name'])) {
        $targetDir = "uploads/";
        $imageName = uniqid() . '_' . basename($_FILES["profile_image"]["name"]);
        $targetFile = $targetDir . $imageName;

        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $targetFile)) {
            $imagePath = $targetFile;
        }
    }

    // Insert citizen data
    $stmt = $conn->prepare("INSERT INTO citizens (name, email, password, profile_image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $imagePath);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! Please log in.'); window.location='login.html';</script>";
    } else {
        echo "<script>alert('Error occurred. Please try again.'); window.location='register.html';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
