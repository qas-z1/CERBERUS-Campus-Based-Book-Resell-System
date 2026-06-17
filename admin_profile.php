<?php
include 'conn.php';
session_start();

// For demo purposes
$admin_name = $_SESSION['admin_name'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Profile</title>
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
      padding: 2rem;
    }

    .profile-card {
      background: var(--glass);
      border-radius: var(--radius);
      padding: 2.5rem;
      max-width: 500px;
      width: 100%;
      box-shadow: var(--shadow);
      text-align: center;
      animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h2 {
      font-size: 2.2rem;
      font-weight: 700;
      background: linear-gradient(90deg, var(--light), #d1c4e9);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 1.5rem;
      position: relative;
    }

    h2::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 70px;
      height: 4px;
      background: var(--secondary);
      border-radius: 10px;
    }

    .profile-info {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .profile-item {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 1rem;
      padding: 1rem;
      border-radius: var(--radius);
      background: rgba(255, 255, 255, 0.05);
      transition: var(--transition);
    }

    .profile-item:hover {
      background: rgba(255, 255, 255, 0.1);
      transform: scale(1.02);
    }

    .profile-icon {
      font-size: 1.5rem;
      color: var(--secondary);
      flex-shrink: 0;
    }

    .profile-detail {
      font-size: 1.1rem;
      color: var(--light);
    }

    .back-button {
      display: inline-block;
      padding: 1rem 2.5rem;
      background: var(--primary-light);
      color: var(--light);
      text-decoration: none;
      border-radius: 50px;
      font-weight: 600;
      transition: var(--transition);
      position: relative;
      overflow: hidden;
    }

    .back-button::before {
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

    .back-button:hover::before {
      width: 100%;
    }

    .back-button:hover {
      background: var(--primary);
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(57, 51, 108, 0.5);
    }

    .back-button i {
      margin-right: 0.5rem;
    }

    @media (max-width: 576px) {
      .profile-card {
        padding: 1.5rem;
      }
      
      .back-button {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <div class="profile-card">
    <h2>Admin Profile</h2>
    
    <div class="profile-info">
      <div class="profile-item">
        <i class="fas fa-user profile-icon"></i>
        <div class="profile-detail"><strong>Name:</strong> <?= $admin_name ?></div>
      </div>
      <div class="profile-item">
        <i class="fas fa-user-shield profile-icon"></i>
        <div class="profile-detail"><strong>Role:</strong> Administrator</div>
      </div>
      <!-- Add more details if needed -->
    </div>

    <a href="homepage_admin.php" class="back-button">
      <i class="fas fa-arrow-left"></i> Back to Homepage
    </a>
  </div>
</body>
</html>