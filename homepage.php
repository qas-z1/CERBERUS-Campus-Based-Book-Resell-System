<?php 
include 'conn.php';
session_start();
if (isset($_POST['reset'])) {
    header("Location: homepage.php");
    exit();
}

// Only fetch notifications for buyers
$show_notifications = false;
$unread_count = 0;

if (isset($_SESSION['usertype']) && $_SESSION['usertype'] == 'buyer') {
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

// Message notifications - for both buyers and sellers
$unread_message_count = 0;
if (isset($_SESSION['studentID'])) {
    $current_user_id = $_SESSION['studentID'];
    $message_query = "SELECT COUNT(*) as unread_count FROM messages WHERE receiver_id = '$current_user_id' AND is_read = 0";
    $message_result = mysqli_query($conn, $message_query);
    $unread_message_count = ($row = mysqli_fetch_assoc($message_result)) ? $row['unread_count'] : 0;
}

// Modified query to only show available books
$query = "SELECT * FROM books WHERE status = 'Available'";
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
}
$result = mysqli_query($conn, $query);

// Get distinct values for filters from available books only
$query2 = "SELECT DISTINCT course_code FROM books WHERE status = 'Available'";
$query3 = "SELECT DISTINCT subject FROM books WHERE status = 'Available'";
$query4 = "SELECT DISTINCT semester FROM books WHERE status = 'Available'";
$result2 = mysqli_query($conn, $query2);
$result3 = mysqli_query($conn, $query3);
$result4 = mysqli_query($conn, $query4);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CampusBooks</title>
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
      --glass-dark: rgba(57, 51, 108, 0.7);
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
    }

    header {
      background: var(--glass);
      color: var(--light);
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

    .logo i {
      font-size: 1.5rem;
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

    nav a.active {
      background: rgba(255, 255, 255, 0.15);
    }

    .dropdown {
      position: relative;
      display: inline-block;
    }

    .dropdown-button {
      background-color: transparent;
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
      background-color: var(--glass);
      min-width: 160px;
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      z-index: 1;
    }

    .dropdown-content a {
      color: var(--light);
      padding: 12px 16px;
      text-decoration: none;
      display: block;
      transition: var(--transition);
    }

    .dropdown-content a:hover {
      background-color: rgba(255, 255, 255, 0.1);
    }

    .dropdown-content.show {
      display: block;
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
      background-color: var(--glass-dark);
      min-width: 250px;
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      z-index: 1000;
      padding: 10px;
    }

    .notification-content div {
      padding: 10px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
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
      max-width: 1400px;
      margin: 2rem auto;
      padding: 0 1.5rem;
    }

    .title {
      font-size: 2.5rem;
      font-weight: 700;
      background: linear-gradient(90deg, var(--light), #d1c4e9);
      -webkit-background-clip: text;
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

    .search-bar {
      margin: 2rem 0;
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
    }

    .search-bar input {
      padding: 1rem 1.5rem;
      width: 300px;
      border-radius: var(--radius);
      border: none;
      background: var(--glass);
      color: var(--light);
      font-size: 1rem;
      transition: var(--transition);
      outline: none;
    }

    .search-bar button {
      padding: 1rem 2rem;
      border: none;
      border-radius: var(--radius);
      background: var(--primary);
      color: var(--light);
      font-weight: 600;
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
      margin-top: 1rem;
    }

    /* Updated filter dropdown styles */
    .filters select {
      padding: 1rem;
      border-radius: var(--radius);
      border: none;
      background: var(--glass);
      color: var(--light);
      font-size: 1rem;
      min-width: 180px;
      transition: var(--transition);
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right 1rem center;
      background-size: 1rem;
      cursor: pointer;
    }

    .filters select:focus {
      outline: none;
      box-shadow: 0 0 0 2px rgba(255, 193, 7, 0.3);
    }

    /* Style for dropdown options */
    .filters option {
      padding: 0.8rem;
      background: var(--primary-dark);
      color: var(--light);
    }

    .filters option:hover {
      background: var(--primary-light) !important;
    }

    .filters option:checked {
      background: var(--primary-light);
    }

    .book-list {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 2rem;
      margin-top: 3rem;
    }

    .book {
      background: var(--glass);
      border-radius: var(--radius);
      overflow: hidden;
      transition: var(--transition);
      transform-style: preserve-3d;
      perspective: 1000px;
      position: relative;
    }

    .book:hover {
      transform: translateY(-10px) rotateX(5deg) rotateY(5deg);
      box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
    }

    .book img {
      width: 100%;
      height: 300px;
      object-fit: cover;
      transition: var(--transition);
    }

    .book:hover img {
      transform: scale(1.05);
    }

    .book-info {
      padding: 1.5rem;
    }

    .book-title {
      font-weight: 700;
      margin-bottom: 1rem;
      font-size: 1.2rem;
      min-height: 3.2rem;
    }

    .book-meta {
      color: rgba(255, 255, 255, 0.8);
      font-size: 0.95rem;
      margin-bottom: 0.5rem;
      display: flex;
      flex-direction: column;
      gap: 0.3rem;
    }

    .book-price {
      font-size: 1.4rem;
      font-weight: 700;
      color: var(--secondary);
      margin-top: 0.8rem;
    }

    /* Add status badge */
    .book-status {
      position: absolute;
      top: 10px;
      right: 10px;
      background: var(--secondary);
      color: var(--dark);
      padding: 5px 10px;
      border-radius: 20px;
      font-weight: bold;
      font-size: 0.8rem;
      z-index: 2;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .animate {
      animation: fadeIn 0.6s ease-out forwards;
    }

    @media (max-width: 768px) {
      .search-bar {
        flex-direction: column;
        gap: 0.5rem;
      }
      
      .filters {
        flex-direction: column;
        gap: 0.5rem;
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
      <?php if (isset($_SESSION['usertype']) && $_SESSION['usertype'] == 'seller') : ?>
        <a href="listings.php">My Book Listings</a>
      <?php endif; ?>
      <?php if (isset($_SESSION['admin_studentID']) && $_SESSION['usertype'] != 'seller' && $_SESSION['usertype'] != 'buyer') : ?>
        <a href="all_books.php">All Book</a>
        <a href="all_users.php">All User</a>
        <a href="homepage_admin.php">Admin</a>
      <?php endif; ?>
      <?php if (isset($_SESSION['usertype']) && ($_SESSION['usertype'] == 'seller' || $_SESSION['usertype'] == 'buyer')) : ?>
        <a href="javascript:void(0);" onclick="handleMessagesClick()">Messages
          <?php if ($unread_message_count > 0): ?>
            <span class="message-badge" id="messageBadge"><?php echo $unread_message_count; ?></span>
          <?php endif; ?>
        </a>
      <?php endif; ?>
      
      <!-- Notification dropdown - only for buyers -->
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
                <div>
                  <?php if (!empty($notif['transaction_id'])): ?>
                    <a href="confirm_meetup.php?transaction_id=<?= $notif['transaction_id'] ?>" style="color: var(--light); text-decoration: none;">
                      <span style="color: var(--light);"><?= htmlspecialchars($notif['message']) ?></span>
                    </a>
                  <?php else: ?>
                    <span style="color: var(--light);"><?= htmlspecialchars($notif['message']) ?></span>
                  <?php endif; ?>
                  <br><small style="font-size: 12px; color: rgba(255, 255, 255, 0.7);"><?= $notif['created_at'] ?></small>
                </div>
              <?php endwhile; ?>
            <?php else: ?>
              <div style="padding: 10px; color: var(--light);">No new notifications</div>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>

      <div class="dropdown">
        <button class="dropdown-button" onclick="toggleDropdown()">
          <span class="dropdown-icon">&#9776;</span>
        </button>
        <div id="dropdownMenu" class="dropdown-content">
          <a href="homepage.php">Books</a>
          <?php if (isset($_SESSION['usertype']) && ($_SESSION['usertype'] == 'seller' || $_SESSION['usertype'] == 'buyer')) : ?>
            <a href="profile.php">Profile</a>
          <?php endif; ?>
          <a href="logout.php">Log Out</a>
        </div>
      </div>
    </nav>
  </header>

  <div class="container animate">
    <div class="title">Browse Available Textbooks</div>
    
    <form action="" method="post" class="animate" style="animation-delay: 0.2s;">
      <div class="search-bar">
        <input type="text" name="search" placeholder="Search textbooks..." 
          value="<?php echo isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''; ?>" />
        <button type="submit" name="sub">Search</button>
        <button type="submit" name="reset" value="1">Reset</button>
      </div>
      <div class="filters animate" style="animation-delay: 0.4s;">
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

    <div class="book-list animate" style="animation-delay: 0.6s;">
      <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <a href="book_details.php?id=<?php echo $row['Book_id']; ?>&sellerID=<?php echo $row['studentID']; ?>" style="text-decoration: none; color: inherit;">
            <div class="book">
              <div class="book-status">Available</div>
              <img src="<?php echo $row['image_url']; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
              <div class="book-info">
                <div class="book-title"><?php echo htmlspecialchars($row['title']); ?></div>
                <div class="book-meta">
                  <div><i class="fas fa-code"></i> <?php echo $row['course_code']; ?></div>
                  <div><i class="fas fa-calendar"></i> <?php echo $row['semester']; ?></div>
                </div>
                <div class="book-price">RM <?php echo number_format($row['price'], 2); ?></div>
              </div>
            </div>
          </a>
        <?php endwhile; ?>
      <?php else: ?>
        <p style="grid-column: 1 / -1; text-align: center; color: var(--light);">No available books found matching your criteria.</p>
      <?php endif; ?>
    </div>
  </div>

  <script>
    function toggleDropdown() {
      const dropdown = document.getElementById("dropdownMenu");
      dropdown.classList.toggle("show");
    }
    
    <?php if ($show_notifications): ?>
    function toggleNotifications() {
      const box = document.getElementById("notificationBox");
      box.classList.toggle("show");

      if (box.classList.contains("show")) {
        fetch('mark_notifications_read.php')
          .then(response => {
            if (response.ok) {
              const badge = document.querySelector('.notif-badge');
              if (badge) badge.remove();
            }
          });
      }
    }
    <?php endif; ?>

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

    window.onclick = function(event) {
      if (!event.target.matches('.dropdown-button') && !event.target.closest('.dropdown')) {
        const dropdown = document.getElementById("dropdownMenu");
        if (dropdown.classList.contains('show')) {
          dropdown.classList.remove('show');
        }
      }
      <?php if ($show_notifications): ?>
      if (!event.target.closest('.notification-dropdown')) {
        document.getElementById("notificationBox").classList.remove("show");
      }
      <?php endif; ?>
    }
  </script>
</body>
</html>