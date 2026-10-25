<?php
$conn = new mysqli("127.0.0.1:3307", "root", "", "problem_reporter");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch only resolved problems
$resolved = $conn->query("
    SELECT p.id, p.title, l.name
    FROM problems p
    JOIN locations l ON p.location_id = l.location_id
    WHERE p.status = 'Resolved'
    ORDER BY p.id DESC
");

?>
<!DOCTYPE html>
<html>
<head>
    <title>Organization Details - Sahayak</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f4f4;
            padding: 40px;
            color: #333;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }
        h2 {
            color: #00897b;
            margin-bottom: 10px;
        }
        p {
            font-size: 16px;
            line-height: 1.6;
        }
        .section {
            margin-top: 40px;
        }
        ul {
            margin-top: 10px;
            padding-left: 20px;
        }
        li {
            margin-bottom: 10px;
        }
        .highlight {
            color: green;
            font-weight: bold;
        }
        .resolved-list {
            background-color: #f6fff6;
            border-left: 4px solid #28a745;
            padding: 10px 15px;
            margin-bottom: 12px;
            border-radius: 6px;
        }
        
    </style>
</head>
<body>
    <a href="javascript:history.back()" style="
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
        <h2>About Sahayak Organization</h2>
        <p><strong>Sahayak</strong> is a grassroots non-profit organization committed to empowering rural communities across India. Its work focuses on five key areas—health, nutrition, agriculture, livelihood, and ecology—using education as the foundation for change. By introducing sustainable, low-cost technologies and locally adapted solutions, Sahayak equips individuals and families with the knowledge and tools they need to improve their everyday lives.</p>
        
        <p>Through kitchen gardening, natural farming, health education, and nutrition awareness, the organization fosters self-reliance and resilience among marginalized communities. Special emphasis is placed on women, children, and the elderly, ensuring inclusive development that addresses the needs of the most vulnerable.</p>

        <p>Sahayak believes in creating long-term impact through participation, dignity, and trust. Its mission is not just to deliver services, but to build capable, informed communities that can sustain their own growth—socially, economically, and ecologically.</p>
        
        <p><strong>Vision:</strong> To enhance civic engagement and transparency by enabling direct reporting and communication between citizens and authorities.</p>

        <p><strong>Location:</strong> Bengaluru, India</p>
        <p><strong>Email:</strong> <a href="mailto:support@sahayak.org">support@sahayak.org</a></p>
        <p><strong>Phone:</strong> +91 98765 43210</p>
        <!-- Achievements Section -->
        <div class="section">
            <h2>🏆 Recent Achievements</h2>
            <?php if ($resolved && $resolved->num_rows > 0): ?>
                <?php while ($row = $resolved->fetch_assoc()): ?>
                    <div class="resolved-list">
                        <span class="highlight">#<?= $row['id'] ?> <?= htmlspecialchars($row['title']) ?></span><br>
                        <small>Resolved from : <?= htmlspecialchars($row['name']) ?></small>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No resolved problems to show at the moment.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
