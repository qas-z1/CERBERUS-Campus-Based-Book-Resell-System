<?php
include 'conn.php';
session_start();

if (!isset($_SESSION['studentID'])) {
    http_response_code(403);
    exit("Unauthorized");
}

$studentID = $_SESSION['studentID'];

$sql = "UPDATE notifications SET is_read = 1 WHERE studentID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $studentID);
$stmt->execute();
$stmt->close();

http_response_code(200);
echo "Marked as read";
?>
