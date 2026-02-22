<?php
require_once 'config.php';
requireLogin();

$tutor_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get tutor details
$stmt = $conn->prepare("SELECT * FROM tutors WHERE id = ?");
$stmt->bind_param("i", $tutor_id);
$stmt->execute();
$tutor = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$tutor) {
    redirect('tutors.php');
}

// Get ratings
$ratings = $conn->query("SELECT ratings.*, students.name as student_name 
                         FROM ratings 
                         JOIN students ON ratings.student_id = students.id 
                         WHERE tutor_id = $tutor_id 
                         ORDER BY created_at DESC");

// Handle new rating submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['user_type'] == 'student') {
    $student_id = $_SESSION['user_id'];
    $rating = intval($_POST['rating']);
    $review = sanitize($_POST['review']);
    
    $stmt = $conn->prepare("INSERT INTO ratings (student_id, tutor_id, rating, review) VALUES (?, ?, ?, ?) 
                           ON DUPLICATE KEY UPDATE rating = ?, review = ?");
    $stmt->bind_param("iiisis", $student_id, $tutor_id, $rating, $review, $rating, $review);
    
    if ($stmt->execute()) {
        // Update tutor's average rating
        $avg = $conn->query("SELECT AVG(rating) as avg, COUNT(*) as total FROM ratings WHERE tutor_id = $tutor_id")->fetch_assoc();
        $conn->query("UPDATE tutors SET average_rating = {$avg['avg']}, total_ratings = {$avg['total']} WHERE id = $tutor_id");
        header("Location: tutor-profile.php?id=$tutor_id");
        exit();
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tutor['name']); ?> - TutorConnect</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar .logo {
            color: white;
            font-size: 1.8rem;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            padding: 0.5rem 1rem;
            border-radius: 5px;
        }

        .nav-links a:hover {
            background: rgba(255,255,255,0.2);
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .profile-card {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .profile-header {
            display: flex;
            gap: 2rem;
            align-items: start;
            margin-bottom: 2rem;
        }

        .avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .profile-info {
            flex: 1;
        }

        .profile-info h1 {
            color: #333;
            margin-bottom: 0.5rem;
            font-size: 2.5rem;
        }

        .rating-display {
            font-size: 1.5rem;
            color: #ffa500;
            margin-bottom: 1rem;
        }

        .profile-stats {
            display: flex;
            gap: 2rem;
            margin-top: 1rem;
        }

        .stat {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #666;
        }

        .subjects {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin: 1.5rem 0;
        }

        .subject-tag {
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px;
            font-weight: 500;
        }

        .bio-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 15px;
            margin: 2rem 0;
        }

        .bio-section h3 {
            color: #333;
            margin-bottom: 1rem;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        .ratings-section {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            animation: slideIn 0.7s ease;
        }

        .ratings-section h2 {
            color: #333;
            margin-bottom: 2rem;
        }

        .rating-form {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }

        .star-rating {
            display: flex;
            gap: 0.5rem;
            font-size: 2rem;
            margin: 1rem 0;
        }

        .star {
            cursor: pointer;
            transition: all 0.3s;
        }

        .star:hover,
        .star.active {
            color: #ffa500;
            transform: scale(1.2);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }

        .form-group textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            resize: vertical;
            min-height: 100px;
        }

        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .review-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 1rem;
            border-left: 4px solid #667eea;
            animation: fadeInUp 0.5s ease;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .reviewer-name {
            font-weight: 600;
            color: #333;
        }

        .review-rating {
            color: #ffa500;
        }

        .review-text {
            color: #666;
            line-height: 1.6;
        }

        .review-date {
            color: #999;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">🎓 TutorConnect</div>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="tutors.php">Find Tutors</a>
            <a href="reels.php">Reels</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="profile-card">
            <div class="profile-header">
                <div class="avatar">👨‍🏫</div>
                <div class="profile-info">
                    <h1><?php echo htmlspecialchars($tutor['name']); ?></h1>
                    <div class="rating-display">
                        ⭐ <?php echo number_format($tutor['average_rating'], 1); ?> 
                        (<?php echo $tutor['total_ratings']; ?> reviews)
                    </div>
                    <div class="profile-stats">
                        <span class="stat">🎓 <?php echo htmlspecialchars($tutor['qualification']); ?></span>
                        <span class="stat">💼 <?php echo $tutor['experience']; ?> years</span>
                        <span class="stat">💰 ₹<?php echo number_format($tutor['hourly_rate'], 2); ?>/hour</span>
                    </div>
                </div>
            </div>

            <div class="subjects">
                <?php 
                $subjects = explode(',', $tutor['subjects']);
                foreach ($subjects as $subject): 
                ?>
                    <span class="subject-tag"><?php echo trim($subject); ?></span>
                <?php endforeach; ?>
            </div>

            <div class="bio-section">
                <h3>About Me</h3>
                <p><?php echo htmlspecialchars($tutor['bio']); ?></p>
            </div>

            <?php if ($_SESSION['user_type'] == 'student'): ?>
                <div class="action-buttons">
                    <a href="book-tutor.php?id=<?php echo $tutor_id; ?>" class="btn btn-primary">📅 Book Session</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="ratings-section">
            <h2>Ratings & Reviews</h2>

            <?php if ($_SESSION['user_type'] == 'student'): ?>
                <div class="rating-form">
                    <h3>Leave a Review</h3>
                    <form method="POST">
                        <div class="form-group">
                            <label>Your Rating</label>
                            <div class="star-rating" id="starRating">
                                <span class="star" data-value="1">☆</span>
                                <span class="star" data-value="2">☆</span>
                                <span class="star" data-value="3">☆</span>
                                <span class="star" data-value="4">☆</span>
                                <span class="star" data-value="5">☆</span>
                            </div>
                            <input type="hidden" name="rating" id="ratingInput" required>
                        </div>
                        <div class="form-group">
                            <label>Your Review</label>
                            <textarea name="review" required placeholder="Share your experience..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Review</button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if ($ratings->num_rows > 0): ?>
                <?php while ($review = $ratings->fetch_assoc()): ?>
                    <div class="review-card">
                        <div class="review-header">
                            <span class="reviewer-name">👤 <?php echo htmlspecialchars($review['student_name']); ?></span>
                            <span class="review-rating"><?php echo str_repeat('⭐', $review['rating']); ?></span>
                        </div>
                        <p class="review-text"><?php echo htmlspecialchars($review['review']); ?></p>
                        <p class="review-date"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; color: #666; padding: 2rem;">No reviews yet. Be the first to review!</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const stars = document.querySelectorAll('.star');
        const ratingInput = document.getElementById('ratingInput');

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                ratingInput.value = value;
                
                stars.forEach(s => {
                    if (s.getAttribute('data-value') <= value) {
                        s.classList.add('active');
                        s.textContent = '★';
                    } else {
                        s.classList.remove('active');
                        s.textContent = '☆';
                    }
                });
            });
        });
    </script>
</body>
</html>