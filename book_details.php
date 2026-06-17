<?php
session_start();
include 'conn.php';

if (!isset($_GET['id'])) {
  header("Location: homepage.php");
  exit();
}

$id = intval($_GET['id']);
$query = "SELECT b.*, u.username FROM books b 
          JOIN user u ON b.studentID = u.studentID 
          WHERE b.Book_id = $id";
$result = mysqli_query($conn, $query);
$book = mysqli_fetch_assoc($result);

if (!$book) {
  echo "<p>Book not found.</p>";
  exit();
}

// Construct the pre-filled message
$message = "Hi, I'm interested in your book 『" . htmlspecialchars($book['title']) . "』 (ID: " . $book['Book_id'] . ") for " . htmlspecialchars($book['course_code']) . " - is it still available?";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Book Details - CampusBooks</title>
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
    }

    header {
      background-color: var(--primary);
      color: var(--light);
      padding: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 1000;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .logo {
      font-size: 28px;
      font-weight: bold;
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

    .container {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 0 1.5rem;
    }

    .back-button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: var(--primary-light);
      color: var(--light);
      font-size: 20px;
      cursor: pointer;
      transition: var(--transition);
      position: relative;
      overflow: hidden;
    }

    .back-button:hover {
      background: var(--primary);
      transform: scale(1.1);
    }

    .title-bar {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-bottom: 2rem;
    }

    .title-bar h1 {
      font-size: 2.5rem;
      font-weight: 700;
      background: linear-gradient(90deg, var(--light), #d1c4e9);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      position: relative;
    }

    .title-bar h1::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 0;
      width: 70px;
      height: 4px;
      background: var(--secondary);
      border-radius: 10px;
    }

    .details-container {
      background: var(--glass);
      border-radius: var(--radius);
      padding: 2.5rem;
      box-shadow: var(--shadow);
      display: flex;
      flex-wrap: wrap;
      gap: 2rem;
      animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .book-image {
      flex: 1 1 300px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .book-image img {
      width: 100%;
      max-width: 300px;
      height: 400px;
      object-fit: cover;
      border-radius: var(--radius);
      transition: transform 0.3s ease-in-out;
    }

    .book-image img:hover {
      transform: scale(1.05);
    }

    .book-info {
      flex: 1 1 400px;
    }

    .book-info h2 {
      font-size: 2.2rem;
      font-weight: 700;
      margin-bottom: 1.5rem;
      position: relative;
    }

    .book-info h2::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 0;
      width: 70px;
      height: 4px;
      background: var(--secondary);
      border-radius: 10px;
    }

    .info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .info-item {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 1rem;
      border-radius: var(--radius);
      background: rgba(255, 255, 255, 0.05);
      transition: var(--transition);
    }

    .info-item:hover {
      background: rgba(255, 255, 255, 0.1);
      transform: scale(1.02);
    }

    .info-icon {
      font-size: 1.5rem;
      color: var(--secondary);
      flex-shrink: 0;
    }

    .info-label {
      font-weight: 600;
      color: rgba(255, 255, 255, 0.8);
    }

    .info-value {
      color: var(--light);
    }

    .seller-info {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-bottom: 2rem;
    }

    .avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: var(--primary-light);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      overflow: hidden;
      font-size: 1.2rem;
      color: var(--light);
    }

    .buttons-group {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
    }

    .btn {
      display: inline-block;
      padding: 1rem 2rem;
      border-radius: 50px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      transition: var(--transition);
      position: relative;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
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

    .description {
      margin-top: 2rem;
      line-height: 1.6;
      color: rgba(255, 255, 255, 0.9);
    }

    @media (max-width: 768px) {
      .details-container {
        flex-direction: column;
      }

      .buttons-group {
        flex-direction: column;
        width: 100%;
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
      <a href="homepage.php">Home</a>
      <a href="#">My listings</a>
      <a href="chatroom.php">Messages</a>
    </nav>
  </header>

  <div class="container">
    <div class="title-bar">
      <a href="homepage.php" class="back-button">
        <i class="fas fa-arrow-left"></i>
      </a>
      <h1>Book Details</h1>
    </div>

    <div class="details-container">
      <div class="book-image">
        <img src="<?php echo $book['image_url']; ?>" alt="Book Cover" />
      </div>

      <div class="book-info">
        <h2><?php echo htmlspecialchars($book['title']); ?></h2>

        <div class="info-grid">
          <div class="info-item">
            <i class="fas fa-code info-icon"></i>
            <div>
              <div class="info-label">Course Code</div>
              <div class="info-value"><?php echo htmlspecialchars($book['course_code']); ?></div>
            </div>
          </div>
          <div class="info-item">
            <i class="fas fa-book-reader info-icon"></i>
            <div>
              <div class="info-label">Subject</div>
              <div class="info-value"><?php echo htmlspecialchars($book['subject']); ?></div>
            </div>
          </div>
          <div class="info-item">
            <i class="fas fa-calendar-week info-icon"></i>
            <div>
              <div class="info-label">Semester</div>
              <div class="info-value">Semester <?php echo htmlspecialchars($book['semester']); ?></div>
            </div>
          </div>
          <div class="info-item">
            <i class="fas fa-box-open info-icon"></i>
            <div>
              <div class="info-label">Condition</div>
              <div class="info-value"><?php echo htmlspecialchars($book['book_condition']); ?></div>
            </div>
          </div>
          <div class="info-item">
            <i class="fas fa-money-bill-wave info-icon"></i>
            <div>
              <div class="info-label">Price</div>
              <div class="info-value">RM <?php echo number_format($book['price'], 2); ?></div>
            </div>
          </div>
        </div>

        <div class="seller-info">
          <div class="avatar"><?php echo substr($book['username'], 0, 1); ?></div>
          <div>
            <strong>Seller:</strong> <?php echo htmlspecialchars($book['username']); ?>
          </div>
        </div>

        <div class="description">
          <strong>Description:</strong><br>
          <?php echo nl2br(htmlspecialchars($book['description'])); ?>
        </div>

        <div class="buttons-group">
          <form action="chatroom.php" method="get">
            <input type="hidden" name="receiver" value="<?php echo htmlspecialchars($book['studentID']); ?>">
            <input type="hidden" name="msg" value="<?php echo htmlspecialchars($message); ?>">
            <button type="submit" class="btn btn-secondary">
              <i class="fas fa-envelope"></i> Message Seller
            </button>
          </form>

          <form action="purchase.php" method="get">
            <input type="hidden" name="id" value="<?php echo $book['Book_id']; ?>">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-shopping-cart"></i> Purchase
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>