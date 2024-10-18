<?php
include 'db.php';

$username = $_POST['username'];
$message = $_POST['message'];
$roomId = $_POST['room_id'];

$stmt = $pdo->prepare("INSERT INTO messages (username, message, room_id) VALUES (?, ?, ?)");
$stmt->execute([$username, $message, $roomId]);

echo json_encode(['status' => 'success']);
?>
