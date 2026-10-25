<?php
session_start();

if (!isset($_SESSION['citizen_id'])) {
    die("You must be logged in as a citizen to submit a problem.");
}

$conn = new mysqli("127.0.0.1:3307", "root", "", "problem_reporter");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch dropdown values
$locations = $conn->query("SELECT location_id, name FROM locations");
$categories = $conn->query("SELECT category_id, name FROM categories");

// If form submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $location_id = $_POST['location_id'];
    $category_id = $_POST['category_id'];
    $severity = $_POST['severity'];
    $citizen_id = $_SESSION['citizen_id'];
    $authority_id = 1; // Static
    $status = "Pending";

    $filename = null;
    if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $filename = uniqid() . "_" . basename($_FILES['file']['name']);
        $targetFile = $uploadDir . $filename;

        if (!move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
            die("File upload failed.");
        }
    }

    $stmt = $conn->prepare("INSERT INTO problems (title, description, location_id, citizen_id, category_id, authority_id, severity, status, image)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiiisss", $title, $description, $location_id, $citizen_id, $category_id, $authority_id, $severity, $status, $filename);

    if ($stmt->execute()) {
        echo "<script>alert('Problem submitted successfully!'); window.location.href='citizen_dashboard.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Problem</title>
    <link rel="stylesheet" href="style.css">
    <style>
        form {
            max-width: 600px;
            margin: 30px auto;
            background: #f9f9f9;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        input[type="file"] {
            margin-bottom: 15px;
        }

        input[type="submit"] {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        h2 {
            text-align: center;
        }
        .custom-footer {
        background-color: #00897b;
            color: white;
            padding: 40px 20px;
            margin-top: 70px;
            font-family: 'Segoe UI', sans-serif;
            }

            .footer-top {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            border-bottom: 1px solid #ffffff4d;
            padding-bottom: 30px;
            }

            .footer-logo-section {
            display: flex;
            align-items: center;
            width: 100%;
            justify-content: center;
            margin-top: 30px;
            margin-bottom: 30px;
            }

            .footer-logo {
            width: 270px;
            height: 270px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 200px;
            border: 4px solid white;
            }

            .footer-info {
            max-width: 600px;
            }

            .footer-title {
            font-size: 28px;
            margin-right: 250px;
            margin-bottom: 10px;
            }

            .footer-desc {
            font-size: 16px;
            font-family:'Georgia', serif;
            line-height: 1.5;
            }

            .footer-bottom {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin-top: 10px;
            }

            .footer-column {
            flex: 1 1 250px;
            margin: 100px;
            }

            .footer-column h4 {
            margin-bottom: 15px;
            font-size: 18px;
            }

            .footer-column ul {
            list-style: none;
            padding: 0;
            }

            .footer-column ul li {
            margin-bottom: 30px;
            font-size: 15px;
            }

            .footer-column ul li a {
            text-decoration: none;
            color: white;
            }

            .footer-column ul li a:hover {
            text-decoration: underline;
            }

    </style>
</head>
<body>
<a href="citizen_dashboard.php" class="back-button">← Back</a>
<h2>Submit a Problem</h2>

<form action="" method="POST" enctype="multipart/form-data">
    <label>Title:</label>
    <input type="text" name="title" required>

    <label>Description:</label>
    <textarea name="description" rows="4" required></textarea>

    <label>Location:</label>
    <select name="location_id" required>
        <option value="">-- Select Location --</option>
        <?php while ($loc = $locations->fetch_assoc()): ?>
            <option value="<?= $loc['location_id'] ?>"><?= $loc['location_id'] . ' - ' . $loc['name'] ?></option>
        <?php endwhile; ?>
    </select>

    <label>Category:</label>
    <select name="category_id" required>
        <option value="">-- Select Category --</option>
        <?php while ($cat = $categories->fetch_assoc()): ?>
            <option value="<?= $cat['category_id'] ?>"><?= $cat['category_id'] . ' - ' . $cat['name'] ?></option>
        <?php endwhile; ?>
    </select>

    <label>Severity:</label>
    <select name="severity" required>
        <option value="">-- Select Severity --</option>
        <option value="1">1 - Low</option>
        <option value="2">2 - Medium</option>
        <option value="3">3 - High</option>
    </select>

    <label>Optional Image:</label>
    <input type="file" name="file" accept="image/*">

    <input type="submit" value="Submit Problem">
</form>
<footer class="custom-footer">
        <div class="footer-top">
            <div class="footer-logo-section">
            <img src="image/sahayak.png" alt="Company Logo" class="footer-logo">
            <div class="footer-info">
                <h2 class="footer-title">SAHAYAK ORGANIZATION</h2>
                <p class="footer-desc">~ Serving individuals with full confidence and ameliorating the society to a better future. Initiated by the members of SAHAYAK.</p>
            </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-column">
            <h4>About Us</h4>
            <ul>
                <li><a href="http://localhost/DBMS-project/view_problems.php">Our Mission</a></li>
                <li>Team:</li>
                <li>    ~ Kartikeyan Reddy</li>
                <li>    ~ Monish PS</li>
                <li>    ~ Mithun Gowda BN</li>
                <li>    ~ Manoj GR</li>
                <li><a href="#">Careers</a></li>
            </ul>
            </div>
            <div class="footer-column">
            <h4>Services</h4>
            <ul>
                <li><a href="http://localhost/DBMS-project/submit_problem.php">Report Issues</a></li>
                <li><a href="http://localhost/DBMS-project/view_problems.php">View Problems</a></li>
                <li><a href="http://localhost/DBMS-project/view_problems.php">Track Complaints</a></li>
                <li><a href="http://localhost/DBMS-project/status_chart.php ">Community Insights</a></li>
            </ul>
            </div>
            <div class="footer-column">
            <h4>Contact</h4>
            <ul>
                <li>Email: 23020500469@reva.edu.in</li>
                <li>Phone: +91 93531 50725</li>
                <li>Location: Kattigenahalli, Bengaluru, Karnataka, India</li>
            </ul>
            </div>
        </div>
    </footer>

</body>
</html>
