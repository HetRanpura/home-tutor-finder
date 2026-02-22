<?php
require_once 'config.php';
requireLogin();

// Get all tutors
$tutors = $conn->query("SELECT * FROM tutors ORDER BY average_rating DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Tutors - TutorConnect</title>
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
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .page-header {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            margin-bottom: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .page-header h1 {
            color: #333;
            margin-bottom: 0.5rem;
        }

        .search-bar {
            margin-top: 1rem;
            position: relative;
        }

        .search-bar input {
            width: 100%;
            padding: 1rem 3rem 1rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .search-bar input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }

        .tutors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
        }

        .tutor-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s;
            animation: fadeInUp 0.6s ease;
            position: relative;
            overflow: hidden;
        }

        .tutor-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transform: scaleX(0);
            transition: transform 0.3s;
        }

        .tutor-card:hover::before {
            transform: scaleX(1);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .tutor-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .tutor-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .tutor-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .tutor-info h3 {
            color: #333;
            margin-bottom: 0.3rem;
        }

        .tutor-rating {
            color: #ffa500;
            font-weight: 600;
        }

        .tutor-details {
            margin: 1rem 0;
            color: #666;
        }

        .tutor-details p {
            margin: 0.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .subjects {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin: 1rem 0;
        }

        .subject-tag {
            padding: 0.3rem 0.8rem;
            background: #e8f0fe;
            color: #667eea;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .tutor-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .btn {
            flex: 1;
            padding: 0.75rem;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            text-align: center;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #f0f0f0;
            color: #666;
        }

        .btn-secondary:hover {
            background: #e0e0e0;
        }

        @media (max-width: 768px) {
            .tutors-grid {
                grid-template-columns: 1fr;
            }
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
            <a href="schedule.php">My Schedule</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>Find Your Perfect Tutor 🔍</h1>
            <p>Browse through our talented tutors and find the best match for you</p>
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search by name, subject, or qualification..." onkeyup="filterTutors()">
            </div>
        </div>

        <div class="tutors-grid" id="tutorsGrid">
            <?php while ($tutor = $tutors->fetch_assoc()): ?>
                <div class="tutor-card" data-search="<?php echo strtolower($tutor['name'] . ' ' . $tutor['subjects'] . ' ' . $tutor['qualification']); ?>">
                    <div class="tutor-header">
                        <div class="tutor-avatar">👨‍🏫</div>
                        <div class="tutor-info">
                            <h3><?php echo htmlspecialchars($tutor['name']); ?></h3>
                            <div class="tutor-rating">
                                ⭐ <?php echo number_format($tutor['average_rating'], 1); ?> (<?php echo $tutor['total_ratings']; ?> reviews)
                            </div>
                        </div>
                    </div>

                    <div class="tutor-details">
                        <p>🎓 <?php echo htmlspecialchars($tutor['qualification']); ?></p>
                        <p>💼 <?php echo $tutor['experience']; ?> years experience</p>
                        <p>💰 ₹<?php echo number_format($tutor['hourly_rate'], 2); ?>/hour</p>
                    </div>

                    <div class="subjects">
                        <?php 
                        $subjects = explode(',', $tutor['subjects']);
                        foreach ($subjects as $subject): 
                        ?>
                            <span class="subject-tag"><?php echo trim($subject); ?></span>
                        <?php endforeach; ?>
                    </div>

                    <p style="color: #666; margin: 1rem 0;"><?php echo htmlspecialchars(substr($tutor['bio'], 0, 100)) . '...'; ?></p>

                    <div class="tutor-actions">
                        <a href="tutor-profile.php?id=<?php echo $tutor['id']; ?>" class="btn btn-primary">View Profile</a>
                        <a href="book-tutor.php?id=<?php echo $tutor['id']; ?>" class="btn btn-secondary">Book Now</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        function filterTutors() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const cards = document.querySelectorAll('.tutor-card');
            
            cards.forEach(card => {
                const searchData = card.getAttribute('data-search');
                if (searchData.includes(input)) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeInUp 0.5s ease';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>