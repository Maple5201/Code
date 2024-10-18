<?php
include 'db.php';

$room = isset($_GET['room']) ? $_GET['room'] : '';

$stmt = $pdo->prepare("SELECT user1, user2 FROM couple WHERE room = ?");
$stmt->execute([$room]);
$result = $stmt->fetch();

if ($result) {
    echo json_encode($result);
} else {
    echo json_encode(['error' => 'Room not found']);
}
?>
