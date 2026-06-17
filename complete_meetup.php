<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['studentID'])) {
  echo "Please login first.";
  exit();
}

$seller_id = $_SESSION['studentID'];
$transaction_id = isset($_GET['transaction_id']) ? intval($_GET['transaction_id']) : 0;

if ($transaction_id <= 0) {
  echo "Invalid transaction ID.";
  exit();
}

// Verify transaction exists and belongs to seller
$query = "SELECT t.*, b.title, u.username AS buyer_name
          FROM transactions t
          JOIN books b ON t.book_id = b.Book_id
          JOIN user u ON u.studentID = t.buyer_studentID
          WHERE t.id = '$transaction_id' AND t.seller_studentID = '$seller_id'";

$result = mysqli_query($conn, $query);
if (!$result || mysqli_num_rows($result) == 0) {
  echo "Transaction not found or access denied.";
  exit();
}

$transaction = mysqli_fetch_assoc($result);

// If already completed
if (strtolower($transaction['status']) === 'completed') {
  $alreadyCompleted = true;
} else {
  // Mark transaction as completed
  $update_query = "UPDATE transactions SET status = 'Completed' WHERE id = '$transaction_id'";
  mysqli_query($conn, $update_query);

  $buyer_id = $transaction['buyer_studentID'];
  $book_title = $transaction['title'];

  // Notifications to buyer
  $message1 = "The seller has marked the transaction for \"$book_title\" as completed.";
  $message2 = "Meet-up completed for \"$book_title\". Thank you for using CampusBooks!";

  // Notification to seller
  $message3 = "You have successfully completed the meetup for \"$book_title\".";

  // Escape
  $msg1 = mysqli_real_escape_string($conn, $message1);
  $msg2 = mysqli_real_escape_string($conn, $message2);
  $msg3 = mysqli_real_escape_string($conn, $message3);

  // Insert notifications for buyer
  mysqli_query($conn, "INSERT INTO notifications (studentID, message, transaction_id) 
                       VALUES ('$buyer_id', '$msg1', '$transaction_id')");
  mysqli_query($conn, "INSERT INTO notifications (studentID, message, transaction_id) 
                       VALUES ('$buyer_id', '$msg2', '$transaction_id')");

  // Insert notification for seller
  mysqli_query($conn, "INSERT INTO notifications (studentID, message, transaction_id) 
                       VALUES ('$seller_id', '$msg3', '$transaction_id')");

  $alreadyCompleted = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Meetup Completed</title>
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
      flex-direction: column;
    }

    .container {
      max-width: 600px;
      margin: auto;
      padding: 2rem;
      text-align: center;
    }

    .card {
      background: var(--glass);
      border-radius: var(--radius);
      overflow: hidden;
      padding: 2rem;
      box-shadow: var(--shadow);
      animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
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

    p {
      font-size: 1.6rem;
      color: var(--light);
      margin-bottom: 1.5rem;
    }

    strong {
      color: var(--secondary);
    }

    a {
      display: inline-block;
      padding: 1rem 2rem;
      border-radius: 50px;
      font-weight: bold;
      cursor: pointer;
      transition: var(--transition);
      border: none;
      text-decoration: none;
      font-size: 1.2rem;
      position: relative;
      overflow: hidden;
    }

    a::before {
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

    a:hover::before {
      width: 100%;
    }

    a:hover {
      background: var(--primary);
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(57, 51, 108, 0.5);
    }

    .back-link {
      margin-top: 2rem;
      color: var(--secondary);
      text-decoration: none;
      font-weight: 500;
      transition: var(--transition);
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .back-link:hover {
      color: var(--light);
      transform: translateX(-5px);
    }
  </style>
</head>
<body>

  <div class="container">

    <div class="card">

      <?php if ($alreadyCompleted): ?>
        <h2>Already Completed</h2>
        <p>This transaction has already been marked as completed previously.</p>
      <?php else: ?>
        <h2>Meet-Up Completed!</h2>
        <p>You have successfully marked the transaction as <strong>Completed</strong> for the book:</p>
        <p><strong>"<?php echo htmlspecialchars($transaction['title']); ?>"</strong></p>
      <?php endif; ?>

      <a href="seller.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Seller Dashboard
      </a>

    </div>

  </div>

</body>
</html>