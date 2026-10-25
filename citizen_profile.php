<?php
session_start();
$citizen_id = $_SESSION['citizen_id'];

$conn = new mysqli("127.0.0.1:3307", "root", "", "problem_reporter");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get citizen info
$stmt = $conn->prepare("SELECT name, email, phone, age, occupation, bio, profile_image FROM citizens WHERE citizen_id = ?");
$stmt->bind_param("i", $citizen_id);
$stmt->execute();
$stmt->bind_result($name, $email, $phone, $age, $occupation, $bio, $profile_image);
$stmt->fetch();
$stmt->close();

// Get user's problems
$problems = [];
$result = $conn->query("SELECT id, title, status FROM problems WHERE citizen_id = $citizen_id ORDER BY id DESC");
while ($row = $result->fetch_assoc()) {
    $problems[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 20px;
        }

        .profile-container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .profile-details {
            flex: 1;
        }

        .profile-photo {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #afd5ff;
        }

        .profile-details h3 {
            margin-bottom: 5px;
            font-size: 24px;
        }

        .profile-details p {
            font-size: 16px;
            color: #555;
            margin: 4px 0;
        }

        .my-problems {
            margin-top: 40px;
        }

        .my-problems h3 {
            margin-bottom: 15px;
        }

        .problem-item {
            background: #f1f1f1;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .problem-item a {
            color: #007bff;
            text-decoration: none;
        }

        .problem-item a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<a href="citizen_dashboard.php" style="margin: 1px; display: inline-block; background-color: #f1f1f1; border-radius: 6px; padding: 8px 15px; font-size: 16px; cursor: pointer; text-decoration: none; color: black; border: 1px solid #ccc;">← Back</a>

<div class="profile-container">
    <div class="profile-header">
        <div class="profile-details">
            <h3><?= htmlspecialchars($name) ?></h3>
            <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($phone) ?></p>
            <?php if (!empty($age)): ?><p><strong>Age:</strong> <?= htmlspecialchars($age) ?></p><?php endif; ?>
            <?php if (!empty($occupation)): ?><p><strong>Occupation:</strong> <?= htmlspecialchars($occupation) ?></p><?php endif; ?>
            <?php if (!empty($bio)): ?><p><strong>Bio:</strong> <?= nl2br(htmlspecialchars($bio)) ?></p><?php endif; ?>
        </div>
        <div>
            <img src="<?= $profile_image ? $profile_image : 'default-user.png' ?>" alt="Profile Photo" class="profile-photo">
        </div>
    </div>

    <div style="text-align: left; margin-top: 20px;">
        <a href="edit_profile.php" style="background-color:rgb(227, 227, 227); color: black; padding: 10px 16px; border-radius: 6px; text-decoration: none;">Edit Profile</a>
    </div>

    <div class="my-problems">
        <h3>📝 My Submitted Problems</h3>
        <?php if (count($problems) > 0): ?>
            <?php foreach ($problems as $prob): ?>
                <div class="problem-item">
                    <strong>#<?= $prob['id'] ?> - <?= htmlspecialchars($prob['title']) ?></strong><br>
                    <span>Status: <b><?= ucfirst($prob['status']) ?></b></span><br>
                    <a href="problem_detail.php?id=<?= $prob['id'] ?>">View Details</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No problems submitted yet.</p>
        <?php endif; ?>
    </div>
    <div class="thank-you">
        <h3 style="margin-top: 50px;margin-left: 250px; color: #2e7d32;">Thank You for Your Contribution!</h3>
        <p style="color: #444; font-size: 15px;">
            Dear Citizen,<br>
            &emsp;&emsp;We sincerely appreciate your active participation in reporting public issues through the <strong>Sahayak Platform</strong>. 
            Your voice plays a vital role in building a better and more accountable community. 
            <br>
            With every issue you raise, we move a step closer to solving real-world problems and improving our city’s infrastructure and well-being.
            <br><br>
            <strong>Keep engaging, keep empowering.</strong><br>
            Together, let's build a brighter tomorrow. 🌟
        </p>
        <p style="text-align: right; font-style: italic; color: #2e7d32;">
            — Team Sahayak
        </p>
    </div>

</div>
</body>
</html>
