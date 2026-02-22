<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'tutor_finder');

// Create Connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper Functions
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

// Claude API Configuration (for AI Schedule Generation)
define('CLAUDE_API_KEY', 'sk-ant-your-actual-key'); // Students can add their key
define('CLAUDE_API_URL', 'https://api.anthropic.com/v1/messages');

function generateAISchedule($studentData) {
    $prompt = "Create a weekly study schedule for a student with the following details:
    Grade: {$studentData['grade']}
    Subjects Needed: {$studentData['subjects']}
    
    Generate a detailed weekly schedule in JSON format with days, times, subjects, and study recommendations.
    Response format: {\"schedule\": [{\"day\": \"Monday\", \"time\": \"9:00 AM\", \"subject\": \"Math\", \"duration\": 60, \"topic\": \"Algebra\"}]}";
    
    $data = json_encode([
        'model' => 'claude-sonnet-4-20250514',
        'max_tokens' => 1024,
        'messages' => [
            ['role' => 'user', 'content' => $prompt]
        ]
    ]);
    
    $ch = curl_init(CLAUDE_API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'x-api-key: ' . CLAUDE_API_KEY,
        'anthropic-version: 2023-06-01'
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    if ($response) {
        $result = json_decode($response, true);
        if (isset($result['content'][0]['text'])) {
            return $result['content'][0]['text'];
        }
    }
    
    return null;
}
?>