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

// Prevent buying your own book
if ($_SESSION['studentID'] === $book['studentID']) {
  echo "<p>You cannot purchase your own book.</p>";
  exit();
}

// Prevent purchasing unavailable book (case-insensitive)
if (strtolower($book['status']) !== 'available') {
  echo "<p>This book is no longer available.</p>";
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Confirm Purchase - CampusBooks</title>
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

    .container {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 0 1.5rem;
    }

    .purchase-container {
      background: var(--glass);
      border-radius: var(--radius);
      padding: 2.5rem;
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

    .book-info {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      margin-bottom: 2.5rem;
      background: var(--glass-dark);
      padding: 1.5rem;
      border-radius: var(--radius);
    }

    .info-item {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 0.75rem;
      border-radius: var(--radius);
      background: rgba(255, 255, 255, 0.05);
      transition: var(--transition);
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

    .payment-options {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      margin-bottom: 2rem;
    }

    .option {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 1rem;
      border-radius: var(--radius);
      background: rgba(255, 255, 255, 0.05);
      transition: var(--transition);
    }

    .option:hover {
      background: rgba(255, 255, 255, 0.1);
      transform: scale(1.02);
    }

    .option input[type="radio"] {
      accent-color: var(--secondary);
      transform: scale(1.5);
    }

    .btn {
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

    .btn:hover {
      background: var(--primary);
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(57, 51, 108, 0.5);
    }

    .back-link {
      display: inline-block;
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

    @media (max-width: 576px) {
      .purchase-container {
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
  </header>

  <div class="container">
    <div class="purchase-container">
      <h2>Confirm Purchase</h2>

      <div class="book-info">
        <div class="info-item">
          <i class="fas fa-book-open info-icon"></i>
          <div>
            <div class="info-label">Title:</div>
            <div class="info-value"><?php echo htmlspecialchars($book['title']); ?></div>
          </div>
        </div>
        <div class="info-item">
          <i class="fas fa-money-bill-wave info-icon"></i>
          <div>
            <div class="info-label">Price:</div>
            <div class="info-value">RM <?php echo number_format($book['price'], 2); ?></div>
          </div>
        </div>
        <div class="info-item">
          <i class="fas fa-user info-icon"></i>
          <div>
            <div class="info-label">Seller:</div>
            <div class="info-value"><?php echo htmlspecialchars($book['username']); ?></div>
          </div>
        </div>
      </div>

      <form id="purchaseForm">
        <input type="hidden" id="book_id" value="<?php echo htmlspecialchars($book['Book_id']); ?>">
        <input type="hidden" id="price" value="<?php echo htmlspecialchars($book['price']); ?>">

        <div class="payment-options">
          <label class="option">
            <input type="radio" name="payment_method" value="Visa" required>
            <span><i class="fab fa-cc-visa"></i> Visa Payment</span>
          </label>
          <label class="option">
            <input type="radio" name="payment_method" value="Cash on Delivery (COD)" required>
            <span><i class="fas fa-hand-holding-usd"></i> Cash on Delivery (COD)</span>
          </label>
        </div>

        <button type="submit" class="btn" id="confirmBtn" disabled>Confirm Purchase</button>
      </form>

      <a href="book_details.php?id=<?php echo htmlspecialchars($book['Book_id']); ?>" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Book Details
      </a>
    </div>
  </div>

<script>
  const purchaseForm = document.getElementById("purchaseForm");
  const radios = document.querySelectorAll('input[name="payment_method"]');
  const confirmBtn = document.getElementById("confirmBtn");

  // Enable button when a radio is selected
  radios.forEach(radio => {
    radio.addEventListener('change', () => {
      confirmBtn.disabled = false;
    });
  });

  // Handle form submission
  purchaseForm.addEventListener("submit", function(event) {
    event.preventDefault();

    const selected = document.querySelector('input[name="payment_method"]:checked');
    if (!selected) {
      alert("Please select a payment method.");
      return;
    }

    const method = selected.value;
    const bookId = document.getElementById("book_id").value;
    const price = document.getElementById("price").value;

    if (method === "Visa") {
      window.location.href = `visa_payment.php?id=${encodeURIComponent(bookId)}&price=${encodeURIComponent(price)}&payment=Visa`;
    } else {
      // Submit COD data using POST dynamically
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = 'meetup_location.php';

      const fields = {
        book_id: bookId,
        price: price,
        payment_method: method
      };

      for (const key in fields) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = fields[key];
        form.appendChild(input);
      }

      document.body.appendChild(form);
      form.submit();
    }
  });
</script>
</body>
</html>