<?php
$host = "127.0.0.1";
$port = 3307; // You mentioned before that your MySQL runs on 3307
$user = "root";
$password = "";
$dbname = "problem_reporter"; // Replace with your actual DB name

$conn = new mysqli($host, $user, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
