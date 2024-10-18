<?php
session_start();
include 'db.php';

$current_user_email = $_GET['current_user'];
$talk_to_user_id = $_GET['talk_to_user_id'];


$current_user_id = "Unknown";
$stmt = $pdo->prepare("SELECT UserID FROM Users WHERE Email = ?");
$stmt->bindParam(1, $current_user_email);
$stmt->execute();
$userResult = $stmt->fetch();
if ($userResult) {
    $current_user_id = $userResult['UserID'];
} else {
    echo "no user find.";
    exit;
}

$checkStmt = $pdo->prepare("SELECT room FROM roomsmatch WHERE (user1 = ? AND user2 = ?) OR (user1 = ? AND user2 = ?)");
$checkStmt->execute([$current_user_id, $talk_to_user_id, $talk_to_user_id, $current_user_id]);
$room = $checkStmt->fetchColumn();

if (!$room) {
    $maxRoomStmt = $pdo->query("SELECT MAX(room) FROM roomsmatch");
    $maxRoom = $maxRoomStmt->fetchColumn();
    $nextRoom = $maxRoom !== null ? $maxRoom + 1 : 0;

    
    $insertStmt = $pdo->prepare("INSERT INTO roomsmatch (user1, user2, room) VALUES (?, ?, ?)");
    $insertStmt->execute([$current_user_id, $talk_to_user_id, $nextRoom]);
    $room = $nextRoom;  
    echo "new room: Room " . $room;
} else {
    echo "exist room: Room " . $room;
}
$current_user_nkname = "Unknown";
$stmt = $pdo->prepare("SELECT Nickname FROM Users WHERE Email = ?");
$stmt->bindParam(1, $current_user_email);
$stmt->execute();
$userResult = $stmt->fetch();
if ($userResult) {
    $current_user_nkname = $userResult['Nickname'];
} else {
    echo "no current user.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Talk Page</title>
    <!-->
    <script>
        setTimeout(function() {
            window.location.href = "login.html?username=" + encodeURIComponent("<?php echo $current_user_nkname; ?>") + "&room_id=" + encodeURIComponent("<?php echo $room; ?>");
        }, 5000);
    </script>
    <-->
</head>
<body>
    <h1>Talk Session</h1>
    <p>Current User nkname: <?php echo htmlspecialchars($current_user_nkname); ?></p>
    <p>Current User ID: <?php echo htmlspecialchars($current_user_id); ?></p>
    <p>Talking to User ID: <?php echo htmlspecialchars($talk_to_user_id); ?></p>
    <p>Room ID: <?php echo htmlspecialchars($room); ?></p>
</body>
</html>
