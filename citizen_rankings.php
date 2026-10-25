<?php
$conn = new mysqli("127.0.0.1:3307", "root", "", "problem_reporter");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$today = date('Y-m-d');
$week_start = date('Y-m-d', strtotime('-7 days'));

// Weekly Rankings
$weekly_sql = "
    SELECT c.name, c.profile_image, COUNT(p.id) AS count
    FROM citizens c
    JOIN problems p ON c.citizen_id = p.citizen_id
    WHERE p.created_at BETWEEN '$week_start' AND '$today'
    GROUP BY c.citizen_id
    ORDER BY count DESC
    LIMIT 10
";
$weekly_result = $conn->query($weekly_sql);

// Overall Rankings
$overall_sql = "
    SELECT c.name, c.profile_image, COUNT(p.id) AS count
    FROM citizens c
    JOIN problems p ON c.citizen_id = p.citizen_id
    GROUP BY c.citizen_id
    ORDER BY count DESC
    LIMIT 10
";
$overall_result = $conn->query($overall_sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Citizen Rankings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 30px;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
        }
        .ranking-section {
            margin-bottom: 40px;
        }
        .ranking-card {
            display: flex;
            align-items: center;
            gap: 20px;
            background: #f1f1f1;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .ranking-card img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #007bff;
        }
        .rank {
            font-weight: bold;
            font-size: 20px;
            width: 40px;
        }
    </style>
</head>
<body>
<a href="admin_dashboard.php" style="
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
<div class="container">
    <h2>🏆 Citizen Submission Rankings</h2>
    <br>
    <div class="ranking-section">
        <h3><strong> 𓆩✧𓆪 Weekly Leaderboard (<?= $week_start ?> to <?= $today ?>)</strong></h3>
        <?php
        $rank = 1;
        $medals = ["🥇", "🥈", "🥉"];
        while ($row = $weekly_result->fetch_assoc()) {
            echo "<div class='ranking-card'>";
            echo "<div class='rank'>" . ("#$rank") . "</div>";
            echo "<img src='" . ($row['profile_image'] ?: 'default-user.png') . "' alt='User'>";
            echo "<div><strong>" . htmlspecialchars($row['name']) . "</strong><br>Problems: " . $row['count'] . "</div>";
            echo "</div>";
            $rank++;
        }
        ?>
    </div>

    <div class="ranking-section">
        <h3><strong>🏅 Overall Top Submitters</strong></h3>
        <?php
        $rank = 1;
        $medals = ["🥇", "🥈", "🥉"];
        while ($row = $overall_result->fetch_assoc()) {
            echo "<div class='ranking-card'>";
            echo "<div class='rank'>" . ("#$rank") . "</div>";
            echo "<img src='" . ($row['profile_image'] ?: 'default-user.png') . "' alt='User'>";
            echo "<div><strong>" . htmlspecialchars($row['name']) . "</strong><br>Problems: " . $row['count'] . "</div>";
            echo "</div>";
            $rank++;
        }
        ?>
    </div>
</div>
</body>
</html>
