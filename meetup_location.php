<?php
session_start();
include 'conn.php';

if (!isset($_POST['book_id']) || !isset($_POST['price']) || !isset($_POST['payment_method'])) {
    header("Location: homepage.php");
    exit();
}

$book_id = $_POST['book_id'];
$price = $_POST['price'];
$payment_method = $_POST['payment_method'];

// Fetch book details
$query = "SELECT b.*, u.username FROM books b 
          JOIN user u ON b.studentID = u.studentID 
          WHERE b.Book_id = $book_id";
$result = mysqli_query($conn, $query);
$book = mysqli_fetch_assoc($result);

if (!$book) {
    echo "<p>Book not found.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Set Meet-up Location - CampusBooks</title>
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

    .container {
      max-width: 800px;
      margin: 3rem auto;
      padding: 0 1.5rem;
    }

    .card {
      background: var(--glass);
      backdrop-filter: blur(12px);
      border-radius: var(--radius);
      padding: 2rem;
      box-shadow: var(--shadow);
    }

    .book-info {
      background: rgba(255, 255, 255, 0.05);
      padding: 1rem;
      border-radius: var(--radius);
      margin-bottom: 1.5rem;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .book-info strong {
      display: block;
      font-size: 1.2rem;
      margin-bottom: 0.5rem;
    }

    label {
      display: block;
      font-weight: 600;
      margin-bottom: 0.5rem;
      margin-top: 1rem;
    }

    textarea {
      width: 100%;
      height: 120px;
      padding: 1rem;
      border-radius: var(--radius);
      border: none;
      resize: vertical;
      font-size: 1rem;
      background: rgba(255, 255, 255, 0.1);
      color: var(--light);
      outline: none;
    }

    .confirm-button {
      display: inline-block;
      margin-top: 1.5rem;
      background: var(--secondary);
      color: var(--dark);
      padding: 1rem 2rem;
      border: none;
      border-radius: 50px;
      font-size: 1rem;
      font-weight: bold;
      cursor: pointer;
      transition: var(--transition);
      box-shadow: 0 6px 15px rgba(255, 193, 7, 0.3);
    }

    .confirm-button:hover {
      background: #ffb300;
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(255, 193, 7, 0.5);
    }

    @media (max-width: 600px) {
      .card {
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body>

<header class="glass">
  <div class="logo"><i class="fas fa-book"></i> CampusBooks</div>
  <nav>
    <a href="homepage.php"><i class="fas fa-home"></i> Home</a>
    <a href="chatroom.php"><i class="fas fa-comments"></i> Messages</a>
  </nav>
</header>

<div class="container">
  <div class="card">
    <h2 style="margin-top: 0; font-size: 2rem; margin-bottom: 1.5rem;">Enter Meet-up Location</h2>

    <div class="book-info">
      <strong><?php echo htmlspecialchars($book['title']); ?></strong><br>
      Price: RM <?php echo number_format($price, 2); ?><br>
      Seller: <?php echo htmlspecialchars($book['username']); ?><br>
      Payment Method: <?php echo htmlspecialchars($payment_method); ?>
    </div>

    <form action="complete_purchase.php" method="post">
      <input type="hidden" name="book_id" value="<?php echo $book['Book_id']; ?>">
      <input type="hidden" name="price" value="<?php echo $price; ?>">
      <input type="hidden" name="payment_method" value="<?php echo htmlspecialchars($payment_method); ?>">

      <label for="location">Meet-up Location:</label>
      <textarea name="location" id="location" required placeholder="E.g. Block B Lobby, Library Entrance, Cafeteria..."></textarea>

      <button type="submit" class="confirm-button">Confirm Purchase</button>
    </form>
  </div>
</div>

</body>
</html>