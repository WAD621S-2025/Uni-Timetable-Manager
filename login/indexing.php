<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NUST Timetable Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="images/Campusconnect_nobg.png" class="logo" alt="Campus Connect Logo">
        </div>
        <div class="buttons">
            <button type="submit" class="btn" id="loginBtn">Login</button>
            <button type="submit" class="btn" id="registerBtn">Register</button>
        </div>
    </header>
    
    <div class="main-content">
        <div class="hero-section">
            <h1>Campus Connect Timetable Manager</h1>
            <p>Welcome to our Timetable Manager</p>
        </div>
        
        <div class="academic-impact">
            <h2>CAMPUS CONNECT'S ACADEMIC IMPACT</h2>
        </div>
        <div class="academic-content">
            <p>CAMPUS CONNECT continues to be a leading educational tool in Namibia, with over 2,000 students registered across various educational institutions. The website has implemented a new digital timetable system to streamline academic scheduling and improve student experience. This system has reduced scheduling conflicts by 85% and improved resource allocation across various university facilities.</p>
        </div>
    </div>

    <div class="container">
        <!--Login form-->
        <div class="form-box login">
            <form action="login_registration.php" method="POST"> 
                <h1>Login</h1>
                <div class="input-box">
                    <input type="text" name="username" placeholder="Username" required> 
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required> 
                    <i class="fa-solid fa-lock"></i>
                </div>
                <button type="submit" class="btn" name="login">Login</button> 
            </form>
        </div>
        <!--Registration Form-->  
        <div class="form-box register">
            <form action="login_registration.php" method="POST"> 
                <h1>Registration</h1>
                <div class="input-box">
                    <input type="text" name="username" placeholder="Username" required> 
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" required> 
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required> 
                    <i class="fa-solid fa-lock"></i>
                </div>
                <div class="input-box">
                    <input type="password" name="confirm_password" placeholder="Confirm password" required> 
                    <i class="fa-solid fa-lock"></i>
                </div>
                <button type="submit" class="btn" name="register">Register</button> 
            </form>
        </div>
        <div class="toggle-box">
            <!--Toggle box Left-->
            <div class="toggle-panel toggle-left">
                <h1>Hello, Welcome!</h1>
                <p>Don't have an Account?</p>
                <button class="btn register-btn">Register</button>
            </div>
            <!--Toggle box right-->
            <div class="toggle-panel toggle-right">
                <h1>Welcome Back!</h1>
                <p>Already have an Account?</p>
                <button class="btn login-btn">Login</button>
            </div>
        </div>
    </div>
    
    <footer>
        <div class="footer-container">
            <div>
                <img src="images/Campusconnect_nobg.png" class="footer-logo" alt="Campus Connect Logo">
            </div>
            <div class="footer-links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="login/login.html">Login</a></li>
                    <li><a href="login/login.html">Register</a></li>
                </ul>
            </div>
            <div class="contact-info">
                <h3>Contact Information</h3>
                <ul>
                    <li>Contact: ‪+264-61-123-4567‬</li>
                    <li>Email us: ictsupport@campusconnect.na</li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2025 Campus Connect Timetable Manager. All rights reserved.</p>
        </div>
    </footer>
    
    <script src="script.js"></script>
</body>
</html>
