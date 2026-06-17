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

// Fetch transaction info
$query = "SELECT t.*, b.title, u.username AS buyer_name 
          FROM transactions t 
          JOIN books b ON t.book_id = b.Book_id 
          JOIN user u ON t.buyer_studentID = u.studentID
          WHERE t.id = '$transaction_id' AND t.seller_studentID = '$seller_id'";

$result = mysqli_query($conn, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    echo "Transaction not found or unauthorized access.";
    exit();
}

$transaction = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $meetup_option = $_POST['meetup_option'];
    $scheduled_time = isset($_POST['scheduled_time']) ? trim($_POST['scheduled_time']) : null;
    $buyer_id = $transaction['buyer_studentID'];
    $book_title = $transaction['title'];
    $location = $transaction['meetup_location'];

    if ($meetup_option === 'now') {
        $message = "The seller has confirmed to meet immediately for \"$book_title\" at location: $location.";
        $meetup_type = 'Now';
    } else {
        if ($scheduled_time) {
            $formatted_time = date("F j, Y \a\\t g:i A", strtotime($scheduled_time));
        } else {
            $formatted_time = '';
        }
        $message = "The seller has scheduled a meetup for \"$book_title\" on $formatted_time at location: $location.";
        $meetup_type = 'Scheduled';
    }

    // Add clickable notification to seller (for marking as complete)
    $seller_message = "Reminder: After completing the meetup for \"$book_title\", <a href='complete_meetup.php?transaction_id=$transaction_id'>click here to mark the transaction as completed</a>.";

    $message_escaped = mysqli_real_escape_string($conn, $message);
    $seller_msg_escaped = mysqli_real_escape_string($conn, $seller_message);

    // Send notification to buyer
    mysqli_query($conn, "INSERT INTO notifications (studentID, message, transaction_id) 
                         VALUES ('$buyer_id', '$message_escaped', '$transaction_id')");

    // Send reminder notification to seller
    mysqli_query($conn, "INSERT INTO notifications (studentID, message, transaction_id) 
                         VALUES ('$seller_id', '$seller_msg_escaped', '$transaction_id')");

    // Insert into meetups table
    $meetup_type_escaped = mysqli_real_escape_string($conn, $meetup_type);
    $scheduled_time_sql = $scheduled_time ? "'" . mysqli_real_escape_string($conn, $scheduled_time) . "'" : "NULL";
    $location_sql = $location ? "'" . mysqli_real_escape_string($conn, $location) . "'" : "NULL";

    $insert_meetup = "INSERT INTO meetups (transaction_id, meetup_type, scheduled_time, location)
                      VALUES ('$transaction_id', '$meetup_type_escaped', $scheduled_time_sql, $location_sql)";
    mysqli_query($conn, $insert_meetup);

    // Update book status to Sold
    $book_id = $transaction['book_id'];
    mysqli_query($conn, "UPDATE books SET status = 'Sold' WHERE Book_id = '$book_id'");

    header("Location: seller.php?meetup_confirmed=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Confirm Meetup - CampusBooks</title>
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

    .container {
      max-width: 600px;
      width: 100%;
      background: var(--glass);
      backdrop-filter: blur(12px);
      border-radius: var(--radius);
      padding: 2.5rem;
      text-align: center;
      box-shadow: var(--shadow);
    }

    h2 {
      font-size: 2rem;
      margin-bottom: 1.5rem;
      color: var(--secondary);
      background: linear-gradient(90deg, var(--light), #d1c4e9);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .info-section {
      text-align: left;
      margin-bottom: 2rem;
      background: rgba(0, 0, 0, 0.2);
      padding: 1.5rem;
      border-radius: var(--radius);
    }

    .info-section strong {
      display: inline-block;
      width: 140px;
      color: var(--secondary);
      font-weight: bold;
      margin-bottom: 0.3rem;
    }

    .form-group {
      margin-bottom: 1.5rem;
      text-align: left;
    }

    .form-group label {
      display: block;
      font-size: 1rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
      color: var(--light);
    }

    .form-group select {
      width: 100%;
      padding: 1rem 1.2rem;
      border: none;
      border-radius: var(--radius);
      background: rgba(255, 255, 255, 0.9);
      color: #000;
      font-size: 1rem;
      transition: var(--transition);
      appearance: none;
      -webkit-appearance: none;
      background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right 1rem center;
      background-size: 1em;
    }

    .form-group select:focus {
      outline: none;
      box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.4);
      background-color: rgba(255, 255, 255, 1);
    }

    .form-group input[type="datetime-local"] {
      width: 100%;
      padding: 1rem 1.2rem;
      border: none;
      border-radius: var(--radius);
      background: rgba(255, 255, 255, 0.9);
      color: #000;
      font-size: 1rem;
      transition: var(--transition);
    }

    .form-group input[type="datetime-local"]:focus {
      outline: none;
      box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.4);
      background-color: rgba(255, 255, 255, 1);
    }

    .form-group option {
      color: #000;
      background-color: #fff;
      padding: 0.5rem;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.8rem;
      padding: 1rem 2rem;
      border-radius: 50px;
      font-weight: bold;
      cursor: pointer;
      transition: var(--transition);
      border: none;
      text-decoration: none;
      font-size: 1rem;
      position: relative;
      overflow: hidden;
      z-index: 1;
      width: 100%;
      margin-top: 1rem;
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
      background: var(--secondary);
      color: var(--dark);
      box-shadow: 0 6px 15px rgba(255, 193, 7, 0.3);
    }

    .btn-primary:hover {
      background: #ffb300;
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(255, 193, 7, 0.4);
    }

    @media (max-width: 768px) {
      .container {
        padding: 1.5rem;
      }
      
      h2 {
        font-size: 1.5rem;
      }
      
      .info-section strong {
        width: 100px;
      }
    }
  </style>
</head>
<body>

  <div class="container">
    <h2>Confirm Meetup for "<?php echo htmlspecialchars($transaction['title']); ?>"</h2>

    <div class="info-section">
      <strong>Buyer:</strong> <?php echo htmlspecialchars($transaction['buyer_name']); ?><br>
      <strong>Transaction ID:</strong> <?php echo $transaction_id; ?><br>
      <strong>Location:</strong> <?php echo htmlspecialchars($transaction['meetup_location']); ?>
    </div>

    <form method="POST">
      <div class="form-group">
        <label for="meetup_option">Meetup Option</label>
        <select id="meetup_option" name="meetup_option" onchange="toggleSchedule()" required>
          <option value="">-- Select Option --</option>
          <option value="now">Meet Now</option>
          <option value="schedule">Schedule Meetup</option>
        </select>
      </div>

      <div id="scheduleBox" style="display:none;">
        <div class="form-group">
          <label for="scheduled_time">Choose Date and Time</label>
          <input type="datetime-local" id="scheduled_time" name="scheduled_time">
        </div>
      </div>

      <button type="submit" class="btn btn-primary">
        <i class="fas fa-check-circle"></i> Confirm Meetup
      </button>
    </form>
  </div>

  <script>
    function toggleSchedule() {
      const option = document.getElementById('meetup_option').value;
      const scheduleBox = document.getElementById('scheduleBox');
      scheduleBox.style.display = (option === 'schedule') ? 'block' : 'none';
      
      // Set min date/time to current if schedule is selected
      if (option === 'schedule') {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        
        document.getElementById('scheduled_time').min = `${year}-${month}-${day}T${hours}:${minutes}`;
      }
    }
  </script>

</body>
</html>