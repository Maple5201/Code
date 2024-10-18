<?php
session_start();

// Make sure the user name is already in the session
if (!isset($_SESSION["username"])) {
    echo "unlogin";
    exit();
}

$username = $_SESSION["username"];

$host = 'sql101.infinityfree.com';
$dbname = 'if0_36150369_Group5';
$db_username = 'if0_36150369';
$password = 'zhangIFDB159876';
$dsn = "mysql:host=$host;dbname=$dbname";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $db_username, $password, $options);
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE Email = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user) {

        $userId = $user['UserID'];
        $rooms = [];

        // search user1
        $stmtRoom1 = $pdo->prepare("SELECT room, user2 AS otherUserID FROM roomsmatch WHERE user1 = :userid");
        $stmtRoom1->execute(['userid' => $userId]);
        $rooms1 = $stmtRoom1->fetchAll(PDO::FETCH_ASSOC);

        // search user2
        $stmtRoom2 = $pdo->prepare("SELECT room, user1 AS otherUserID FROM roomsmatch WHERE user2 = :userid");
        $stmtRoom2->execute(['userid' => $userId]);
        $rooms2 = $stmtRoom2->fetchAll(PDO::FETCH_ASSOC);

        // merge two userlist
        $rooms = array_merge($rooms1, $rooms2);
        $uniqueRooms = [];
        foreach ($rooms as $room) {
            if (!isset($uniqueRooms[$room['room']])) {
                $uniqueRooms[$room['room']] = $room['otherUserID'];
            }
        }

        if ($uniqueRooms) {
            foreach ($uniqueRooms as $room => $otherUserID) {
                $stmtOtherUser = $pdo->prepare("SELECT Username FROM Users WHERE UserID = :otherUserID");
                $stmtOtherUser->execute(['otherUserID' => $otherUserID]);
                $otherUsername = $stmtOtherUser->fetchColumn();
            }
        } else {
            echo "this user do not join any room<br>";
        }
    } else {
        echo "no user find";
    }
} catch (PDOException $e) {
    echo "disconnect db: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat Partners</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 10px;
            padding: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {background-color: #f5f5f5;}
        .chat-link {
            display: block;
            color: #0000EE; /* Link color */
            text-decoration: none;
        }
        .chat-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Chat Rooms Overview</h1>
    <table>
        <tr>
            <th>History Users</th>
        </tr>
        <?php foreach ($uniqueRooms as $room => $otherUserID): ?>
            <?php
            $stmtOtherUser = $pdo->prepare("SELECT Username FROM Users WHERE UserID = :otherUserID");
            $stmtOtherUser->execute(['otherUserID' => $otherUserID]);
            $otherUsername = $stmtOtherUser->fetchColumn();
            ?>
            <tr>
                <td>
                    <a href="talk_page.php?current_user=<?php echo urlencode($_SESSION["username"]); ?>&talk_to_user_id=<?php echo $otherUserID; ?>" class="chat-link">
                        <?php echo htmlspecialchars($otherUsername); ?>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>