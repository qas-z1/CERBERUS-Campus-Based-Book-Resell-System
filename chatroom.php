<?php
session_start();
require 'conn.php';

// Redirect if not logged in 
if (!isset($_SESSION['studentID'])) {
    header("Location: login.php");
    exit();
}

// Mark messages as read when chatroom is opened
if (isset($_SESSION['studentID'])) {
    $user_id = $_SESSION['studentID'];
    $update_query = "UPDATE messages SET is_read = 1 WHERE receiver_id = '$user_id' AND is_read = 0";
    mysqli_query($conn, $update_query);
}

$currentUserID = $_SESSION['studentID'];

// Get selected receiver and pre-filled message
$selectedReceiver = isset($_GET['receiver']) ? mysqli_real_escape_string($conn, $_GET['receiver']) : null;
$prefilledMessage = isset($_GET['msg']) ? urldecode($_GET['msg']) : '';

// Fetch all unique conversations for the current user with unread counts and latest message
$chatListQuery = "
    SELECT 
        u.studentID, 
        u.username, 
        u.usertype,
        latest_msg.message as latest_message,
        latest_msg.timestamp as last_time,
        SUM(CASE WHEN m.is_read = 0 AND m.receiver_id = ? THEN 1 ELSE 0 END) as unread_count
    FROM user u
    JOIN (
        -- Get all conversation partners
        SELECT DISTINCT
            IF(sender_id = ?, receiver_id, sender_id) as partner_id
        FROM messages
        WHERE sender_id = ? OR receiver_id = ?
    ) partners ON u.studentID = partners.partner_id
    LEFT JOIN (
        -- Get the latest message for each conversation
        SELECT 
            IF(sender_id = ?, receiver_id, sender_id) as partner_id,
            message,
            timestamp,
            is_read,
            sender_id,
            receiver_id
        FROM messages m1
        WHERE (sender_id = ? OR receiver_id = ?)
        AND timestamp = (
            SELECT MAX(timestamp)
            FROM messages m2
            WHERE 
                (m2.sender_id = m1.sender_id AND m2.receiver_id = m1.receiver_id) OR
                (m2.sender_id = m1.receiver_id AND m2.receiver_id = m1.sender_id)
        )
    ) latest_msg ON u.studentID = latest_msg.partner_id
    LEFT JOIN messages m ON (
        (m.sender_id = ? AND m.receiver_id = u.studentID) OR
        (m.sender_id = u.studentID AND m.receiver_id = ?)
    ) AND m.is_read = 0
    WHERE u.studentID != ?
    GROUP BY u.studentID, u.username, u.usertype, latest_msg.message, latest_msg.timestamp
    ORDER BY last_time DESC";

$stmt = $conn->prepare($chatListQuery);
$stmt->bind_param("ssssssssss", 
    $currentUserID, $currentUserID, $currentUserID, $currentUserID, 
    $currentUserID, $currentUserID, $currentUserID,
    $currentUserID, $currentUserID, $currentUserID);
$stmt->execute();
$chatListResult = $stmt->get_result();

// Check if we have any conversations
$hasConversations = $chatListResult->num_rows > 0;

$receiverName = "";
$receiverUsertype = "";
$receiverExists = false;

