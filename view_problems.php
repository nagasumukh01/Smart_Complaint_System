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

// Fetch problems from database
$sql = "SELECT * FROM problems ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registered Problems</title>
    <link rel="stylesheet" href="style.css">
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

        .container {
            padding: 20px;
        }

        .problems-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .problem-card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
            padding: 16px;
            width: 100%;
            max-width: 300px;
            box-sizing: border-box;
        }

        .problem-card img {
            max-width: 100%;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .problem-title {
            font-weight: bold;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .imp-badge {
            background-color:rgb(195, 192, 0);
            color: white;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: bold;
        }

        .problem-description {
            margin-bottom: 10px;
        }

        .actions {
            display: flex;
            justify-content: space-between;
            padding-top: 8px;
            font-size: 18px;
        }

        .action-btn {
            text-decoration: none;
            color: black;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .action-btn:hover {
            opacity: 0.7;
        }

        @media (max-width: 768px) {
            .problem-card {
                max-width: 100%;
            }
        }
        .custom-footer {
        background-color: #00897b;
            color: white;
            padding: 20px 20px;
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

<div class="container">
    <h2 style="text-align:center;">Registered Problems</h2>

    <div class="problems-grid">
        <?php
        $serial = 1;
        while ($row = $result->fetch_assoc()) {
            $problemId = $row['id'];

            // Fetch vote counts
            $upvotes = $conn->query("SELECT COUNT(*) AS count FROM votes WHERE problem_id = $problemId AND vote_type = 'up'")->fetch_assoc()['count'];
            $downvotes = $conn->query("SELECT COUNT(*) AS count FROM votes WHERE problem_id = $problemId AND vote_type = 'down'")->fetch_assoc()['count'];

            // Truncate description if needed
            $desc = strlen($row['description']) > 80 ? substr($row['description'], 0, 80) . "..." : $row['description'];

            echo '<div class="problem-card">';
            
            echo "<div class='problem-title'>";
            echo "<b>" . htmlspecialchars($row['title']) . "</b>";
            if ($upvotes >= 10) {
                $status = strtolower($row['status']);
                $impColor = 'darkgoldenrod'; // Default: dark yellow
                if ($status === 'resolved') {
                    $impColor = 'green';
                }
                echo "<span class='imp-badge' style='background-color: $impColor;'>IMP</span>";
            }

            echo "</div>";

            // ✅ Show image if available
            if (!empty($row['image'])) {
                echo "<a href='problem_detail.php?id=$problemId'><img src='uploads/" . htmlspecialchars($row['image']) . "' alt='Problem Image'></a>";
                echo "<div class='problem-description'>" . htmlspecialchars($desc) . "</div>";
            } else {
                echo "<a href='problem_detail.php?id=$problemId'><div class='problem-description'>" . htmlspecialchars($row['description']) . "</div></a>";
            }
            echo "<div id='error-msg-$problemId' style='color: red; display: none; font-size: 14px; margin-bottom: 8px;'></div>";

            // ✅ Emoji actions
            echo '<div class="actions">';
            echo "<button class='action-btn' onclick='vote($problemId, \"up\")'>👍" . ($upvotes > 0 ? " $upvotes" : "") . "</button>";
            echo "<button class='action-btn' onclick='vote($problemId, \"down\")'>👎" . ($downvotes > 0 ? " $downvotes" : "") . "</button>";
            echo "<a href='problem_detail.php?id=$problemId' class='action-btn'>💬</a>";
            echo "<button class='action-btn' onclick='copyURL($problemId)'>🔗</button>";
            echo '</div>';

            echo '</div>';
            $serial++;
        }
        ?>
    </div>
</div>

<script>
function vote(problemId, type) {
    fetch(type + 'vote.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + problemId
    }).then(res => res.text())
    .then(data => {
        location.reload();
    });
}

function copyURL(problemId) {
    const url = window.location.origin + '/DBMS-project/problem_detail.php?id=' + problemId;
    navigator.clipboard.writeText(url).then(() => {
        alert('Link copied to clipboard!');
    });
}
function vote(problemId, type) {
    fetch(type + 'vote.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + problemId
    })
    .then(res => res.text())
    .then(data => {
        if (data.startsWith("error:")) {
            const msg = data.replace("error:", "");
            const msgBox = document.getElementById("error-msg-" + problemId);
            if (msgBox) {
                msgBox.innerText = msg;
                msgBox.style.display = 'block';
            }
        } else {
            location.reload();
        }
    });
}

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
