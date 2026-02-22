
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }
        
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0c0c1d 0%, #1a1a2e 50%, #16213e 100%);
            color: #fff;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            perspective: 1000px;
        }
        
         .container {
            position: relative;
            z-index: 10;
            display: flex;
            width: 90%;
            max-width: 1200px;
            height: 70px;
            min-height: 675px;
            background: rgba(10, 15, 30, 0.7);
            border-radius: 30px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            animation: containerAppear 1.5s ease-out;
        }
        
        
        
        .login-section {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .visual-section {
            flex: 1;
            position: relative;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            margin-bottom: 40px;
            animation: logoFloat 6s ease-in-out infinite;
        }
        
        @keyframes logoFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .logo-icon {
            font-size: 40px;
            margin-right: 15px;
            color: #00d9ff;
            text-shadow: 0 0 15px rgba(0, 217, 255, 0.7);
        }
        
        .logo-text {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(to right, #00d9ff, #0099ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 1px;
        }
        
        .login-title {
            font-size: 32px;
            margin-bottom: 10px;
            background: linear-gradient(to right, #fff, #a8e6cf);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .login-subtitle {
            color: #a0a0c0;
            margin-bottom: 40px;
            font-size: 16px;
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #b0b0d0;
            font-size: 14px;
            transition: color 0.3s;
        }
        
        .input-container {
            position: relative;
        }
        
        .form-input {
            width: 100%;
            padding: 16px 20px 16px 50px;
            background: rgba(20, 25, 45, 0.8);
            border: 2px solid rgba(100, 100, 150, 0.3);
            border-radius: 12px;
            color: #fff;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #00d9ff;
            box-shadow: 0 0 20px rgba(0, 217, 255, 0.3);
        }
        
        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #00d9ff;
            font-size: 18px;
        }
        
        .password-toggle {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0a0c0;
            cursor: pointer;
            transition: color 0.3s;
        }
        
        .password-toggle:hover {
            color: #00d9ff;
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
        }
        
        .checkbox {
            margin-right: 8px;
            accent-color: #00d9ff;
        }
        
        .forgot-password {
            color: #00d9ff;
            text-decoration: none;
            transition: text-shadow 0.3s;
        }
        
        .forgot-password:hover {
            text-shadow: 0 0 10px rgba(0, 217, 255, 0.7);
        }
        
        .login-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(to right, #00d9ff, #0099ff);
            border: none;
            border-radius: 12px;
            color: #0c0c1d;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            letter-spacing: 1px;
            margin-bottom: 25px;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 217, 255, 0.4);
        }
        
        .login-btn:active {
            transform: translateY(-1px);
        }
        
        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.7s;
            z-index: -1;
        }
        
        .login-btn:hover::before {
            left: 100%;
        }
        
        .register-link {
            text-align: center;
            color: #a0a0c0;
            font-size: 15px;
        }
        
        .register-link a {
            color: #00d9ff;
            text-decoration: none;
            font-weight: 600;
            margin-left: 5px;
        }
        
        .social-login {
            display: flex;
            justify-content: center;
            margin-top: 30px;
            gap: 20px;
        }
        
        .social-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: rgba(20, 25, 45, 0.8);
            color: #fff;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .social-icon:hover {
            transform: translateY(-5px);
            background: rgba(30, 55, 46, 0.9);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }
        
        
    </style>
</head>
<body>
    
    <!-- Main container -->
    <div class="container">
        <!-- Login form section -->
        <div class="login-section">
            <div class="logo">
                <i class="fas fa-cube logo-icon"></i>
                <div class="logo-text">LOGIN</div>
            </div>
            
            <h1 class="login-title">Welcome Back</h1>
            <p class="login-subtitle">Sign in to your account to continue your journey</p>
            
            <form id="loginForm">
                <div class="form-group">
                    <label class="form-label" for="username">Username or Email</label>
                    <div class="input-container">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" id="username" class="form-input" placeholder="Enter your Username or Email" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-container">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="password" class="form-input" placeholder="Enter your Password" required>
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                    </div>
                </div>
                
                <div class="remember-forgot">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" class="checkbox">
                        <label for="remember">Remember me</label>
                    </div>
                    <a href="#" class="forgot-password">Forgot Password?</a>
                </div>
                
                <button type="submit" class="login-btn">
                    <a  href="home.html" class="fas fa-sign-in-alt"></a> SIGN IN
                </button>
                
                <div class="register-link">
                    Don't have an account? <a href="sign up.html" id="registerLink">Create Account</a>
                </div>
                
                <div class="social-login">
                    <div class="social-icon" id="googleLogin">
                        <a href="https://www.google.com/"class="fab fa-google"></a>
                        
                    </div>
                    <div>
                        <div class="social-icon" id="facebookLogin">
                            <a href="https://www.facebook.com/" class="fab fa-facebook-f"></a>
                        </div>
                    </div>
                    <div class="social-icon" id="instagramLogin">
                        <a href="https://www.instagram.com/" class="fab fa-instagram"></a>
                </div>
            </form>
        </div>
        
       
        </div>
    </div>
    
   </body>
</html>
