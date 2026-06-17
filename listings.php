<?php 
include 'conn.php';
session_start();

// Notification functionality for seller
$show_notifications = false;
$unread_count = 0;

if (isset($_SESSION['usertype']) && $_SESSION['usertype'] == 'seller') {
    $show_notifications = true;
    $stuID = $_SESSION['studentID'] ?? '';
    
    // Notifications
    $notif_query = "SELECT * FROM notifications WHERE studentID = '$stuID' ORDER BY created_at DESC LIMIT 5";
    $notif_result = mysqli_query($conn, $notif_query);

    // Count unread notifications
    $unread_query = "SELECT COUNT(*) as unread_count FROM notifications WHERE studentID = '$stuID' AND is_read = 0";
    $unread_result = mysqli_query($conn, $unread_query);
    $unread_count = ($row = mysqli_fetch_assoc($unread_result)) ? $row['unread_count'] : 0;
}

// Message notifications - for seller
$unread_message_count = 0;
if (isset($_SESSION['studentID'])) {
    $current_user_id = $_SESSION['studentID'];
    $message_query = "SELECT COUNT(*) as unread_count FROM messages WHERE receiver_id = '$current_user_id' AND is_read = 0";
    $message_result = mysqli_query($conn, $message_query);
    $unread_message_count = ($row = mysqli_fetch_assoc($message_result)) ? $row['unread_count'] : 0;
}

$query = "SELECT * FROM books WHERE studentID='{$_SESSION['studentID']}'";

if (!isset($_SESSION['username'])) {
    echo "<p style='color:red;'>Username not set in session.</p>";
} else {
    $uname = $_SESSION['username'];
}

if (isset($_POST['sub']) && isset($_SESSION['studentID'])) {
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
    $query = "SELECT * FROM books WHERE studentID='" . mysqli_real_escape_string($conn, $_SESSION['studentID']) . "'";
}

