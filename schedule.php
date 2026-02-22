<?php
require_once 'config.php';
requireLogin();

if ($_SESSION['user_type'] != 'student') {
    redirect('dashboard.php');
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

$schedule = null;
$error = '';

// Check if schedule exists
$existing = $conn->query("SELECT * FROM ai_schedules WHERE student_id = $user_id ORDER BY created_at DESC LIMIT 1");
if ($existing->num_rows > 0) {
    $schedule_row = $existing->fetch_assoc();
    $schedule = json_decode($schedule_row['schedule_data'], true);
}

// Generate new schedule
if (isset($_POST['generate'])) {
    // This is a mock AI schedule - in production, you'd call Claude API
    $mock_schedule = [
        [
            'day' => 'Monday',
            'time' => '4:00 PM',
            'subject' => 'Mathematics',
            'duration' => 60,
            'topic' => 'Algebra - Quadratic Equations'
        ],
        [
            'day' => 'Tuesday',
            'time' => '4:00 PM',
            'subject' => 'Physics',
            'duration' => 60,
            'topic' => 'Mechanics - Newton\'s Laws'
        ],
        [
            'day' => 'Wednesday',
            'time' => '4:00 PM',
            'subject' => 'Chemistry',
            'duration' => 60,
            'topic' => 'Organic Chemistry Basics'
        ],
        [
            'day' => 'Thursday',
            'time' => '4:00 PM',
            'subject' => 'Mathematics',
            'duration' => 60,
            'topic' => 'Trigonometry'
        ],
        [
            'day' => 'Friday',
            'time' => '4:00 PM',
            'subject' => 'Physics',
            'duration' => 60,
            'topic' => 'Work, Energy and Power'
        ],
        [
            'day' => 'Saturday',
            'time' => '10:00 AM',
            'subject' => 'Revision',
            'duration' => 120,
            'topic' => 'Weekly Review and Practice'
        ]
    ];
    
    $schedule = $mock_schedule;
    $schedule_json = json_encode($mock_schedule);
    
    // Save to database
    $conn->query("DELETE FROM ai_schedules WHERE student_id = $user_id");
    $stmt = $conn->prepare("INSERT INTO ai_schedules (student_id, schedule_data) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $schedule_json);
    $stmt->execute();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Study Schedule - TutorConnect</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .navbar {
            background: rgba(0,0,0,0.3);
            backdrop-filter: blur(10px);
            padding: 1rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
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

        .header-card {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            margin-bottom: 2rem;
            animation: slideIn 0.5s ease;
            text-align: center;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header-card h1 {
            color: #333;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .header-card p {
            color: #666;
            font-size: 1.1rem;
        }

        .generate-section {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            margin-bottom: 2rem;
            animation: slideIn 0.6s ease;
        }

        .student-info {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 1.5rem;
        }

        .student-info h3 {
            color: #667eea;
            margin-bottom: 1rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-item strong {
            color: #333;
        }

        .generate-btn {
            width: 100%;
            padding: 1.2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .generate-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .generate-btn:hover::before {
            width: 500px;
            height: 500px;
        }

        .generate-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        .schedule-container {
            animation: fadeInUp 0.8s ease;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .schedule-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .schedule-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(180deg, #667eea, #764ba2);
        }

        .schedule-card:hover {
            transform: translateX(10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .day-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .day-name {
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea;
        }

        .time-badge {
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px;
            font-weight: 600;
        }

        .schedule-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #666;
        }

        .topic {
            margin-top: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
            color: #333;
        }

        .no-schedule {
            background: white;
            padding: 4rem 2rem;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }

        .no-schedule h2 {
            color: #333;
            margin-bottom: 1rem;
        }

        .no-schedule p {
            color: #666;
            font-size: 1.1rem;
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
        <div class="header-card">
            <h1>🤖 AI-Powered Study Schedule</h1>
            <p>Get a personalized weekly schedule based on your subjects and grade</p>
        </div>

        <div class="generate-section">
            <div class="student-info">
                <h3>Your Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span>📚</span>
                        <span><strong>Grade:</strong> <?php echo htmlspecialchars($student['grade']); ?></span>
                    </div>
                    <div class="info-item">
                        <span>📖</span>
                        <span><strong>Subjects:</strong> <?php echo htmlspecialchars($student['subjects_needed']); ?></span>
                    </div>
                </div>
            </div>

            <form method="POST">
                <button type="submit" name="generate" class="generate-btn">
                    🔄 <?php echo $schedule ? 'Regenerate Schedule' : 'Generate My Schedule'; ?>
                </button>
            </form>
        </div>

        <?php if ($schedule): ?>
            <div class="schedule-container">
                <?php foreach ($schedule as $session): ?>
                    <div class="schedule-card">
                        <div class="day-header">
                            <div class="day-name">📅 <?php echo $session['day']; ?></div>
                            <div class="time-badge">⏰ <?php echo $session['time']; ?></div>
                        </div>
                        <div class="schedule-details">
                            <div class="detail-item">
                                <span>📚</span>
                                <span><strong>Subject:</strong> <?php echo $session['subject']; ?></span>
                            </div>
                            <div class="detail-item">
                                <span>⏱️</span>
                                <span><strong>Duration:</strong> <?php echo $session['duration']; ?> minutes</span>
                            </div>
                        </div>
                        <div class="topic">
                            <strong>📝 Topic:</strong> <?php echo $session['topic']; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-schedule">
                <h2>No Schedule Yet</h2>
                <p>Click the button above to generate your personalized AI study schedule!</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>