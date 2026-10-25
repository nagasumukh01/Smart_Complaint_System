<?php
session_start();
$conn = new mysqli("127.0.0.1:3307", "root", "", "problem_reporter");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if (!isset($_POST['role'], $_POST['login_id'], $_POST['password'])) {
    die("Missing login details.");
}

$role = $_POST['role'];
$login_id = $_POST['login_id'];
$password = $_POST['password'];

if ($role === 'citizen') {
    $sql = "SELECT * FROM citizens WHERE email = ?";
} elseif ($role === 'admin') {
    $sql = "SELECT * FROM admins WHERE username = ?";
} else {
    die("Invalid role selected.");
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $login_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $storedPassword = $user['password'];

    if (password_verify($password, $storedPassword) || $password === $storedPassword) {
        $_SESSION['user'] = $user;
        $_SESSION['role'] = $role;

        if ($role === 'citizen') {
            $_SESSION['citizen_id'] = $user['citizen_id'];
            header("Location: citizen_dashboard.php");
        } else {
            $_SESSION['admin_id'] = $user['admin_id'];
            header("Location: admin_dashboard.php");
        }
        exit();
    } else {
        echo "Incorrect password.";
    }
} else {
    echo "No user found.";
}

$stmt->close();
$conn->close();
?>
