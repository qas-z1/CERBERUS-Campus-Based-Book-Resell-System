<?php 
include 'conn.php';

if (isset($_POST['register'])) {
  $fullname = $_POST['fullname'];
  $studentid = $_POST['studentid'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $confirmpassword = $_POST['confirmpassword'];
  $phone = $_POST['phone'];
  $role = $_POST['role']; // buyer or seller
  
  if ($password !== $confirmpassword) {
    echo "<script>alert('Passwords do not match!');</script>";
  } else {
    $query = "INSERT INTO user (username,studentID,email,password,phone,usertype) 
              VALUES ('$fullname', '$studentid','$email', '$password', '$phone','$role')";
    
    if (mysqli_query($conn, $query)) {
      echo "<script>alert('Registration successful!'); window.location='login.php';</script>";
    } else {
      echo "<script>alert('Registration failed: " . mysqli_error($conn) . "');</script>";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CampusBooks Register</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #39336C;
      --primary-light: #4d4685;
      --primary-dark: #2c2753;
      --secondary: #FFC107;
      --light: #FFFFFF;
      --dark: #2d1a53;
      --glass: rgba(255, 255, 255, 0.15);
      --radius: 16px;
      --transition: all 0.3s ease;
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
      align-items: center;
      justify-content: center;
    }

    .form-container {
      max-width: 800px;
      padding: 2.5rem;
      border-radius: var(--radius);
      background: var(--glass);
      width: 100%;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .form-input {
      width: 100%;
      padding: 1.2rem;
      margin: 10px 0;
      border: none;
      border-radius: var(--radius);
      background: rgba(255, 255, 255, 0.1);
      color: var(--light);
      font-size: 1rem;
      transition: var(--transition);
    }

    .form-input:focus {
      background: rgba(255, 255, 255, 0.15);
      outline: none;
      box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.2);
    }

    .register-btn {
      width: 100%;
      padding: 1.2rem;
      background: var(--secondary);
      color: var(--dark);
      border: none;
      border-radius: var(--radius);
      font-weight: bold;
      font-size: 1.1rem;
      cursor: pointer;
      transition: var(--transition);
    }

    .register-btn:hover {
      background: #ffb300;
      transform: translateY(-3px);
    }

    .links {
      margin-top: 10px;
      text-align: center;
    }

    .links a {
      color: var(--light);
      text-decoration: underline;
      font-size: 14px;
    }

    .links a:hover {
      color: var(--secondary);
    }

    .radio-group {
      margin-top: 15px;
      text-align: center;
    }

    .radio-group label {
      margin-right: 10px;
      font-size: 14px;
      color: var(--light);
    }
  </style>
</head>
<body>

  <div class="form-container glass">
    <h1 style="text-align: center; margin-bottom: 20px;">Register</h1>
    <form method="POST" action="">
      <input type="text" class="form-input" name="fullname" placeholder="Full Name" required>
      <input type="text" class="form-input" name="studentid" placeholder="Student ID" required>
      <input type="email" class="form-input" name="email" placeholder="Student Email" required>
      <input type="password" class="form-input" name="password" id="password" placeholder="Password" required>
      <input type="password" class="form-input" name="confirmpassword" id="confirmpassword" placeholder="Confirm Password" required>
      <input type="text" class="form-input" name="phone" placeholder="Phone Number" required>

      <div class="radio-group">
        Register as:
        <label><input type="radio" name="role" value="buyer" required> Buyer</label>
        <label><input type="radio" name="role" value="seller" required> Seller</label>
      </div>

      <button type="submit" class="register-btn" name="register">Register</button>
    </form>

    <div class="links">
      <a href="login.php">Already have an account? Login here</a>
    </div>
  </div>

  <script>
    function togglePassword() {
      var pass = document.getElementById("password");
      var confirm = document.getElementById("confirmpassword");
      if (pass.type === "password") {
        pass.type = "text";
        confirm.type = "text";
      } else {
        pass.type = "password";
        confirm.type = "password";
      }
    }
  </script>
</body>
</html>