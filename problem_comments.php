<?php
session_start();
if (!isset($_SESSION['role']) || (!isset($_SESSION['citizen_id']) && !isset($_SESSION['admin_id']))) {
    echo "Unauthorized access.";
    exit();
}

$conn = new mysqli("127.0.0.1:3307", "root", "", "problem_reporter");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$problem_id = isset($_GET['problem_id']) ? intval($_GET['problem_id']) : 0;

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $comment = trim($_POST['comment']);
    $user_type = $_SESSION['role'];
    $user_id = $user_type === 'admin' ? $_SESSION['admin_id'] : $_SESSION['citizen_id'];

    if (!empty($comment)) {
        $stmt = $conn->prepare("INSERT INTO comments (problem_id, user_type, user_id, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isis", $problem_id, $user_type, $user_id, $comment);
        $stmt->execute();
    }
}

// Fetch problem details
$problem = $conn->query("SELECT * FROM problems WHERE id = $problem_id")->fetch_assoc();

// Fetch comments
$comment_query = $conn->prepare("SELECT * FROM comments WHERE problem_id = ? ORDER BY timestamp ASC");
$comment_query->bind_param("i", $problem_id);
$comment_query->execute();
$comments = $comment_query->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Problem Comments</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .problem-box, .comment-box, .new-comment { margin-bottom: 20px; border: 1px solid #ccc; padding: 15px; border-radius: 5px; }
        .comment-box { background-color: #f9f9f9; }
        .new-comment textarea { width: 100%; height: 80px; }
        .new-comment input[type="submit"] {
            padding: 8px 16px;
            background-color: #2196F3;
            color: white;
            border: none;
            border-radius: 4px;
            margin-top: 8px;
        }
    </style>
</head>
<body>

<h2>Comments for Problem ID: <?php echo $problem_id; ?></h2>

<div class="problem-box">
    <strong>Title:</strong> <?php echo htmlspecialchars($problem['title']); ?><br>
    <strong>Description:</strong> <?php echo htmlspecialchars($problem['description']); ?><br>
    <strong>Status:</strong> <?php echo htmlspecialchars($problem['status']); ?>
</div>

<?php while ($row = $comments->fetch_assoc()): ?>
    <div class="comment-box">
        <strong><?php echo ucfirst($row['user_type']); ?>:</strong> <?php echo nl2br(htmlspecialchars($row['comment'])); ?><br>
        <small><em><?php echo $row['timestamp']; ?></em></small>
    </div>
<?php endwhile; ?>

<div class="new-comment">
    <form method="post">
        <textarea name="comment" placeholder="Write your comment..." required></textarea><br>
        <input type="submit" value="Post Comment">
    </form>
</div>

</body>
</html>
