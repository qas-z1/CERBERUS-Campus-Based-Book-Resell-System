<?php 
session_start();
include 'conn.php';

if(isset($_POST['login'])){
  $stuID=$_POST['studentID'];
  $password=$_POST['password'];
  $query="SELECT * FROM user WHERE studentID='$stuID' AND password='$password'";
  $query2="SELECT * FROM admin WHERE admin_studentID='$stuID' AND admin_password='$password' ";

  $result=mysqli_query($conn,$query);
  $result2=mysqli_query($conn,$query2);

  if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $_SESSION['studentID']=$row['studentID'];
    $_SESSION['usertype'] = $row['usertype'];
    if($_SESSION['usertype']=="buyer"){
      header('Location: homepage.php');
      exit();
    }
    else if(($_SESSION['usertype']=="seller")){
      $_SESSION['studentID']=$row['studentID'];
      $_SESSION['username'] = $row['username'];
       header('Location: homepage.php');
      exit();
    }  
  } 
  else if (mysqli_num_rows($result2) > 0) {
    $row = mysqli_fetch_assoc($result2);
    $_SESSION['admin_studentID'] = $row['admin_studentID'];
    

    header('Location: homepage_admin.php');
    exit();
  } 
  else {
    echo "<script>alert('Login Failed: Wrong Username Or Password!');</script>";
  }



}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CampusBooks - Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #39336C;
      --primary-light: #4d4685;
      --primary-dark: #2c2753;
      --secondary: #FFC107;
      --light: #FFFFFF;
      --dark: #2d1a53;
      --gray: #f5f5f7;
      --gray-dark: #e0e0e0;
      --glass: rgba(255, 255, 255, 0.15);
      --glass-dark: rgba(57, 51, 108, 0.7);
      --radius: 16px;
      --shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
      --transition: all 0.3s ease;
      --error: #ff6b6b;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    body {
      background: linear-gradient(135deg, #1a103f, #2a1a5e);
      color: var(--light);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      overflow-x: hidden;
      background-attachment: fixed;
      background-size: cover;
    }
    
    /* Glassmorphism effect */
    .glass {
      background: var(--glass);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: var(--shadow);
    }
    
    .glass-dark {
      background: var(--glass-dark);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: var(--shadow);
    }
    
    /* Header Styles */
    header {
      background: var(--glass);
      color: var(--light);
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 1000;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .logo {
      font-size: 1.8rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      background: linear-gradient(90deg, var(--secondary), #ffd54f);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    
    .logo i {
      font-size: 1.5rem;
    }
    
    /* Buttons */
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.8rem;
      padding: 1rem 2rem;
      border-radius: 50px;
      font-weight: 600;
      cursor: pointer;
      transition: var(--transition);
      border: none;
      text-decoration: none;
      font-size: 1rem;
      position: relative;
      overflow: hidden;
      z-index: 1;
    }
    
    .btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 0;
      height: 100%;
      background: rgba(255, 255, 255, 0.2);
      transition: var(--transition);
      z-index: -1;
    }
    
    .btn:hover::before {
      width: 100%;
    }
    
    .btn-primary {
      background: var(--primary-light);
      color: var(--light);
      box-shadow: 0 6px 15px rgba(57, 51, 108, 0.4);
    }
    
    .btn-primary:hover {
      background: var(--primary);
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(57, 51, 108, 0.5);
    }
    
    .btn-secondary {
      background: var(--secondary);
      color: var(--dark);
      box-shadow: 0 6px 15px rgba(255, 193, 7, 0.3);
    }
    
    .btn-secondary:hover {
      background: #ffb300;
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(255, 193, 7, 0.4);
    }
    
    /* Form Elements */
    .form-container {
      max-width: 500px;
      margin: 2rem auto;
      padding: 2.5rem;
      border-radius: var(--radius);
      background: var(--glass);
      width: 90%;
    }
    
    .form-group {
      margin-bottom: 1.8rem;
      position: relative;
    }
    
    .form-label {
      display: block;
      margin-bottom: 0.8rem;
      font-weight: 600;
      color: var(--light);
      font-size: 1.1rem;
    }
    
    .form-control {
      width: 100%;
      padding: 1.2rem 1.5rem;
      border: none;
      border-radius: var(--radius);
      background: rgba(255, 255, 255, 0.1);
      color: var(--light);
      font-size: 1rem;
      transition: var(--transition);
      border: 1px solid transparent;
    }
    
    .form-control:focus {
      background: rgba(255, 255, 255, 0.15);
      border-color: var(--secondary);
      outline: none;
      box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.2);
    }
    
    .form-control::placeholder {
      color: rgba(255, 255, 255, 0.6);
    }
    
    .password-container {
      position: relative;
    }
    
    .toggle-password {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      background: transparent;
      border: none;
      color: rgba(255, 255, 255, 0.6);
      cursor: pointer;
      font-size: 1.2rem;
    }
    
    .error-message {
      color: var(--error);
      margin-top: 0.5rem;
      font-size: 0.9rem;
      display: block;
      text-align: center;
    }
    
    /* Login specific styles */
    .login-container {
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      min-height: 80vh;
      padding: 2rem;
    }
    
    .login-header {
      text-align: center;
      margin-bottom: 2rem;
    }
    
    .login-header h1 {
      font-size: 2.5rem;
      font-weight: 700;
      background: linear-gradient(90deg, var(--light), #d1c4e9);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 1rem;
    }
    
    .login-header p {
      color: rgba(255, 255, 255, 0.8);
      font-size: 1.1rem;
      max-width: 500px;
    }
    
    .login-links {
      display: flex;
      justify-content: space-between;
      width: 100%;
      margin-top: 1.5rem;
    }
    
    .login-links a {
      color: var(--secondary);
      text-decoration: none;
      transition: var(--transition);
      font-weight: 500;
    }
    
    .login-links a:hover {
      text-decoration: underline;
    }
    
    /* Animation Classes */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .animate {
      animation: fadeIn 0.8s ease-out forwards;
    }
    
    /* Footer */
    footer {
      background: var(--glass-dark);
      padding: 2rem;
      text-align: center;
      margin-top: auto;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .footer-content {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
    }
    
    .social-icons {
      display: flex;
      justify-content: center;
      gap: 1.5rem;
    }
    
    .social-icons a {
      color: var(--light);
      font-size: 1.5rem;
      transition: var(--transition);
    }
    
    .social-icons a:hover {
      color: var(--secondary);
      transform: translateY(-5px);
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
      .form-container {
        padding: 1.5rem;
      }
      
      .login-header h1 {
        font-size: 2rem;
      }
      
      .login-links {
        flex-direction: column;
        gap: 1rem;
        align-items: center;
      }
    }
  </style>
</head>
<body>
  <!-- Header -->
  <header class="glass">
    <div class="logo">
      <i class="fas fa-book"></i>
      CampusBooks
    </div>
  </header>
  
  <!-- Main Content -->
  <div class="login-container">
    <div class="login-header animate">
      <h1>Welcome Back to CampusBooks</h1>
      <p>Sign in to access textbooks, manage your listings, and connect with other students</p>
    </div>
    
    <!-- Login Form -->
    <div class="form-container glass animate">
      <?php if(isset($login_error)): ?>
        <div class="error-message" style="margin-bottom: 1.5rem; text-align: center;">
          <i class="fas fa-exclamation-circle"></i> <?php echo $login_error; ?>
        </div>
      <?php endif; ?>
      
      <form method="POST" action="">
        <div class="form-group">
          <label class="form-label">Student ID</label>
          <input type="text" class="form-control" name="studentID" placeholder="Enter your student ID" required>
        </div>
        
        <div class="form-group">
          <label class="form-label">Password</label>
          <div class="password-container">
            <input type="password" class="form-control" id="pass" name="password" placeholder="Enter your password" required>
            <button type="button" class="toggle-password" onclick="togglePassword()">
              <i class="fas fa-eye"></i>
            </button>
          </div>
        </div>
        
        <button class="btn btn-primary" name="login" style="width: 100%; padding: 1.2rem; font-size: 1.1rem;">
          <i class="fas fa-sign-in-alt"></i> Login
        </button>
        
        <div class="login-links">
          <a href="register.php">
            <i class="fas fa-user-plus"></i> Create an account
          </a>
          <a href="forget_pass.php">
            <i class="fas fa-key"></i> Forgot password?
          </a>
        </div>
      </form>
    </div>
  </div>
  
  <!-- Footer -->
  <footer>
    <div class="footer-content">
      <div class="logo">
        <i class="fas fa-book"></i>
        CampusBooks
      </div>
      <p>Buy and sell textbooks with fellow students. Save money, save trees.</p>
      
      <div class="social-icons">
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-linkedin-in"></i></a>
      </div>
      
      <p>© 2023 CampusBooks. All rights reserved.</p>
    </div>
  </footer>
  
  <script>
    // Toggle password visibility
    function togglePassword() {
      const passwordInput = document.getElementById('pass');
      const toggleIcon = document.querySelector('.toggle-password i');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
      } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
      }
    }
    
    // Add animation to form elements
    document.addEventListener('DOMContentLoaded', function() {
      const formGroups = document.querySelectorAll('.form-group');
      formGroups.forEach((group, index) => {
        group.style.animationDelay = `${index * 0.1}s`;
      });
      
      // Add hover effect to buttons
      const buttons = document.querySelectorAll('.btn');
      buttons.forEach(button => {
        button.addEventListener('mouseenter', () => {
          button.style.transform = 'translateY(-5px)';
        });
        
        button.addEventListener('mouseleave', () => {
          button.style.transform = 'translateY(0)';
        });
      });
      
      // Form input focus effects
      const inputs = document.querySelectorAll('.form-control');
      inputs.forEach(input => {
        input.addEventListener('focus', () => {
          input.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', () => {
          input.parentElement.classList.remove('focused');
        });
      });
    });
  </script>
</body>
</html>