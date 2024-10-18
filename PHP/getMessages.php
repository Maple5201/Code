<?php
include 'db.php';

$roomId = $_GET['room_id'];

$stmt = $pdo->prepare("SELECT messages.*, talk_users.avatar_url FROM messages LEFT JOIN talk_users ON messages.username = talk_users.username WHERE messages.room_id = ? ORDER BY messages.created_at DESC");
$stmt->execute([$roomId]);
$messages = $stmt->fetchAll();

echo json_encode($messages);
?>
