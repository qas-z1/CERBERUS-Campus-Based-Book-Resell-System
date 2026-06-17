<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['studentID'])) {
  header("Location: login.php");
  exit();
}

$studentID = $_SESSION['studentID'];

// Fetch user data
$query = "SELECT * FROM user WHERE studentID = '$studentID'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $phone = mysqli_real_escape_string($conn, $_POST['phone']);
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);

  $updateQuery = "UPDATE user SET email = '$email', phone = '$phone', username = '$username', password = '$password' WHERE studentID = '$studentID'";
  mysqli_query($conn, $updateQuery);
  header("Location: profile.php?updated=1");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CampusBooks - Profile</title>
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

    nav {
      display: flex;
      align-items: center;
      gap: 1.5rem;
    }

    nav a {
      color: var(--light);
      text-decoration: none;
      font-weight: 500;
      padding: 0.7rem 1.2rem;
      border-radius: 50px;
      transition: var(--transition);
      position: relative;
      overflow: hidden;
    }

    nav a::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 0;
      height: 100%;
      background: rgba(255, 255, 255, 0.1);
      transition: var(--transition);
      z-index: -1;
    }

    nav a:hover::before {
      width: 100%;
    }

    nav a.active {
      background: rgba(255, 255, 255, 0.15);
    }

    .dropdown {
      position: relative;
      display: inline-block;
    }

    .dropdown-button {
      background-color: transparent;
      border: none;
      cursor: pointer;
      padding: 8px;
    }

    .dropdown-icon {
      font-size: 24px;
      color: white;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      right: 0;
      background-color: var(--glass);
      min-width: 160px;
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      z-index: 1;
    }

    .dropdown-content a {
      color: var(--light);
      padding: 12px 16px;
      text-decoration: none;
      display: block;
      transition: var(--transition);
    }

    .dropdown-content a:hover {
      background-color: rgba(255, 255, 255, 0.1);
    }

    .dropdown-content.show {
      display: block;
    }

    .container {
      max-width: 800px;
      margin: 2rem auto;
      padding: 0 1.5rem;
    }

    .title {
      font-size: 2.5rem;
      font-weight: 700;
      background: linear-gradient(90deg, var(--light), #d1c4e9);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      position: relative;
      display: inline-block;
      margin-bottom: 2rem;
    }

    .title::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 0;
      width: 70px;
      height: 4px;
      background: var(--secondary);
      border-radius: 10px;
    }

    .profile-pic {
      text-align: center;
      margin-bottom: 2rem;
    }

    .profile-pic img {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid var(--secondary);
    }

    form {
      background: var(--glass);
      border-radius: var(--radius);
      padding: 2rem;
      box-shadow: var(--shadow);
    }

    .form-group {
      display: flex;
      align-items: center;
      margin-bottom: 1.5rem;
      position: relative;
    }

    .form-group label {
      flex: 0 0 120px;
      text-align: right;
      margin-right: 1rem;
      color: rgba(255, 255, 255, 0.8);
      font-weight: 500;
    }

    .form-group input {
      flex: 1;
      padding: 0.8rem 1rem;
      border-radius: var(--radius);
      border: none;
      background: rgba(255, 255, 255, 0.1);
      color: var(--light);
      font-size: 1rem;
      transition: var(--transition);
      border: 1px solid transparent;
    }

    .form-group input:focus {
      background: rgba(255, 255, 255, 0.15);
      border-color: var(--secondary);
      outline: none;
      box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.2);
    }

    .eye-icon {
      position: absolute;
      right: 1.5rem;
      top: 50%;
      transform: translateY(-50%);
      width: 20px;
      height: 20px;
      cursor: pointer;
      fill: var(--light);
      transition: var(--transition);
    }

    .save-btn {
      display: block;
      margin: 0 auto;
      background: var(--primary-light);
      color: var(--light);
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
    }

    .save-btn::before {
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

    .save-btn:hover::before {
      width: 100%;
    }

    .save-btn:hover {
      background: var(--primary);
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(57, 51, 108, 0.5);
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
    <nav>
      <a href="homepage.php">Home Page</a>
      <?php if (isset($_SESSION['usertype']) && $_SESSION['usertype'] == 'seller') : ?>
        <a href="listings.php">My Book Listings</a>
      <?php endif; ?>
      <a href="chatroom.php">Messages</a>
      <div class="dropdown">
        <button class="dropdown-button" onclick="toggleDropdown()">
          <span class="dropdown-icon">&#9776;</span>
        </button>
        <div id="dropdownMenu" class="dropdown-content">
          <a href="homepage.php">Books</a>
          <a href="#">Profile</a>
          <a href="logout.php">Log-Out</a>
        </div>
      </div>
    </nav>
  </header>

  <!-- Main Content -->
  <div class="container">
    <div class="title">Profile</div>
    <div class="profile-pic">
      <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png"  alt="Profile">
    </div>
    <form method="POST">
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
      </div>
      <div class="form-group">
        <label>Student ID</label>
        <input type="text" name="studentID" value="<?php echo htmlspecialchars($user['studentID']); ?>" disabled>
      </div>
      <div class="form-group">
        <label>Phone Number</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
      </div>
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>">
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" id="password" value="<?php echo htmlspecialchars($user['password']); ?>">
        <svg class="eye-icon" onmouseover="togglePassword(true)" onmouseout="togglePassword(false)" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12c-2.8 0-5-2.2-5-5s2.2-5 5-5 5 2.2 5 5-2.3 5-5 5zm0-8c-1.6 0-3 1.3-3 3s1.4 3 3 3 3-1.3 3-3-1.4-3-3-3z"/></svg>
      </div>
      <button type="submit" class="save-btn">Save</button>
    </form>
  </div>

  <?php if (isset($_GET['updated']) && $_GET['updated'] == '1'): ?>
  <script>
    alert("Profile updated successfully!");
  </script>
  <?php endif; ?>

  <script>
    function toggleDropdown() {
      document.getElementById("dropdownMenu").classList.toggle("show");
    }

    function togglePassword(show) {
      const passInput = document.getElementById("password");
      passInput.type = show ? "text" : "password";
    }
  </script>
</body>
</html>