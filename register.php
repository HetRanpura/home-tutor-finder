<?php
require_once 'config.php';

$type = isset($_GET['type']) ? $_GET['type'] : 'student';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = sanitize($_POST['phone']);
    
    if ($type == 'student') {
        $grade = sanitize($_POST['grade']);
        $subjects = sanitize($_POST['subjects']);
        
        $stmt = $conn->prepare("INSERT INTO students (name, email, password, phone, grade, subjects_needed) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $email, $password, $phone, $grade, $subjects);
    } else {
        $subjects = sanitize($_POST['subjects']);
        $experience = sanitize($_POST['experience']);
        $qualification = sanitize($_POST['qualification']);
        $rate = sanitize($_POST['rate']);
        $bio = sanitize($_POST['bio']);
        
        $stmt = $conn->prepare("INSERT INTO tutors (name, email, password, phone, subjects, experience, qualification, hourly_rate, bio) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssisds", $name, $email, $password, $phone, $subjects, $experience, $qualification, $rate, $bio);
    }
    
    if ($stmt->execute()) {
        $success = "Registration successful! Redirecting to login...";
        header("refresh:2;url=login.php");
    } else {
        $error = "Email already exists or registration failed!";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TutorConnect</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .register-container {
            background: white;
            padding: 3rem;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
            animation: slideIn 0.5s ease;
            transform-style: preserve-3d;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-50px) rotateX(-15deg);
            }
            to {
                opacity: 1;
                transform: translateY(0) rotateX(0);
            }
        }

        .register-container h2 {
            text-align: center;
            color: #667eea;
            margin-bottom: 0.5rem;
            font-size: 2.5rem;
        }

        .register-type {
            text-align: center;
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .type-btn {
            padding: 0.5rem 2rem;
            border: 2px solid #667eea;
            background: white;
            color: #667eea;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            font-weight: 600;
        }

        .type-btn.active {
            background: #667eea;
            color: white;
            transform: scale(1.05);
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
            background: #f8f9fa;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
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

        .submit-btn::before {
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

        .submit-btn:hover::before {
            width: 400px;
            height: 400px;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
        }

        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .login-link a:hover {
            color: #764ba2;
        }

        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            animation: fadeIn 0.5s;
        }

        .alert-error {
            background: #fee;
            color: #c33;
            border-left: 4px solid #c33;
        }

        .alert-success {
            background: #efe;
            color: #3c3;
            border-left: 4px solid #3c3;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .home-link {
            position: absolute;
            top: 2rem;
            left: 2rem;
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: transform 0.3s;
        }

        .home-link:hover {
            transform: translateX(-5px);
        }
    </style>
</head>
<body>
    <a href="index.php" class="home-link">← Back to Home</a>
    
    <div class="register-container">
        <h2>Create Account</h2>
        
        <div class="register-type">
            <a href="register.php?type=student" class="type-btn <?php echo $type == 'student' ? 'active' : ''; ?>">
                Student
            </a>
            <a href="register.php?type=tutor" class="type-btn <?php echo $type == 'tutor' ? 'active' : ''; ?>">
                Tutor
            </a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required minlength="6">
            </div>

            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" required>
            </div>

            <?php if ($type == 'student'): ?>
                <div class="form-group">
                    <label>Grade/Class</label>
                    <select name="grade" required>
                        <option value="">Select Grade</option>
                        <option value="6th">6th Grade</option>
                        <option value="7th">7th Grade</option>
                        <option value="8th">8th Grade</option>
                        <option value="9th">9th Grade</option>
                        <option value="10th">10th Grade</option>
                        <option value="11th">11th Grade</option>
                        <option value="12th">12th Grade</option>
                        <option value="College">College</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Subjects Needed</label>
                    <input type="text" name="subjects" placeholder="e.g., Math, Physics, Chemistry" required>
                </div>
            <?php else: ?>
                <div class="form-group">
                    <label>Subjects You Teach</label>
                    <input type="text" name="subjects" placeholder="e.g., Math, Physics, Chemistry" required>
                </div>

                <div class="form-group">
                    <label>Years of Experience</label>
                    <input type="number" name="experience" min="0" required>
                </div>

                <div class="form-group">
                    <label>Qualification</label>
                    <input type="text" name="qualification" placeholder="e.g., MSc in Mathematics" required>
                </div>

                <div class="form-group">
                    <label>Hourly Rate (₹)</label>
                    <input type="number" name="rate" min="0" step="0.01" required>
                </div>

                <div class="form-group">
                    <label>Bio</label>
                    <textarea name="bio" placeholder="Tell us about yourself..." required></textarea>
                </div>
            <?php endif; ?>

            <button type="submit" class="submit-btn">Register</button>

            <div class="login-link">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </form>
    </div>
</body>
</html>