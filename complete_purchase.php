<?php 
session_start();
include 'conn.php';

// Validate required POST data
if (!isset($_POST['book_id'], $_POST['price'], $_POST['payment_method'])) {
  echo "Missing purchase information.";
  exit();
}

$book_id = intval($_POST['book_id']);
$price = floatval($_POST['price']);
$payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
$location = isset($_POST['location']) ? mysqli_real_escape_string($conn, trim($_POST['location'])) : 'No location provided';
$buyer_id = $_SESSION['studentID'];

// Get seller studentID and book title
$book_query = mysqli_query($conn, "SELECT studentID, title FROM books WHERE Book_id = '$book_id'");
if (!$book_query || mysqli_num_rows($book_query) == 0) {
  echo "Book not found.";
  exit();
}
$book = mysqli_fetch_assoc($book_query);
$seller_id = $book['studentID'];
$book_title = $book['title'];

// Determine transaction status based on payment method
$transaction_status = (strtolower($payment_method) === 'cash on delivery (cod)') ? 'Awaiting COD Payment' : 'Paid';

// Insert transaction
$insert = "INSERT INTO transactions (
    book_id,
    buyer_studentID,
    seller_studentID,
    price,
    payment_method,
    meetup_location,
    status
  ) VALUES (
    '$book_id',
    '$buyer_id',
    '$seller_id',
    '$price',
    '$payment_method',
    '$location',
    '$transaction_status'
  )";

if (!mysqli_query($conn, $insert)) {
  echo "Failed to save transaction: " . mysqli_error($conn);
  exit();
}

// Get transaction ID
$transaction_id = mysqli_insert_id($conn);

// Update book status to 'Pending Meet-up'
mysqli_query($conn, "UPDATE books SET status = 'Pending Meet-up' WHERE Book_id = '$book_id'");

// Notifications
$buyer_msg = "You have successfully purchased \"$book_title\".";
$seller_msg = "Your book \"$book_title\" has been purchased. <a href='confirm_meetup.php?transaction_id=$transaction_id'>Click here to confirm the meetup</a>.";

// Escape message content for database insertion
$buyer_msg_escaped = mysqli_real_escape_string($conn, $buyer_msg);
$seller_msg_escaped = mysqli_real_escape_string($conn, $seller_msg);

// Insert notifications
mysqli_query($conn, "INSERT INTO notifications (studentID, message) VALUES ('$buyer_id', '$buyer_msg_escaped')");
mysqli_query($conn, "INSERT INTO notifications (studentID, message, transaction_id) VALUES ('$seller_id', '$seller_msg_escaped', '$transaction_id')");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Purchase Completed - CampusBooks</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
  <style>
    :root {
      --primary: #39336C;
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
      align-items: center;
      justify-content: center;
      overflow-x: hidden;
    }

    .message-box {
      max-width: 600px;
      width: 100%;
      background: var(--glass);
      backdrop-filter: blur(12px);
      border-radius: var(--radius);
      padding: 2.5rem;
      text-align: center;
      box-shadow: var(--shadow);
    }

    .message-box h2 {
      font-size: 2rem;
      color: var(--secondary);
      margin-bottom: 1.5rem;
    }

    .detail {
      text-align: left;
      margin-top: 20px;
      font-size: 1rem;
      line-height: 1.4;
    }

    .detail strong {
      font-weight: bold;
    }

    a {
      text-decoration: none;
      color: var(--dark);
      font-weight: bold;
    }

    .back-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      background: var(--secondary);
      color: var(--dark);
      padding: 0.8rem 1.5rem;
      border-radius: 50px;
      font-weight: bold;
      cursor: pointer;
      transition: var(--transition);
      margin-top: 1rem;
    }

    .back-btn:hover {
      background: #ffb300;
      transform: translateY(-3px);
      box-shadow: 0 6px 15px rgba(255, 193, 7, 0.3);
    }

    @media (max-width: 768px) {
      .message-box {
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body>

  <div class="message-box">
    <h2>Purchase Confirmed!</h2>
    <p>Thank you for your purchase.</p>

    <div class="detail">
      <p><strong>Book ID:</strong> <?php echo htmlspecialchars($book_id); ?></p>
      <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($payment_method); ?></p>
      <p><strong>Transaction Status:</strong> <?php echo htmlspecialchars($transaction_status); ?></p>
      <p><strong>Total Price:</strong> RM <?php echo number_format($price, 2); ?></p>

      <?php if (!empty($location)): ?>
        <p><strong>Meet-Up Location:</strong><br><?php echo nl2br(htmlspecialchars($location)); ?></p>
      <?php endif; ?>
    </div>

    <a href="homepage.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Homepage</a>
  </div>

</body>
</html>