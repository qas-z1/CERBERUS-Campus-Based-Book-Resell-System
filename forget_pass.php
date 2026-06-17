<?php
// Include DB connection
include 'conn.php';

$studentExists = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['check'])) {
    $studentID = $_POST['studentID'];
    $checkQuery = "SELECT * FROM user WHERE studentID = '$studentID'";
    $result = mysqli_query($conn, $checkQuery);
    if (mysqli_num_rows($result) > 0) {
        $studentExists = true;
    } else {
        $error_message = 'Student ID not found in database.';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {
    $studentID = $_POST['studentID'];
    $newPass = $_POST['new_password'];
    $updateQuery = "UPDATE user SET password = '$newPass' WHERE studentID = '$studentID'";
    if (mysqli_query($conn, $updateQuery)) {
        $success_message = 'Password updated successfully.';
        // Redirect after 2 seconds
        header("Refresh: 2; url=login.php");
    } else {
        $error_message = 'Failed to update password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password - CampusBooks</title>
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
      --success: #4ade80;
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
      padding: 0.8rem;
      border-radius: var(--radius);
      background: rgba(255, 107, 107, 0.1);
    }
    
    .success-message {
      color: var(--success);
      margin-top: 0.5rem;
      font-size: 0.9rem;
      display: block;
      text-align: center;
      padding: 0.8rem;
      border-radius: var(--radius);
      background: rgba(74, 222, 128, 0.1);
    }
    
    /* Reset Password specific styles */
    .reset-container {
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      min-height: 80vh;
      padding: 2rem;
    }
    
    .reset-header {
      text-align: center;
      margin-bottom: 2rem;
    }
    
    .reset-header h1 {
      font-size: 2.5rem;
      font-weight: 700;
      background: linear-gradient(90deg, var(--light), #d1c4e9);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 1rem;
    }
    
    .reset-header p {
      color: rgba(255, 255, 255, 0.8);
      font-size: 1.1rem;
      max-width: 500px;
    }
    
    .reset-links {
      display: flex;
      justify-content: center;
      width: 100%;
      margin-top: 1.5rem;
    }
    
    .reset-links a {
      color: var(--secondary);
      text-decoration: none;
      transition: var(--transition);
      font-weight: 500;
    }
    
    .reset-links a:hover {
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
    
    /* Password strength indicator */
    .password-strength {
      height: 4px;
      width: 100%;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 2px;
      margin-top: 0.5rem;
      overflow: hidden;
    }
    
    .strength-meter {
      height: 100%;
      width: 0%;
      background: var(--error);
      transition: var(--transition);
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
      
      .reset-header h1 {
        font-size: 2rem;
      }
    }
    
    /* Progress bar animation */
    @keyframes progressBar {
      0% { width: 0%; }
      100% { width: var(--strength); }
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
  <div class="reset-container">
    <div class="reset-header animate">
      <h1>Reset Your Password</h1>
      <p>Enter your Student ID to create a new password</p>
    </div>
    
    <!-- Reset Form -->
    <div class="form-container glass animate">
      <?php if(isset($error_message)): ?>
        <div class="error-message">
          <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
        </div>
      <?php endif; ?>
      
      <?php if(isset($success_message)): ?>
        <div class="success-message">
          <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
          <p>Redirecting to login page...</p>
        </div>
      <?php endif; ?>
      
      <form method="POST" action="">
        <div class="form-group">
          <label class="form-label">Student ID</label>
          <input type="text" class="form-control" name="studentID" 
                 value="<?= isset($_POST['studentID']) ? $_POST['studentID'] : '' ?>" 
                 placeholder="Enter your student ID" required>
        </div>
        
        <?php if (!$studentExists): ?>
          <button class="btn btn-secondary" name="check" style="width: 100%; padding: 1.2rem; font-size: 1.1rem;">
            <i class="fas fa-search"></i> Verify Student ID
          </button>
        <?php else: ?>
          <div class="form-group">
            <label class="form-label">New Password</label>
            <div class="password-container">
              <input type="password" class="form-control" id="new_password" name="new_password" 
                     placeholder="Enter your new password" required
                     oninput="checkPasswordStrength(this.value)">
              <button type="button" class="toggle-password" onclick="togglePassword('new_password')">
                <i class="fas fa-eye"></i>
              </button>
            </div>
            <div class="password-strength">
              <div class="strength-meter" id="strength-meter"></div>
            </div>
            <div id="password-strength-text" style="font-size: 0.8rem; margin-top: 0.5rem; color: rgba(255,255,255,0.7);"></div>
          </div>
          
          <button class="btn btn-primary" name="save" style="width: 100%; padding: 1.2rem; font-size: 1.1rem;">
            <i class="fas fa-save"></i> Update Password
          </button>
        <?php endif; ?>
        
        <div class="reset-links">
          <a href="login.php">
            <i class="fas fa-arrow-left"></i> Back to Login
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
    function togglePassword(inputId) {
      const passwordInput = document.getElementById(inputId);
      const toggleIcon = document.querySelector(`#${inputId} + .toggle-password i`);
      
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
    
    // Check password strength
    function checkPasswordStrength(password) {
      const strengthMeter = document.getElementById('strength-meter');
      const strengthText = document.getElementById('password-strength-text');
      let strength = 0;
      let text = '';
      let color = '';
      
      // Check password length
      if (password.length > 7) strength += 25;
      
      // Check for mixed case
      if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) strength += 25;
      
      // Check for numbers
      if (password.match(/([0-9])/)) strength += 25;
      
      // Check for special characters
      if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/)) strength += 25;
      
      // Update UI based on strength
      strengthMeter.style.width = strength + '%';
      strengthMeter.style.animation = 'progressBar 0.5s ease-out';
      
      if (strength < 25) {
        text = 'Very Weak';
        color = 'var(--error)';
      } else if (strength < 50) {
        text = 'Weak';
        color = '#ff9f43';
      } else if (strength < 75) {
        text = 'Medium';
        color = '#feca57';
      } else {
        text = 'Strong';
        color = 'var(--success)';
      }
      
      strengthMeter.style.backgroundColor = color;
      strengthText.textContent = 'Password Strength: ' + text;
      strengthText.style.color = color;
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