if ($selectedReceiver) {
    // Verify the receiver exists and get their details
    $stmt = $conn->prepare("SELECT username, usertype FROM user WHERE studentID = ?");
    $stmt->bind_param("s", $selectedReceiver);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($row = $res->fetch_assoc()) {
        $receiverName = $row['username'];
        $receiverUsertype = $row['usertype'];
        $receiverExists = true;
    }
    
    // Handle sending a new message
    if ($receiverExists && isset($_POST['send']) && !empty(trim($_POST['message']))) {
        $msg = trim($_POST['message']);
        
        // Prevent sending to self
        if ($currentUserID != $selectedReceiver) {
            $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $currentUserID, $selectedReceiver, $msg);
            $stmt->execute();
            $stmt->close();
            
            // Clear the prefilled message after sending
            $prefilledMessage = '';
        } else {
            $_SESSION['error'] = "You cannot send messages to yourself.";
        }
        
        header("Location: chatroom.php?receiver=$selectedReceiver");
        exit();
    }

    // Fetch the conversation if receiver exists and is not self
    if ($receiverExists && $currentUserID != $selectedReceiver) {
        $chatQuery = "SELECT m.*, u.username as sender_name
                     FROM messages m
                     JOIN user u ON m.sender_id = u.studentID
                     WHERE 
                        (m.sender_id = ? AND m.receiver_id = ?) 
                        OR 
                        (m.sender_id = ? AND m.receiver_id = ?)
                     ORDER BY m.timestamp ASC";
        $stmt = $conn->prepare($chatQuery);
        $stmt->bind_param("ssss", $currentUserID, $selectedReceiver, $selectedReceiver, $currentUserID);
        $stmt->execute();
        $chatResult = $stmt->get_result();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CampusBooks Chat</title>
  <style>
    :root {
      --primary: #39336C;
      --primary-light: #4d4685;
      --primary-dark: #2c2753;
      --secondary: #FFC107;
      --light: #FFFFFF;
      --dark: #2d1a53;
      --gray: #f5f5f7;
      --glass: rgba(255, 255, 255, 0.15);
      --glass-dark: rgba(57, 51, 108, 0.7);
      --radius: 16px;
      --shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
      --transition: all 0.3s ease;
    }

    * {
      box-sizing: border-box;
      transition: var(--transition);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      margin: 0;
      background: linear-gradient(135deg, #1a103f, #2a1a5e);
      color: var(--light);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      overflow-x: hidden;
      background-attachment: fixed;
      background-size: cover;
    }

    header {
      background: var(--glass);
      color: var(--light);
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 1000;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    header nav a {
      color: var(--light);
      text-decoration: none;
      margin-left: 25px;
      font-weight: bold;
      font-size: 1rem;
      position: relative;
      padding: 0.5rem 1rem;
      border-radius: 50px;
    }

    header nav a:hover::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.1);
      z-index: -1;
      border-radius: 50px;
    }

    .chat-wrapper {
      display: flex;
      height: calc(100vh - 70px);
      margin-top: auto;
    }

    .user-list {
      width: 25%;
      background: var(--glass);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-right: 1px solid rgba(255, 255, 255, 0.1);
      overflow-y: auto;
      border-radius: 0;
    }

    .user-list h2 {
      margin: 0;
      padding: 20px;
      background: var(--glass-dark);
      color: white;
      font-size: 20px;
      font-weight: 600;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .user-item {
      padding: 15px 20px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      cursor: pointer;
      font-weight: bold;
      transition: var(--transition);
      border-radius: var(--radius);
      margin: 5px 10px;
      position: relative;
      overflow: hidden;
    }

    .user-item::before {
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

    .user-item:hover::before {
      width: 100%;
    }

    .user-item.active::before {
      width: 100%;
      background: rgba(255, 255, 255, 0.2);
    }

    .user-item a {
      text-decoration: none;
      color: var(--light);
      display: block;
    }

    .unread-badge {
      position: absolute;
      top: 10px;
      right: 15px;
      background: var(--secondary);
      color: var(--dark);
      font-size: 12px;
      padding: 3px 6px;
      border-radius: 50%;
      font-weight: bold;
    }

    .chat-box {
      flex: 1;
      display: flex;
      flex-direction: column;
      background: var(--glass);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-radius: 0;
    }

    .chat-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: transparent;
      padding: 20px;
      font-size: 20px;
      font-weight: bold;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .back-button {
      font-size: 22px;
      color: var(--secondary);
      text-decoration: none;
      font-weight: bold;
      transition: var(--transition);
    }

    .back-button:hover {
      transform: scale(1.1);
      color: var(--light);
    }

    .messages {
      flex: 1;
      overflow-y: auto;
      padding: 20px;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .message {
      max-width: 70%;
      padding: 12px 18px;
      border-radius: 30px;
      font-size: 15px;
      line-height: 1.4;
      word-wrap: break-word;
      box-shadow: var(--shadow);
    }

    .sent {
      background: var(--secondary);
      color: var(--dark);
      align-self: flex-end;
      border-bottom-right-radius: 5px;
    }

    .received {
      background: var(--glass-dark);
      color: var(--light);
      align-self: flex-start;
      border-bottom-left-radius: 5px;
    }

    .message-text {
      margin-bottom: 5px;
    }

    .message-time {
      font-size: 0.7em;
      opacity: 0.8;
    }

    .chat-input {
      display: flex;
      padding: 15px 20px;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      background: transparent;
    }

    .chat-input input {
      flex: 1;
      padding: 12px 18px;
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 25px;
      font-size: 14px;
      background: rgba(255, 255, 255, 0.1);
      color: var(--light);
    }

    .chat-input input:focus {
      outline: none;
      border-color: var(--secondary);
      background: rgba(255, 255, 255, 0.15);
      box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.2);
    }

    .chat-input button {
      margin-left: 10px;
      background: var(--primary-light);
      color: var(--light);
      border: none;
      padding: 10px 22px;
      border-radius: 25px;
      font-weight: bold;
      cursor: pointer;
      box-shadow: 0 6px 15px rgba(57, 51, 108, 0.4);
    }

    .chat-input button:hover {
      background: var(--primary);
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(57, 51, 108, 0.5);
    }

    /* Dropdown */
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

    .dropdown-content.show {
      display: block;
      animation: fadeIn 0.3s ease-out forwards;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Typing indicator */
    .typing-indicator {
      background: var(--glass-dark);
      color: var(--light);
      padding: 8px 16px;
      border-radius: 18px;
      align-self: flex-start;
      margin: 5px 0;
      display: inline-block;
      font-size: 0.9em;
    }

    .typing-dots {
      display: inline-block;
    }

    .typing-dots span {
      height: 8px;
      width: 8px;
      background: var(--light);
      border-radius: 50%;
      display: inline-block;
      margin: 0 2px;
      animation: bounce 1.5s infinite ease-in-out;
    }

    .typing-dots span:nth-child(2) {
      animation-delay: 0.2s;
    }

    .typing-dots span:nth-child(3) {
      animation-delay: 0.4s;
    }

    @keyframes bounce {
      0%, 60%, 100% { transform: translateY(0); }
      30% { transform: translateY(-5px); }
    }

    /* Error message */
    .error-message {
      background: rgba(255, 0, 0, 0.2);
      color: #ff6b6b;
      padding: 10px;
      border-radius: 5px;
      margin: 10px;
      text-align: center;
      border: 1px solid rgba(255, 0, 0, 0.3);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .chat-wrapper {
        flex-direction: column;
      }

      .user-list {
        width: 100%;
        max-height: 200px;
      }
    }
  </style>
</head>
<body>

<header>
  <div style="font-size: 24px; font-weight: bold;">CampusBooks</div>
  <nav>
    <a href="homepage.php">Home Page</a>
    <?php if (isset($_SESSION['usertype']) && $_SESSION['usertype'] == 'seller') : ?>
      <a href="listings.php">My Book Listings</a>
    <?php endif; ?>
    <a href="chatroom.php">Messages</a>
    <div class="dropdown">
      <button class="dropdown-button" onclick="toggleDropdown()">
        <span class="dropdown-icon">&#9776;</span>
      </button>
      <div id="dropdownMenu" class="dropdown-content">
        <a href="homepage.php">Books</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Log Out</a>
      </div>
    </div>
  </nav>
</header>

<div class="chat-wrapper">

  <div class="user-list">
    <h2>Messages</h2>
    <?php while($row = $chatListResult->fetch_assoc()): ?>
      <div class="user-item <?php if ($selectedReceiver == $row['studentID']) echo 'active'; ?>">
        <a href="chatroom.php?receiver=<?php echo htmlspecialchars($row['studentID']); ?>">
          <?php echo htmlspecialchars($row['username']) . " (" . htmlspecialchars($row['usertype']) . ")"; ?>
          <?php if ($row['unread_count'] > 0): ?>
            <span class="unread-badge"><?php echo $row['unread_count']; ?></span>
          <?php endif; ?>
          <?php if (!empty($row['latest_message'])): ?>
            <span style="font-size: 0.8em; display: block; font-weight: normal; margin-top: 5px;">
              <?php echo htmlspecialchars(substr($row['latest_message'], 0, 30)) . (strlen($row['latest_message']) > 30 ? '...' : ''); ?>
              <br>
              <small><?php echo date("M j, g:i a", strtotime($row['last_time'])); ?></small>
            </span>
          <?php endif; ?>
        </a>
      </div>
    <?php endwhile; ?>
  </div>

  <?php if ($selectedReceiver && $receiverExists): ?>
    <div class="chat-box">
      <div class="chat-header">
        <?php echo htmlspecialchars($receiverName) . " (" . htmlspecialchars($receiverUsertype) . ")"; ?>
        <a href="homepage.php" class="back-button">&#x2190;</a>
      </div>

      <?php if (isset($_SESSION['error'])): ?>
        <div class="error-message">
          <?php echo htmlspecialchars($_SESSION['error']); ?>
          <?php unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>

      <?php if ($currentUserID == $selectedReceiver): ?>
        <div class="messages" style="justify-content: center; align-items: center;">
          <p>You cannot message yourself.</p>
        </div>
      <?php else: ?>
        <div class="messages">
          <?php if (isset($chatResult)): ?>
            <?php while ($msg = $chatResult->fetch_assoc()): ?>
              <div class="message <?php echo $msg['sender_id'] == $currentUserID ? 'sent' : 'received'; ?>">
                <div class="message-text"><?php echo htmlspecialchars($msg['message']); ?></div>
                <div class="message-time">
                  <?php echo date("g:i a", strtotime($msg['timestamp'])); ?>
                  <?php if ($msg['sender_id'] == $currentUserID): ?>
                    <span style="color: <?php echo $msg['sender_id'] == $currentUserID ? '#2d1a53' : '#ffffff'; ?>;">✓✓</span>
                  <?php endif; ?>
                </div>
              </div>
            <?php endwhile; ?>
          <?php endif; ?>
        </div>

        <form method="POST" class="chat-input">
          <input type="text" name="message" placeholder="Type your message..." required autocomplete="off" 
                 value="<?php echo htmlspecialchars($prefilledMessage); ?>" />
          <button type="submit" name="send">Send</button>
        </form>
      <?php endif; ?>
    </div>
  <?php elseif ($selectedReceiver): ?>
    <div class="chat-box">
      <div class="chat-header">
        User Not Found
        <a href="homepage.php" class="back-button">&#x2190;</a>
      </div>
      <div class="messages" style="justify-content: center; align-items: center;">
        <p>This user doesn't exist or you don't have permission to message them.</p>
      </div>
    </div>
  <?php else: ?>
    <div class="chat-box">
      <div class="chat-header">
        Select a conversation
      </div>
      <div class="messages" style="justify-content: center; align-items: center; text-align: center;">
        <p>Choose a conversation from the list to start chatting</p>
        <p style="font-size: 0.9em; opacity: 0.7;">Or find users through the book listings to start a new conversation</p>
      </div>
    </div>
  <?php endif; ?>

</div>

<script>
  // Toggle dropdown menu
  function toggleDropdown() {
    const dropdown = document.getElementById("dropdownMenu");
    dropdown.classList.toggle("show");
  }

  // Close dropdown when clicking outside
  window.onclick = function(event) {
    if (!event.target.matches('.dropdown-button') && !event.target.closest('.dropdown')) {
      const dropdown = document.getElementById("dropdownMenu");
      if (dropdown.classList.contains('show')) {
        dropdown.classList.remove('show');
      }
    }
  }

  // Auto-scroll to bottom of messages
  const messagesContainer = document.querySelector('.messages');
  if (messagesContainer) {
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
  }

  // Focus message input when chat is opened
  const messageInput = document.querySelector('input[name="message"]');
  if (messageInput) {
    messageInput.focus();
    
    // If there's a prefilled message, move cursor to end
    if (messageInput.value) {
      messageInput.selectionStart = messageInput.selectionEnd = messageInput.value.length;
    }
  }

  // Auto-refresh chat every 5 seconds (optional)
  /*setInterval(() => {
    if (window.location.href.includes('receiver=')) {
      window.location.reload();
    }
  }, 5000);*/
</script>

</body>
</html>