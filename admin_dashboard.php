<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .dashboard-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            max-width: 400px;
            margin: 0 auto;
            text-align: center;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        .dashboard-buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .dashboard-buttons button {
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            text-decoration: none;
            background-color: #4CAF50;
        }

        .logout-button {
            background-color: #f44336 !important;
        }

        .utility-boxes {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 40px;
        }

        .utility-box {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
            padding: 20px;
            width: 300px;
            min-height: 180px;
        }

        .utility-box h3 {
            margin-top: 0;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .feedback-scroll {
            max-height: 200px;
            overflow: hidden;
            position: relative;
            font-size: 16px;
            color: #333;
        }


        .feedback-scroll p {
            margin-bottom: 12px;
        }

        @media screen and (max-width: 768px) {
            .utility-boxes {
                flex-direction: column;
                align-items: center;
            }
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
            .leaderboard-box {
                text-align: center;
            }

            .leaderboard-podium {
                display: flex;
                justify-content: center;
                align-items: flex-end;
                margin-top: 25px;
                gap: 30px;
            }

            .podium-card {
                background-color: #f9f9f9;
                border-radius: 10px;
                padding: 5px;
                width: 100px;
                box-shadow: 0 0 8px rgba(0,0,0,0.1);
            }

            .podium-card img {
                width: 70px;
                height: 70px;
                border-radius: 50%;
                object-fit: cover;
                border: 3px solid #007bff;
            }

            .podium-card h4 {
                margin: 10px 0 4px;
                font-size: 14px;
            }

            .podium-card p {
                margin: 0;
                font-size: 13px;
                color: #555;
            }

            /* Size & position adjustments */
            .podium-card.first {
                transform: scale(1.2);
                margin-bottom: 20px;
            }
            .podium-card.second {
                transform: scale(1);
                margin-bottom: 0;
            }
            .podium-card.third {
                transform: scale(1);
                margin-bottom: 0;
            }

    </style>
</head>
<body>

<div class="dashboard-container">
    <h2>Admin Dashboard</h2>

    <div class="dashboard-buttons">
        <button onclick="location.href='view_problems.php';">View All Problems</button>
        <button onclick="location.href='charts.php';">Category Chart</button>
        <button onclick="location.href='status_chart.php';">Status Chart</button>
        <button onclick="location.href='filter_problems.php';">Filter Problems</button>
        <button onclick="location.href='search_problems.php';">Search Problems</button>
        <button class="logout-button" onclick="location.href='logout.php';">Logout</button>
    </div>
</div>

<!-- Utility Boxes Section -->
<div class="utility-boxes">
    <!-- Box 1: Maintenance -->
    <div class="utility-box">
        <h3>📅 Upcoming Schedules</h3>
        <ul style="list-style-type: none; padding-left: 0;">
            <br>
            <li><strong>Road repair</strong> – JP Nagar<br><small>10 July 2025</small></li>
            <br>
            <li><strong>Water pipe fix</strong> – Whitefield<br><small>12 July 2025</small></li>
            <br>
            <li><strong>Streetlight install</strong> – Indiranagar<br><small>15 July 2025</small></li>
        </ul>
    </div>

    <!-- Box 2: Feedback Display Only -->
    <div class="utility-box">
        <h3>📩 Citizen Feedback</h3>
        <div class="feedback-scroll" id="feedbackScroller">
            <?php
            $conn = new mysqli("127.0.0.1:3307", "root", "", "problem_reporter");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            $result = $conn->query("SELECT message FROM feedbacks ORDER BY submitted_at DESC LIMIT 10");
            while ($row = $result->fetch_assoc()) {
                echo "<p><em>" . htmlspecialchars($row['message']) . "</em></p>";
            }
            $conn->close();
            ?>
        </div>

    </div>

    <!-- Box 3: Weekly Leaderboard -->
    <a href="citizen_rankings.php" style="text-decoration: none; color: inherit;">
    <div class="utility-box leaderboard-box">
        <h3>🏆 Weekly Leaderboard</h3>
        <div class="leaderboard-podium">
            <?php
            $conn = new mysqli("127.0.0.1:3307", "root", "", "problem_reporter");
            if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

            $query = "
                SELECT c.name, c.profile_image, COUNT(p.id) AS problem_count
                FROM citizens c
                LEFT JOIN problems p ON c.citizen_id = p.citizen_id
                WHERE p.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                GROUP BY c.citizen_id
                ORDER BY problem_count DESC
                LIMIT 3
            ";
            $result = $conn->query($query);
            $citizens = [];
            while ($row = $result->fetch_assoc()) {
                $citizens[] = $row;
            }
            $conn->close();

            // Display podium positions
            $positions = [1 => 'second', 0 => 'first', 2 => 'third']; // Rearranged
            $medals = ['🥇', '🥈', '🥉'];
            foreach ($positions as $index => $class) {
                if (isset($citizens[$index])) {
                    $c = $citizens[$index];
                    echo "<div class='podium-card $class'>";
                    echo "<img src='" . ($c['profile_image'] ?: 'default-user.png') . "' alt='User' />";
                    echo "<h4>" . $medals[$index] . "<br>" . htmlspecialchars($c['name']) . "</h4>";
                    echo "<p>Problems: " . $c['problem_count'] . "</p>";
                    echo "</div>";
                }
            }

            ?>
        </div>
    </div>
    </a>

</div>
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
                <p>Team:<br>
                    &emsp;~ Kartikeyan Reddy<br>
                    &emsp;~ Monish PS<br>
                    &emsp;~ Mithun Gowda BN<br>
                    &emsp;~ Manoj GR<br></p>
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
<script>
    let scroller = document.getElementById("feedbackScroller");
    let scrollSpeed = 1; // pixels per interval
    let scrollInterval = 50; // milliseconds

    function scrollFeedback() {
        scroller.scrollTop += scrollSpeed;
        // Reset if scrolled to bottom
        if (scroller.scrollTop + scroller.clientHeight >= scroller.scrollHeight) {
            scroller.scrollTop = 0;
        }
    }

    setInterval(scrollFeedback, scrollInterval);
</script>

</body>
</html>
