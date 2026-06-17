<?php
session_start();
include 'conn.php';
// Optional: admin login protection
// if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - CampusBooks</title>
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
    }

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
      background: none;
      border: none;
      cursor: pointer;
      padding: 8px;
    }

    .dropdown-icon {
      font-size: 24px;
      color: var(--light);
    }

    .dropdown-content {
      display: none;
      position: absolute;
      right: 0;
      background: var(--glass);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      min-width: 160px;
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      z-index: 10;
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .dropdown-content a {
      color: var(--light);
      padding: 12px 16px;
      text-decoration: none;
      display: block;
      font-weight: 500;
    }

    .dropdown-content a:hover {
      background: rgba(255, 255, 255, 0.1);
    }

    .dropdown:hover .dropdown-content {
      display: block;
    }

    .container {
      max-width: 1200px;
      margin: 3rem auto;
      padding: 0 1.5rem;
      flex: 1;
    }

    .page-title {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 2rem;
      padding-bottom: 1rem;
    }

    .page-title h1 {
      font-size: 2.5rem;
      font-weight: 700;
      background: linear-gradient(90deg, var(--light), #d1c4e9);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      position: relative;
      display: inline-block;
    }

    .page-title h1::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 0;
      width: 70px;
      height: 4px;
      background: var(--secondary);
      border-radius: 10px;
    }

    .card {
      background: var(--glass);
      border-radius: var(--radius);
      padding: 2rem;
      max-width: 700px;
      margin: 0 auto;
      box-shadow: var(--shadow);
    }

    .card p {
      margin-bottom: 1.5rem;
      line-height: 1.6;
      color: rgba(255, 255, 255, 0.9);
    }

    ul {
      list-style: none;
      padding-left: 1rem;
      margin-bottom: 1.5rem;
    }

    ul li {
      margin-bottom: 0.75rem;
      position: relative;
      padding-left: 1.5rem;
    }

    ul li::before {
      content: "•";
      position: absolute;
      left: 0;
      color: var(--secondary);
      font-size: 1.2rem;
    }

    footer {
      background: var(--glass-dark);
      padding: 2rem;
      text-align: center;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    @media (max-width: 768px) {
      nav {
        flex-wrap: wrap;
        justify-content: center;
      }

      .page-title {
        flex-direction: column;
        align-items: flex-start;
      }

      .card {
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body>

<header class="glass">
  <div class="logo">
    <i class="fas fa-book"></i>
    CampusBooks
  </div>
  <nav>
    <a href="homepage_admin.php"><i class="fas fa-home"></i> Home</a>

    
      <a href="all_books.php"><i class="fas fa-book-open"></i> All Books</a>
      <a href="all_users.php"><i class="fas fa-users"></i> All Users</a>
      <a href="admin.php"><i class="fas fa-tachometer-alt"></i> Admin Panel</a>
    

    <div class="dropdown">
      <button class="dropdown-button">
        <span class="dropdown-icon">&#9776;</span>
      </button>
      <div class="dropdown-content">
       
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a>
      </div>
    </div>
  </nav>
</header>

<div class="container">
  <div class="page-title">
    <h1>Administrator Dashboard</h1>
  </div>

  <div class="card">
    <p>This is the admin homepage. From here, you can manage:</p>
    <ul>
      <li><strong>📋 All registered users</strong></li>
      <li><strong>📚 Books posted for resell</strong></li>
      <li><strong>💬 Transactions and price negotiations</strong></li>
    </ul>
    <p>Use the navigation bar above to get started.</p>
  </div>
</div>

<footer>
  <p>&copy; 2025 CampusBooks. All rights reserved.</p>
</footer>

</body>
</html>