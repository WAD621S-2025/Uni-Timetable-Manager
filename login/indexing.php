<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Campus Connect</title>
  <link rel="stylesheet" href="styling.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
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
</body>
<script src="script.js"></script>
</html>

