<?php
session_start();
include 'db_connect.php';

// Determine back link based on login session
$backLink = 'login.php';
if (isset($_SESSION['citizen_id'])) {
    $backLink = 'citizen_dashboard.php';
} elseif (isset($_SESSION['admin_id'])) {
    $backLink = 'admin_dashboard.php';
}

// Connect to database
$conn = new mysqli("127.0.0.1:3307", "root", "", "problem_reporter");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch values from POST
$location = $_POST['location'] ?? '';
$status = $_POST['status'] ?? '';
$category = $_POST['category'] ?? '';

// Fetch location and category lists
$locationOptions = $conn->query("SELECT location_id, name FROM locations");
$categoryOptions = $conn->query("SELECT category_id, name FROM categories");

// Build search query
$sql = "SELECT * FROM problems WHERE 1=1";
if (!empty($location)) $sql .= " AND location_id = $location";
if (!empty($status)) $sql .= " AND status = '$status'";
if (!empty($category)) $sql .= " AND category_id = $category";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Problems</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        label, select, input[type="submit"] {
            display: block;
            margin: 10px 0;
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
<a href="<?php echo $backLink; ?>" class="back-button">← Back</a>
<h2>Search Problems</h2>
<form method="post">
    <label>Location:</label>
    <select name="location">
        <option value="">-- Select Any One --</option>
        <?php while ($loc = $locationOptions->fetch_assoc()): ?>
            <option value="<?= $loc['location_id'] ?>" <?= $location == $loc['location_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($loc['name']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Status:</label>
    <select name="status">
        <option value="">-- Select Any One --</option>
        <option value="Pending" <?= $status == 'Pending' ? 'selected' : '' ?>>Pending</option>
        <option value="In Progress" <?= $status == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
        <option value="Resolved" <?= $status == 'Resolved' ? 'selected' : '' ?>>Resolved</option>
    </select>

    <label>Category:</label>
    <select name="category">
        <option value="">-- Select Any One --</option>
        <?php while ($cat = $categoryOptions->fetch_assoc()): ?>
            <option value="<?= $cat['category_id'] ?>" <?= $category == $cat['category_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <input type="submit" value="Search">
</form>

<?php
if ($result->num_rows > 0) {
    echo "<h3>Results:</h3><table><tr>
            <th>ID</th><th>Title</th><th>Description</th><th>Status</th><th>Severity</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td data-label>{$row['id']}</td>
                <td data-label>{$row['title']}</td>
                <td data-label>{$row['description']}</td>
                <td data-label>{$row['status']}</td>
                <td data-label>{$row['severity']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p style='text-align:center;'>No problems found.</p>";
}
$conn->close();
?>
<footer class="custom-footer">
        <div class="footer-top">
            <div class="footer-logo-section">
            <a href="organization_details.php">
                <img src="image/sahayak.png" alt="Company Logo" class="footer-logo">
            </a>
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
