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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Visa Payment - CampusBooks</title>
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
      justify-content: center;
      align-items: center;
      padding: 2rem;
    }

    .payment-container {
      background: var(--glass);
      border-radius: var(--radius);
      padding: 2.5rem;
      width: 100%;
      max-width: 500px;
      box-shadow: var(--shadow);
      animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h2 {
      font-size: 2rem;
      font-weight: 700;
      background: linear-gradient(90deg, var(--light), #d1c4e9);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 2rem;
      position: relative;
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

    .visa-logo {
      text-align: right;
      margin-bottom: 1.5rem;
    }

    .visa-logo img {
      height: 40px;
    }

    .book-info {
      background: var(--glass-dark);
      border-radius: var(--radius);
      padding: 1.5rem;
      margin-bottom: 2rem;
      animation: fadeIn 0.4s ease-out;
    }

    .book-title {
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
    }

    .book-details {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
      font-size: 1rem;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: rgba(255, 255, 255, 0.8);
    }

    input[type="text"],
    input[type="number"],
    input[type="month"] {
      width: 100%;
      padding: 1rem 1.2rem;
      border-radius: var(--radius);
      border: none;
      background: rgba(255, 255, 255, 0.1);
      color: var(--light);
      font-size: 1rem;
      transition: var(--transition);
      border: 1px solid transparent;
    }

    input[type="text"]:focus,
    input[type="number"]:focus,
    input[type="month"]:focus {
      background: rgba(255, 255, 255, 0.15);
      border-color: var(--secondary);
      outline: none;
      box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.2);
    }

    .pay-btn {
      display: block;
      width: 100%;
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

    .pay-btn::before {
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

    .pay-btn:hover::before {
      width: 100%;
    }

    .pay-btn:hover {
      background: var(--primary);
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(57, 51, 108, 0.5);
    }

    @media (max-width: 576px) {
      .payment-container {
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <div class="payment-container">
    <div class="visa-logo">
      <img src="https://upload.wikimedia.org/wikipedia/commons/4/41/Visa_Logo.png"  alt="Visa Logo">
    </div>

    <h2>Visa Payment</h2>

    <div class="book-info">
      <div class="book-title"><?php echo htmlspecialchars($book['title']); ?></div>
      <div class="book-details">
        <span>Price:</span> RM <?php echo number_format($book['price'], 2); ?>
        <span>Seller:</span> <?php echo htmlspecialchars($book['username']); ?>
      </div>
    </div>

    <form action="meetup_location.php" method="post">
      <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['Book_id']); ?>">
      <input type="hidden" name="price" value="<?php echo htmlspecialchars($book['price']); ?>">
      <input type="hidden" name="payment_method" value="Visa">

      <div class="form-group">
        <label for="cardholder">Cardholder Name</label>
        <input type="text" id="cardholder" name="cardholder" required>
      </div>

      <div class="form-group">
        <label for="cardnumber">Card Number</label>
        <input type="text" id="cardnumber" name="cardnumber" pattern="\d{16}" placeholder="1234 5678 9012 3456" required>
      </div>

      <div class="form-group">
        <label for="expiry">Expiry Date</label>
        <input type="month" id="expiry" name="expiry" required>
      </div>

      <div class="form-group">
        <label for="cvv">CVV</label>
        <input type="number" id="cvv" name="cvv" pattern="\d{3}" required>
      </div>

      <button type="submit" class="pay-btn">Pay Now</button>
    </form>
  </div>
</body>
</html>