<?php
session_start();
if (!isset($_SESSION['citizen_id'])) {
    header("Location: login.html");
    exit();
}

$citizenId = $_SESSION['citizen_id'];

$conn = new mysqli("127.0.0.1:3307", "root", "", "problem_reporter");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch current details
$stmt = $conn->prepare("SELECT name, email, phone, age, occupation, bio, profile_image FROM citizens WHERE citizen_id = ?");
$stmt->bind_param("i", $citizenId);
$stmt->execute();
$result = $stmt->get_result();
$citizen = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <a href="javascript:history.back()" style="
    margin: 1px;
    display: inline-block;
    background-color: #f1f1f1;
    border-radius: 6px;
    padding: 8px 15px;
    font-size: 16px;
    cursor: pointer;
    text-decoration: none;
    color: black;
    border: 1px solid #ccc;">← Back</a>
    <title>Edit Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            padding: 20px;
        }

        .form-container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        input[type="file"] {
            margin-top: 5px;
        }

        button {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Edit Profile</h2>
    <form action="update_profile.php" method="POST" enctype="multipart/form-data">
        <label for="profile_image">Profile Photo:</label>
        <?php if (!empty($citizen['profile_image'])): ?>
            <img src="uploads/<?= $citizen['profile_image'] ?>" alt="Profile" style="width:100px; height:100px; border-radius:50%; margin-top:5px;"><br>
        <?php endif; ?>
        <input type="file" name="profile_image">

        <label for="name">Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($citizen['name']) ?>" required>

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($citizen['email']) ?>" required>

        <label for="phone">Phone:</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($citizen['phone']) ?>" required>

        <label for="age">Age (optional):</label>
        <input type="text" name="age" value="<?= htmlspecialchars($citizen['age']) ?>">

        <label for="occupation">Occupation (optional):</label>
        <input type="text" name="occupation" value="<?= htmlspecialchars($citizen['occupation']) ?>">

        <label for="bio">Bio (optional):</label>
        <textarea name="bio" rows="4"><?= htmlspecialchars($citizen['bio']) ?></textarea>

        <button type="submit">Update Profile</button>
    </form>
</div>

</body>
</html>

<?php $conn->close(); ?>
