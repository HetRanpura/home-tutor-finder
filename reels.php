<?php
require_once 'config.php';
requireLogin();

// Get all reels with tutor info
$reels_query = "SELECT reels.*, tutors.name as tutor_name, tutors.profile_image 
                FROM reels 
                JOIN tutors ON reels.tutor_id = tutors.id 
                ORDER BY reels.created_at DESC";
$reels = $conn->query($reels_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Educational Reels - TutorConnect</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #000;
            color: white;
        }

        .navbar {
            background: rgba(0,0,0,0.9);
            backdrop-filter: blur(10px);
            padding: 1rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
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
            background: rgba(255,255,255,0.1);
        }

        .reels-container {
            margin-top: 80px;
            padding: 2rem;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
            animation: fadeIn 0.8s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .page-header h1 {
            font-size: 3rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, #667eea, #764ba2, #f093fb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradient 3s ease infinite;
            background-size: 200% 200%;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .reels-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }

        .reel-card {
            background: #1a1a1a;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s;
            animation: fadeInUp 0.6s ease;
            cursor: pointer;
            position: relative;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .reel-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 60px rgba(102, 126, 234, 0.5);
        }

        .reel-thumbnail {
            width: 100%;
            height: 400px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 5rem;
            position: relative;
            overflow: hidden;
        }

        .reel-thumbnail::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            animation: shine 2s infinite;
        }

        @keyframes shine {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }

        .play-button {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            transition: all 0.3s;
        }

        .reel-card:hover .play-button {
            transform: translate(-50%, -50%) scale(1.2);
            background: white;
        }

        .reel-info {
            padding: 1.5rem;
        }

        .reel-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .tutor-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .reel-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: white;
        }

        .reel-subject {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            background: rgba(102, 126, 234, 0.2);
            border-radius: 15px;
            font-size: 0.85rem;
            color: #667eea;
            margin-top: 0.5rem;
        }

        .reel-stats {
            display: flex;
            gap: 1.5rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: #999;
        }

        .stat {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        @media (max-width: 768px) {
            .reels-grid {
                grid-template-columns: 1fr;
            }
            
            .page-header h1 {
                font-size: 2rem;
            }
        }

        .no-reels {
            text-align: center;
            padding: 4rem 2rem;
            color: #666;
        }

        .no-reels h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
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

    <div class="reels-container">
        <div class="page-header">
            <h1>🎬 Educational Reels</h1>
            <p style="color: #999;">Learn from bite-sized video content</p>
        </div>

        <?php if ($reels->num_rows > 0): ?>
            <div class="reels-grid">
                <?php while ($reel = $reels->fetch_assoc()): ?>
                    <div class="reel-card" onclick="window.location.href='view-reel.php?id=<?php echo $reel['id']; ?>'">
                        <div class="reel-thumbnail">
                            🎥
                            <div class="play-button">▶</div>
                        </div>
                        <div class="reel-info">
                            <div class="reel-header">
                                <div class="tutor-avatar">👨‍🏫</div>
                                <span style="color: #999;"><?php echo htmlspecialchars($reel['tutor_name']); ?></span>
                            </div>
                            <div class="reel-title"><?php echo htmlspecialchars($reel['title']); ?></div>
                            <span class="reel-subject"><?php echo htmlspecialchars($reel['subject']); ?></span>
                            <div class="reel-stats">
                                <span class="stat">👁 <?php echo number_format($reel['views']); ?> views</span>
                                <span class="stat">❤️ <?php echo number_format($reel['likes']); ?> likes</span>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-reels">
                <h2>No reels yet</h2>
                <p>Be the first to explore when tutors start sharing content!</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>