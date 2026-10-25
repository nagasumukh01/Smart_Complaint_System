<?php
session_start();
$isCitizen = isset($_SESSION['role']) && $_SESSION['role'] === 'citizen';
$host = "127.0.0.1:3307";
$user = "root";
$password = "";
$database = "problem_reporter";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get problem
$stmt = $conn->prepare("SELECT * FROM problems WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$problem = $result->fetch_assoc();
$stmt->close();
// Handle new comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_text'])) {
    $newComment = trim($_POST['comment_text']);
    if (!empty($newComment)) {
        $stmt = $conn->prepare("INSERT INTO comments (problem_id, comment) VALUES (?, ?)");
        $stmt->bind_param("is", $id, $newComment);
        $stmt->execute();
        $stmt->close();
        header("Location: problem_detail.php?id=" . $id); // Prevent resubmission
        exit;
    }
}
if (!$problem) {
    echo "Problem not found.";
    exit;
}

// Get votes
$voteStmt = $conn->prepare("SELECT vote_type, COUNT(*) as total FROM votes WHERE problem_id = ? GROUP BY vote_type");
$voteStmt->bind_param("i", $id);
$voteStmt->execute();
$voteResult = $voteStmt->get_result();

$upvotes = 0;
$downvotes = 0;
while ($row = $voteResult->fetch_assoc()) {
    if ($row['vote_type'] == 'up') $upvotes = $row['total'];
    if ($row['vote_type'] == 'down') $downvotes = $row['total'];
}
$voteStmt->close();

// Fetch comments
$commentStmt = $conn->prepare("SELECT * FROM comments WHERE problem_id = ? ORDER BY comment_id DESC");
$commentStmt->bind_param("i", $id);
$commentStmt->execute();
$comments = $commentStmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Problem Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f9f9f9;
            padding: 20px;
        }
        .container {
            max-width: 700px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .back-btn {
            text-decoration: none;
            color: #333;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .share-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .imp-badge {
            background-color:rgb(195, 192, 0);
            color: white;
            padding: 4px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
        }
        .share-btn {
            text-decoration: none;
            font-size: 14px;
            background: #007BFF;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
        }
        .problem-img {
            width: 100%;
            border-radius: 8px;
            margin: 10px 0;
        }
        .title {
            font-size: 22px;
            font-weight: bold;
            margin: 10px 0;
        }
        .desc {
            margin: 10px 0;
            font-size: 16px;
        }
        .btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin: 10px 0;
        }
        .vote-btn {
            padding: 6px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }
        .vote-btn.up {
            background-color: #28a745;
            color: white;
        }
        .vote-btn.down {
            background-color: #dc3545;
            color: white;
        }
        .vote-count {
            margin-left: 6px;
            font-weight: bold;
        }
        .status-btn {
            background-color:rgb(237, 234, 234);
            color: #000;
            border-radius: 6px;
            padding: 6px 16px;
            font-size: 16px;
            text-decoration: none;
            line-height: 34px;
        }
        .status-btn:hover {
            background-color:rgb(255, 251, 251);
        }
        .comments {
            margin-top: 25px;
        }
        .comment {
            background: #f1f1f1;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
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
    <div class="container">
        <div class="top-bar">
            <a href="javascript:history.back()" class="back-btn">&#8592; Back</a>
            <div class="share-wrapper">
                <button class="share-btn" onclick="copyURL()">🔗 Share</button>
            </div>
        </div>

        <div class="title">
            #<?php echo $problem['id']; ?> <?php echo htmlspecialchars($problem['title']); ?>
            <?php if ($upvotes >= 10): ?>
                <span class="imp-badge">IMP</span>
            <?php endif; ?>
        </div>

        <div style="margin-top: 10px; font-weight: bold;">
            <span style="color: #555;">Status:</span>
            <span style="color: <?php echo ($problem['status'] == 'Resolved' ? 'green' : ($problem['status'] == 'in process' ? 'orange' : 'red')); ?>">
                <?php echo ucfirst($problem['status']); ?>
            </span>
        </div>

        <?php if ($problem['image']) { ?>
            <img src="uploads/<?php echo $problem['image']; ?>" class="problem-img" alt="Problem Image">
        <?php } ?>
        <div class="desc"><?php echo nl2br(htmlspecialchars($problem['description'])); ?></div>
        <?php if (!$isCitizen): ?>
            <div style="color: red; font-weight: bold; margin-top: 10px;">
                Note: Only citizens can vote.
            </div>
        <?php endif; ?>

        <div class="btn-group">
            <form method="post" action="upvote.php">
                <input type="hidden" name="id" value="<?php echo $problem['id']; ?>">
                <button class="vote-btn up" type="submit" <?php if (!$isCitizen) echo 'disabled'; ?>>
                    Upvote <span class="vote-count"><?php echo $upvotes; ?></span>
                </button>
            </form>

            <form method="post" action="downvote.php">
                <input type="hidden" name="id" value="<?php echo $problem['id']; ?>">
                <button class="vote-btn down" type="submit" <?php if (!$isCitizen) echo 'disabled'; ?>>
                    Downvote <span class="vote-count"><?php echo $downvotes; ?></span>
                </button>
            </form>

            <form method="post" action="update_status.php" style="display: flex; align-items: center;">
                <input type="hidden" name="id" value="<?php echo $problem['id']; ?>">
                <select name="status" class="status-btn" onchange="this.form.submit()">
                    <option selected disabled>Pending</option>
                    <option value="in process">In Process</option>
                    <option value="resolved">Resolved</option>
                </select>
            </form>
            
        </div>

        <div class="comments">
            <h3>Comments</h3>
            <form method="post" class="add-comment-bar">
                <input type="text" name="comment_text" placeholder="Add a comment..." required>
                <button type="submit">Post</button>
            </form>

            <?php if ($comments->num_rows > 0) {
                while ($comment = $comments->fetch_assoc()) {
                    echo "<div class='comment'>" . htmlspecialchars($comment['comment']) . "</div>";
                }
            } else {
                echo "<p>No comments yet.</p>";
            } ?>
        </div>
    </div>

    <script>
        function copyURL() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                alert("Link copied to clipboard!");
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
<?php $conn->close(); ?>
