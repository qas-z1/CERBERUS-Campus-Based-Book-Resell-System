<?php
include 'conn.php';
session_start();

// Handle delete action
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $delete = "DELETE FROM user WHERE User_id = $id";
    mysqli_query($conn, $delete);
    echo "<script>alert('User deleted successfully.'); window.location='all_users.php';</script>";
}

// Fetch users
$result = mysqli_query($conn, "SELECT * FROM user");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Users - Admin</title>
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
      justify-content: flex-start;
      padding: 2rem;
    }

    .container {
      max-width: 1200px;
      width: 100%;
      padding: 2rem;
    }

    h2 {
      font-size: 2.5rem;
      font-weight: 700;
      background: linear-gradient(90deg, var(--light), #d1c4e9);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      position: relative;
      margin-bottom: 2rem;
    }

    h2::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 0;
      width: 70px;
      height: 4px;
      background: var(--secondary);
      border-radius: 10px;
    }

    .back-btn {
      display: inline-block;
      margin-bottom: 2rem;
      padding: 1rem 2rem;
      background: var(--primary-light);
      color: var(--light);
      text-decoration: none;
      border-radius: 50px;
      font-weight: 600;
      transition: var(--transition);
      position: relative;
      overflow: hidden;
    }

    .back-btn::before {
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

    .back-btn:hover::before {
      width: 100%;
    }

    .back-btn:hover {
      background: var(--primary);
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(57, 51, 108, 0.5);
    }

    .back-btn i {
      margin-right: 0.5rem;
    }

    .user-table {
      background: var(--glass);
      border-radius: var(--radius);
      overflow: hidden;
      box-shadow: var(--shadow);
      animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 1.5rem;
      text-align: center;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    th {
      background: var(--primary-light);
      font-weight: 600;
    }

    tr:nth-child(even) {
      background: var(--glass);
    }

    tr:hover {
      background: rgba(255, 255, 255, 0.1);
      transform: translateY(-2px);
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    .btn {
      padding: 0.6rem 1.2rem;
      border-radius: var(--radius);
      font-weight: bold;
      cursor: pointer;
      transition: var(--transition);
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
    }

    .delete {
      background: crimson;
      color: white;
    }

    .delete:hover {
      background: #b30000;
      transform: scale(1.05);
    }

    @media (max-width: 768px) {
      th, td {
        padding: 1rem;
        font-size: 0.9rem;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <a href="homepage_admin.php" class="back-btn">
      <i class="fas fa-arrow-left"></i> Back to Homepage
    </a>

    <h2>All Registered Users</h2>

    <div class="user-table">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Student ID</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Type</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
              <td><?= $row['User_id'] ?></td>
              <td><?= $row['username'] ?></td>
              <td><?= $row['studentID'] ?></td>
              <td><?= $row['email'] ?></td>
              <td><?= $row['phone'] ?></td>
              <td><?= ucfirst($row['usertype']) ?></td>
              <td>
                <a href="all_users.php?delete=<?= $row['User_id'] ?>" 
                   class="btn delete"
                   onclick="return confirm('Are you sure you want to delete this user?')">
                  Delete
                </a>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>