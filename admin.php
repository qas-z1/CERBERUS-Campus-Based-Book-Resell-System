<?php 
include 'conn.php';
session_start();

// Start with a base query
$query = "SELECT * FROM books WHERE 1=1";

// Add filters if they exist
if (isset($_POST['sub'])) {
    if (!empty($_POST['search'])) {
        $search = mysqli_real_escape_string($conn, $_POST['search']);
        $query .= " AND (title LIKE '%$search%' OR course_code LIKE '%$search%' OR subject LIKE '%$search%')";
    }

    if (!empty($_POST['course_code'])) {
        $course = mysqli_real_escape_string($conn, $_POST['course_code']);
        $query .= " AND course_code = '$course'";
    }

    if (!empty($_POST['subject'])) {
        $subject = mysqli_real_escape_string($conn, $_POST['subject']);
        $query .= " AND subject = '$subject'";
    }

    if (!empty($_POST['semester'])) {
        $semester = mysqli_real_escape_string($conn, $_POST['semester']);
        $query .= " AND semester = '$semester'";
    }

} else {
    // If not submitted, show all books
    $query = "SELECT * FROM books";
}

// Execute main result
$result = mysqli_query($conn, $query);

// For dropdown filters
$query2 = "SELECT DISTINCT course_code FROM books";
$query3 = "SELECT DISTINCT subject FROM books";
$query4 = "SELECT DISTINCT semester FROM books";

$result2 = mysqli_query($conn, $query2);
$result3 = mysqli_query($conn, $query3);
$result4 = mysqli_query($conn, $query4);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CampusBooks - Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
  <style>
    /* ALL ORIGINAL CSS REMAINS THE SAME EXCEPT FOR THE FILTER SELECTORS */
    
    /* Updated filter dropdown colors only */
    .filters select {
      padding: 1rem;
      border-radius: var(--radius);
      border: none;
      background: var(--glass);
      color: var(--light);
      cursor: pointer;
    }
    
    .filters select option {
      background: var(--primary);
      color: var(--light);
    }
    
    .filters select option:checked {
      background: var(--primary-light);
      color: var(--light);
    }
    
    .filters select option:hover {
      background: var(--primary-light);
    }
    
    /* END OF COLOR CHANGES - ALL OTHER CSS REMAINS EXACTLY THE SAME */
    
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
    }

    header {
      background: var(--glass);
      backdrop-filter: blur(12px);
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

    .search-bar {
      display: flex;
      gap: 1rem;
      margin-bottom: 2rem;
      flex-wrap: wrap;
    }

    .search-bar input[type="text"] {
      flex: 1;
      padding: 1rem 1.5rem;
      border-radius: var(--radius);
      border: none;
      background: rgba(255, 255, 255, 0.1);
      color: var(--light);
    }

    .search-bar input[type="text"]::placeholder {
      color: rgba(255, 255, 255, 0.7);
    }

    .search-bar button {
      padding: 1rem 1.5rem;
      background: var(--secondary);
      color: var(--dark);
      border: none;
      border-radius: var(--radius);
      font-weight: bold;
      cursor: pointer;
      transition: var(--transition);
    }

    .search-bar button:hover {
      background: #ffb300;
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(255, 193, 7, 0.5);
    }

    .filters {
      display: flex;
      gap: 1.5rem;
      margin-bottom: 2rem;
      flex-wrap: wrap;
    }

    .book-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
      gap: 1.8rem;
    }

    .book-card {
      background: var(--glass);
      border-radius: var(--radius);
      overflow: hidden;
      transition: var(--transition);
      position: relative;
      perspective: 1000px;
    }

    .book-card:hover {
      transform: translateY(-10px) rotateX(5deg) rotateY(5deg);
      box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
    }

    .book-image {
      height: 280px;
      width: 100%;
      overflow: hidden;
    }

    .book-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: var(--transition);
    }

    .book-card:hover .book-image img {
      transform: scale(1.1);
    }

    .book-info {
      padding: 1.5rem;
    }

    .book-title {
      font-weight: 700;
      margin-bottom: 0.8rem;
      font-size: 1.2rem;
      min-height: 3.2rem;
    }

    .book-price {
      font-size: 1.4rem;
      font-weight: 700;
      color: var(--secondary);
      margin-top: 0.8rem;
    }

    @media (max-width: 768px) {
      .filters {
        flex-direction: column;
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
    <h1>Manage Book Listings</h1>
  </div>

  <form action="" method="post">
    <div class="search-bar">
      <input type="text" name="search" placeholder="Search textbooks...">
      <button type="submit" name="sub">Search</button>
    </div>

    <div class="filters">
      <select name="course_code">
        <option disabled selected>Course Code</option>
        <?php while ($row2 = mysqli_fetch_assoc($result2)): ?>
          <option><?= htmlspecialchars($row2['course_code']) ?></option>
        <?php endwhile; ?>
      </select>

      <select name="subject">
        <option disabled selected>Subject</option>
        <?php while ($row3 = mysqli_fetch_assoc($result3)): ?>
          <option><?= htmlspecialchars($row3['subject']) ?></option>
        <?php endwhile; ?>
      </select>

      <select name="semester">
        <option disabled selected>Semester</option>
        <?php while ($row4 = mysqli_fetch_assoc($result4)): ?>
          <option><?= htmlspecialchars($row4['semester']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>
  </form>

  <div class="book-grid">
    <?php if (mysqli_num_rows($result) > 0): ?>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="book-card">
          <div class="book-image">
            <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
          </div>
          <div class="book-info">
            <h3 class="book-title"><?= htmlspecialchars($row['title']) ?></h3>
            <div class="book-price">RM <?= number_format($row['price'], 2) ?></div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No books found.</p>
    <?php endif; ?>
  </div>
</div>

</body>
</html>