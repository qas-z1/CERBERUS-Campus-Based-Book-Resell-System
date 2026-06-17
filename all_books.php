<?php
include 'conn.php';
session_start();

// Handle delete action
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $delete = "DELETE FROM books WHERE Book_id = $id";
    mysqli_query($conn, $delete);
    echo "<script>alert('Book deleted successfully.'); window.location='all_books.php';</script>";
}

// Fetch books
$result = mysqli_query($conn, "SELECT * FROM books");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Books - CampusBooks</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
  <style>
    :root {
      --primary: #39336C;
      --primary-light: #4d4685;
      --secondary: #FFC107;
      --light: #FFFFFF;
      --dark: #2d1a53;
      --glass: rgba(255, 255, 255, 0.15);
      --radius: 16px;
      --shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
      --transition: all 0.3s ease;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background: linear-gradient(135deg, #1a103f, #2a1a5e);
      color: var(--light);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      padding: 2rem;
    }

    .container {
      max-width: 1200px;
      margin: auto;
      width: 100%;
    }

    .back-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      background: var(--glass-dark);
      color: var(--light);
      padding: 1rem 1.5rem;
      border-radius: var(--radius);
      text-decoration: none;
      font-weight: bold;
      margin-bottom: 2rem;
      transition: var(--transition);
    }

    .back-btn:hover {
      background: var(--secondary);
      color: var(--dark);
    }

    h2 {
      font-size: 2rem;
      margin-bottom: 1.5rem;
      text-align: center;
      color: var(--light);
    }

    .table-container {
      overflow-x: auto;
      border-radius: var(--radius);
      background: var(--glass);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      box-shadow: var(--shadow);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      min-width: 800px;
    }

    th, td {
      padding: 1rem;
      text-align: center;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    th {
      background-color: var(--primary);
      color: var(--light);
      font-weight: 600;
      position: sticky;
      top: 0;
      z-index: 1;
    }

    tr:hover {
      background-color: rgba(255, 255, 255, 0.05);
    }

    img {
      width: 80px;
      height: auto;
      object-fit: cover;
      border-radius: var(--radius);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    .btn {
      display: inline-block;
      padding: 0.5rem 1rem;
      border-radius: var(--radius);
      font-weight: bold;
      text-decoration: none;
      transition: var(--transition);
      font-size: 0.95rem;
    }

    .btn.delete {
      background: crimson;
      color: var(--light);
    }

    .btn.delete:hover {
      background: darkred;
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(255, 0, 0, 0.4);
    }

    @media (max-width: 768px) {
      h2 {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>

<div class="container">
  <a href="homepage_admin.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Homepage</a>

  <h2>All Books</h2>

  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Image</th>
          <th>Title</th>
          <th>Course Code</th>
          <th>Subject</th>
          <th>Price (RM)</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
          <tr>
            <td><?= htmlspecialchars($row['Book_id']) ?></td>
            <td>
              <?php if (!empty($row['image_url'])) { ?>
                <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="Book Image">
              <?php } else { ?>
                <span>No Image</span>
              <?php } ?>
            </td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['course_code']) ?></td>
            <td><?= htmlspecialchars($row['subject']) ?></td>
            <td><?= number_format($row['price'], 2) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td>
              <a href="all_books.php?delete=<?= $row['Book_id'] ?>" class="btn delete" onclick="return confirm('Delete book?')">Delete</a>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>