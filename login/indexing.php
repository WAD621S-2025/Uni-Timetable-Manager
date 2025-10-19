<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login / Register</title>
    <link rel="stylesheet" href="auth.css">
</head>
<body>
    <div class="container">
        <!-- Login Form -->
        <div class="form-box login">
            <form action="login_registration.php" method="POST">
                <h1>Login</h1>
                <div class="input-box">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" name="login">Login</button>
            </form>
        </div>

        <!-- Registration Form -->
        <div class="form-box register">
            <form action="login_registration.php" method="POST">
                <h1>Registration</h1>
                <div class="input-box">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="input-box">
                    <input type="password" name="confirm_password" placeholder="Confirm password" required>
                </div>
                <button type="submit" name="register">Register</button>
            </form>
        </div>

        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <h1>Hello, Welcome!</h1>
                <p>Don't have an Account?</p>
                <button class="btn register-btn">Register</button>
            </div>
            <div class="toggle-panel toggle-right">
                <h1>Welcome Back!</h1>
                <p>Already have an Account?</p>
                <button class="btn login-btn">Login</button>
            </div>
        </div>
    </div>

    <script src="scripting.js" defer></script>
</body>
</html>