$result = mysqli_query($conn, $query);

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
  <title>CampusBooks - My Listings</title>
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
      font-size: 1rem;
    }
    
    .filters select option {
      background: var(--primary-dark);
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
      --primary-dark: #2c2753;
      --secondary: #FFC107;
      --light: #FFFFFF;
      --dark: #2d1a53;
      --glass: rgba(255, 255, 255, 0.1);
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
      background-clip: text;
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
      display: flex;
      align-items: center;
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

    /* Notification styles */
    .notification-dropdown {
      position: relative;
      display: inline-block;
    }

    .notification-icon {
      font-size: 20px;
      cursor: pointer;
      color: var(--light);
      position: relative;
    }

    .notif-badge {
      position: absolute;
      top: -5px;
      right: -10px;
      background: var(--secondary);
      color: var(--dark);
      font-size: 12px;
      padding: 3px 6px;
      border-radius: 50%;
      font-weight: bold;
    }

    .message-badge {
      background: var(--secondary);
      color: var(--dark);
      font-size: 12px;
      padding: 3px 6px;
      border-radius: 50%;
      font-weight: bold;
      margin-left: 5px;
    }

    .notification-content {
      display: none;
      position: absolute;
      top: 40px;
      right: 0;
      background-color: rgba(57, 51, 108, 0.9);
      min-width: 300px;
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: var(--radius);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
      z-index: 1000;
      padding: 10px;
      max-height: 400px;
      overflow-y: auto;
    }

    .notification-item {
      padding: 10px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      transition: var(--transition);
    }

    .notification-item.unread {
      background: rgba(255, 193, 7, 0.1);
    }

    .notification-item a {
      color: var(--secondary);
      text-decoration: underline;
    }

    .notification-item small {
      display: block;
      margin-top: 5px;
      font-size: 12px;
      color: rgba(255, 255, 255, 0.7);
    }

    .notification-content.show {
      display: block;
    }

    .loading-messages {
      display: none;
      margin-left: 5px;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    .container {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 0 1.5rem;
    }

    .title {
      font-size: 2.5rem;
      font-weight: 700;
      background: linear-gradient(90deg, var(--light), #d1c4e9);
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
      position: relative;
      display: inline-block;
      margin-bottom: 2rem;
    }

    .title::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 0;
      width: 70px;
      height: 4px;
      background: var(--secondary);
      border-radius: 10px;
    }

    .user-info {
      display: flex;
      gap: 2rem;
      margin-bottom: 2rem;
      flex-wrap: wrap;
    }

    .user-info div {
      background: var(--glass);
      padding: 1rem 1.5rem;
      border-radius: var(--radius);
      min-width: 200px;
      text-align: center;
    }

    .search-bar {
      margin-bottom: 1rem;
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
    }

    .search-bar input {
      padding: 1rem;
      border-radius: var(--radius);
      border: none;
      background: var(--glass);
      color: var(--light);
      font-size: 1rem;
      flex: 1;
    }

    .search-bar button {
      padding: 1rem 2rem;
      border: none;
      border-radius: var(--radius);
      background: var(--primary);
      color: var(--light);
      cursor: pointer;
      transition: var(--transition);
    }

    .search-bar button:hover {
      background: var(--secondary);
      color: var(--dark);
    }

    .filters {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
      margin-bottom: 2rem;
    }

    .book-list {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 2rem;
    }

    .book, .add-book {
      background: var(--glass);
      padding: 1rem;
      border-radius: var(--radius);
      text-align: center;
      transition: var(--transition);
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .book:hover, .add-book:hover {
      transform: translateY(-5px);
      background: rgba(255, 255, 255, 0.1);
    }

    .book img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: var(--radius);
      margin-bottom: 1rem;
    }

    .book-title {
      font-size: 1rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
      color: var(--light);
    }

    .book-price {
      font-size: 0.9rem;
      color: var(--secondary);
      font-weight: 600;
    }
    
    .add-book {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 260px;
    }
    
    .add-book a {
      text-decoration: none;
      color: inherit;
      width: 100%;
      height: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    .plus-icon {
      font-size: 4rem;
      color: var(--secondary);
      margin-bottom: 1rem;
    }

    .add-book-text {
      font-size: 1.2rem;
      font-weight: bold;
      color: var(--light);
    }

    @media (max-width: 1200px) {
      .book-list {
        grid-template-columns: repeat(3, 1fr);
      }
    }

    @media (max-width: 900px) {
      .book-list {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 600px) {
      .book-list {
        grid-template-columns: 1fr;
      }
      
      .notification-content {
        min-width: 250px;
        right: -50px;
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
      <a href="homepage.php">Home Page</a>
      <a href="listings.php">My Book Listings</a>
      <a href="javascript:void(0);" onclick="handleMessagesClick()">Messages
        <?php if ($unread_message_count > 0): ?>
          <span class="message-badge" id="messageBadge"><?php echo $unread_message_count; ?></span>
        <?php endif; ?>
      </a>
      
      <!-- Notification dropdown for seller -->
      <?php if ($show_notifications): ?>
        <div class="notification-dropdown">
          <span class="notification-icon" onclick="toggleNotifications()">
            <i class="fas fa-bell"></i>
            <?php if ($unread_count > 0): ?>
              <span class="notif-badge"><?php echo $unread_count; ?></span>
            <?php endif; ?>
          </span>
          <div class="notification-content" id="notificationBox">
            <?php if (mysqli_num_rows($notif_result) > 0): ?>
              <?php while ($notif = mysqli_fetch_assoc($notif_result)): ?>
                <div class="notification-item <?php echo $notif['is_read'] ? '' : 'unread'; ?>">
                  <?php 
                  // Check if message contains HTML link
                  if (preg_match('/<a href=/i', $notif['message'])) {
                    echo $notif['message'];
                  } else {
                    echo htmlspecialchars($notif['message']);
                  }
                  ?>
                  <small><?php echo $notif['created_at']; ?></small>
                </div>
              <?php endwhile; ?>
            <?php else: ?>
              <div style="padding: 10px; color: var(--light);">No new notifications</div>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
    </nav>
  </header>

  <div class="container">
    <div class="title">My Book Listings</div>

    <div class="user-info">
      <div><strong>Seller Name:</strong> <?php echo $uname; ?></div>
      <div><strong>Seller Student ID:</strong> <?php echo $_SESSION['studentID']; ?></div>
    </div>

    <form action="" method="post">
      <div class="search-bar">
        <input type="text" name="search" placeholder="Search textbooks..." 
          value="<?php echo isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''; ?>" />
        <button type="submit" name="sub">Search</button>
        <button type="button" onclick="window.location.href='listings.php'">Reset</button>
      </div>
      <div class="filters">
        <select name="course_code">
          <option disabled selected>Course code</option>
          <?php while($row2 = mysqli_fetch_assoc($result2)): ?>
            <option value="<?php echo $row2['course_code']; ?>" 
              <?php if (isset($_POST['course_code']) && $_POST['course_code'] == $row2['course_code']) echo 'selected'; ?>>
              <?php echo $row2['course_code']; ?>
            </option>
          <?php endwhile; ?>
        </select>
        <select name="subject">
          <option disabled selected>Subject</option>
          <?php while($row3 = mysqli_fetch_assoc($result3)): ?>
            <option value="<?php echo $row3['subject']; ?>" 
              <?php if (isset($_POST['subject']) && $_POST['subject'] == $row3['subject']) echo 'selected'; ?>>
              <?php echo $row3['subject']; ?>
            </option>
          <?php endwhile; ?>
        </select>
        <select name="semester">
          <option disabled selected>Semester</option>
          <?php while($row4 = mysqli_fetch_assoc($result4)): ?>
            <option value="<?php echo $row4['semester']; ?>" 
              <?php if (isset($_POST['semester']) && $_POST['semester'] == $row4['semester']) echo 'selected'; ?>>
              <?php echo $row4['semester']; ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
    </form>

    <div class="book-list">
      <div class="add-book">
        <a href="seller.php" style="text-decoration:none; color:inherit;">
          <div class="plus-icon">+</div>
          <div class="add-book-text">Add New Book</div>
        </a>
      </div>

      <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <div class="book">
            <a href="seller2.php?id=<?php echo $row['Book_id']; ?>">
              <img src="<?php echo $row['image_url']; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
            </a>
            <div class="book-title"><?php echo htmlspecialchars($row['title']); ?></div>
            <div class="book-price">RM <?php echo number_format($row['price'], 2); ?></div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No books found.</p>
      <?php endif; ?>
    </div>
  </div>

  <script>
    function toggleNotifications() {
      const box = document.getElementById("notificationBox");
      box.classList.toggle("show");

      if (box.classList.contains("show")) {
        // Mark notifications as read when dropdown is opened
        fetch('mark_notifications_read.php')
          .then(response => {
            if (response.ok) {
              const badge = document.querySelector('.notif-badge');
              if (badge) badge.remove();
              
              // Remove unread styling
              document.querySelectorAll('.notification-item.unread').forEach(item => {
                item.classList.remove('unread');
              });
            }
          });
      }
    }

    function handleMessagesClick() {
      // Show loading indicator
      const link = event.currentTarget;
      const loading = document.createElement('i');
      loading.className = 'fas fa-spinner loading-messages';
      link.appendChild(loading);
      loading.style.display = 'inline-block';

      // Mark messages as read first
      fetch('mark_messages_read.php')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Remove the badge if it exists
            const badge = document.getElementById('messageBadge');
            if (badge) badge.remove();
          }
          // Always redirect to chatroom
          window.location.href = 'chatroom.php';
        })
        .catch(error => {
          console.error('Error:', error);
          window.location.href = 'chatroom.php';
        });
    }

    // Close notifications when clicking outside
    document.addEventListener('click', function(event) {
      const notificationDropdown = document.querySelector('.notification-dropdown');
      if (notificationDropdown && !notificationDropdown.contains(event.target)) {
        document.getElementById("notificationBox").classList.remove("show");
      }
    });
  </script>
</body>
</html>