<?php
session_start();

// Decide back button target
$backLink = 'login.php';
if (isset($_SESSION['citizen_id'])) {
    $backLink = 'citizen_dashboard.php';
} elseif (isset($_SESSION['admin_id'])) {
    $backLink = 'admin_dashboard.php';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Problem Status Chart</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .back-button {
            margin: 20px;
            display: inline-block;
            background-color: #f1f1f1;
            border-radius: 6px;
            padding: 8px 15px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            color: black;
            border: 1px solid #ccc;
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
    <h2 style="text-align:center;">Problem Status Distribution</h2>
    <div style="max-width: 600px; margin: 0 auto;">
        <canvas id="statusChart" width="400" height="400"></canvas>
    </div>

    <?php
    $conn = new mysqli("127.0.0.1:3307", "root", "", "problem_reporter");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT status, COUNT(*) AS count FROM problems GROUP BY status";
    $result = $conn->query($sql);

    $statuses = [];
    $counts = [];

    while ($row = $result->fetch_assoc()) {
        $statuses[] = $row['status'];
        $counts[] = $row['count'];
    }

    $conn->close();
    ?>

    <script>
        const ctx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: <?= json_encode($statuses); ?>,
                datasets: [{
                    label: 'Problems by Status',
                    data: <?= json_encode($counts); ?>,
                    backgroundColor: ['#f44336', '#2196f3', '#4caf50', '#ff9800', '#9c27b0']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: { enabled: true }
                }
            }
        });
    </script>
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
