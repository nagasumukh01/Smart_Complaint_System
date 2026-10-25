<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'citizen') {
    header("Location: login.html");
    exit();
}

$conn = new mysqli("127.0.0.1:3307", "root", "", "problem_reporter");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch trending problems (top 5 with most upvotes and comments)
$trending_sql = "
    SELECT p.*, 
           (SELECT COUNT(*) FROM votes v WHERE v.problem_id = p.id AND v.vote_type = 'up') AS upvotes,
           (SELECT COUNT(*) FROM comments c WHERE c.problem_id = p.id) AS comment_count
    FROM problems p
    ORDER BY upvotes DESC, comment_count DESC
    LIMIT 6
";
$trending_result = $conn->query($trending_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Citizen Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <style>
        form button {
            display: block;
            width: 100%;
            margin-bottom: 15px;
        }
        .trending-section {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }
        .trending-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            justify-items: center;
        }

        @media (min-width: 1000px) {
            .trending-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .trending-card {
            width: 100%;
            max-width: 320px; /* Optional: Keeps card width controlled */
            text-decoration: none;
            background: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s ease;
            min-height: 240px;
        }

        .trending-card:hover {
            transform: scale(1.02);
        }
        .trending-img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .no-img {
            width: 100%;
            height: 150px;
            background: #ddd;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 14px;
            color: #666;
        }
        .trending-info {
            padding: 10px;
        }
        .trending-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
            color: #333;
        }
        .trending-meta {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #555;
        }
        .status {
            padding: 2px 6px;
            border-radius: 6px;
            font-weight: bold;
            text-transform: capitalize;
        }
        .status.pending {
            background: #ffe8e8;
            color: red;
        }
        .status.in\ process {
            background: #fff4cc;
            color: orange;
        }
        .status.resolved {
            background: #e6ffea;
            color: green;
        }
        .view-all-link {
            text-align: center;
            margin-top: 15px;
        }
        .view-all-btn {
            text-decoration: none;
            background: #007bff;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: bold;
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
            .profile-dropdown {
                position: absolute;
                top: 20px;
                right: 20px;
            }

            .profile-icon {
                background-color:rgb(238, 228, 228);
                color: white;
                width: 45px;
                height: 45px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                cursor: pointer;
                box-shadow: 0 0 5px rgba(0,0,0,0.2);
            }

            .dropdown-menu {
                display: none;
                position: absolute;
                right: 0;
                background-color: white;
                box-shadow: 0 2px 10px rgba(0,0,0,0.2);
                border-radius: 8px;
                margin-top: 10px;
                overflow: hidden;
                z-index: 1000;
                min-width: 150px;
            }

            .dropdown-menu a {
                display: block;
                padding: 12px 16px;
                color: #333;
                text-decoration: none;
                font-size: 15px;
                border-bottom: 1px solid #eee;
            }

            .dropdown-menu a:hover {
                background-color: #f5f5f5;
            }
    </style>
</head>
<body>
    <div class="profile-dropdown">
        <div class="profile-icon" onclick="toggleDropdown()">👤</div>
        <div class="dropdown-menu" id="dropdownMenu">
            <a href="citizen_profile.php">Manage Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <form>
        <h2>Citizen Dashboard</h2>
        <button onclick="location.href='submit_problem.php'; return false;">Submit a Problem</button>
        <button onclick="location.href='view_problems.php'; return false;">View Submitted Problems</button>
        <button onclick="location.href='status_chart.php'; return false;">View Status Chart</button>
        <button onclick="location.href='logout.php'; return false;" style="background-color: #f44336;">Logout</button>
    </form>

    <div class="trending-section">
        <h3>🔥 Trending Problems</h3>
        <?php if ($trending_result && $trending_result->num_rows > 0): ?>
            <div class="trending-grid">
                <?php while ($row = $trending_result->fetch_assoc()): ?>
                    <a href="problem_detail.php?id=<?= $row['id'] ?>" class="trending-card">
                        <?php if (!empty($row['image'])): ?>
                            <img src="uploads/<?= $row['image'] ?>" alt="Image" class="trending-img">
                        <?php else: ?>
                            <div class="no-img">No Image</div>
                        <?php endif; ?>
                        <div class="trending-info">
                            <div class="trending-title" title="<?= htmlspecialchars($row['title']) ?>">
                                #<?= $row['id'] ?> <?= mb_strimwidth(htmlspecialchars($row['title']), 0, 50, "...") ?>
                            </div>
                            <div class="trending-meta">
                                <span class="status <?= strtolower($row['status']) ?>"><?= ucfirst($row['status']) ?></span>
                                <span>👍 <?= $row['upvotes'] ?></span>
                                <span>💬 <?= $row['comment_count'] ?></span>
                            </div>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
            <div class="view-all-link">
                <a href="view_problems.php" class="view-all-btn">View All Trending</a>
            </div>
        <?php else: ?>
            <p>No trending problems at the moment.</p>
        <?php endif; ?>
    </div>
    <!-- Feedback Box on Citizen Dashboard -->
    <!-- ✍️ Submit Feedback Section -->
    <div style="display: flex; justify-content: center; margin-top: 40px;">
        <div style="width: 800px; padding: 25px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); background-color: #fff;">
            <h3 style="text-align: center; margin-bottom: 15px;">Submit Feedback</h3>
            <?php
            if (isset($_SESSION['feedback_success'])) {
                echo "<p style='color: green; text-align: center; font-size: 14px;'>" . $_SESSION['feedback_success'] . "</p>";
                unset($_SESSION['feedback_success']);
            }
            ?>
            <form action="submit_feedback.php" method="POST">
                <textarea name="feedback" rows="9" placeholder="Share your thoughts..." required style="width: 100%; height: 180px; padding: 14px; font-size: 18px; border-radius: 6px; border: 1px solid #ccc;"></textarea>
                <button type="submit" style="margin-top: 10px; width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 5px; font-size: 16px;">Send Feedback</button>
            </form>
        </div>
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
    <script>
        function toggleDropdown() {
            const menu = document.getElementById("dropdownMenu");
            menu.style.display = menu.style.display === "block" ? "none" : "block";
        }

        // Close dropdown if clicked outside
        document.addEventListener("click", function(event) {
            const dropdown = document.getElementById("dropdownMenu");
            const icon = document.querySelector(".profile-icon");
            if (!icon.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.style.display = "none";
            }
        });
    </script>

</body>
</html>

<?php $conn->close(); ?>
