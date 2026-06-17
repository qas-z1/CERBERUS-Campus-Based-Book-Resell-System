<?php
include 'conn.php';
session_start();

if (isset($_SESSION['studentID'])) {
    $user_id = $_SESSION['studentID'];
    // Update all unread messages for this user
    $update_query = "UPDATE messages SET is_read = 1 WHERE receiver_id = '$user_id' AND is_read = 0";
    mysqli_query($conn, $update_query);
    
    // Return success response
    echo json_encode(['success' => true]);
    exit();
}

// Return failure response if not logged in
echo json_encode(['success' => false]);
?>