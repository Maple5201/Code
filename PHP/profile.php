<?php
ob_start();
session_start(); 

$link = mysqli_connect("sql101.infinityfree.com", "if0_36150369", "zhangIFDB159876", "if0_36150369_Group5");

if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}


if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit();
}

$userId = $_SESSION["id"]; 

$hobbies = array();
$hobbySql = "SELECT HobbyName FROM HobbyList hl JOIN UserHobbies uh ON hl.HobbyID = uh.HobbyID WHERE uh.UserID = ?";
if ($hobbyStmt = $link->prepare($hobbySql)) {
    $hobbyStmt->bind_param("i", $userId);
    if ($hobbyStmt->execute()) {
        $hobbyResult = $hobbyStmt->get_result();
        while ($row = $hobbyResult->fetch_assoc()) {
            $hobbies[] = $row['HobbyName']; 
        }
    }
    $hobbyStmt->close();
}

$_SESSION['user_hobbies'] = $hobbies;


if (isset($_POST['updateProfile'])) {
    
    $username = $_POST['username'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $nickname = $_POST['nickname'];
    $city = $_POST['city'];
    $dob = $_POST['dob'];
    $phoneNumber = $_POST['phoneNumber'];
    $description = $_POST['description'];
    $userId = $_SESSION["id"]; 
 
  if (isset($_POST['hobbies'])) {
        
        $selectedHobbies = $_POST['hobbies'];

        
        $deleteHobbySql = "DELETE FROM UserHobbies WHERE UserID = ?";
        if ($deleteStmt = $link->prepare($deleteHobbySql)) {
            $deleteStmt->bind_param("i", $userId);
            $deleteStmt->execute();
            $deleteStmt->close();
        }

        
        $insertHobbySql = "INSERT INTO UserHobbies (UserID, HobbyID) VALUES (?, ?)";
        if ($insertStmt = $link->prepare($insertHobbySql)) {
            foreach ($selectedHobbies as $hobbyId) {
                $insertStmt->bind_param("ii", $userId, $hobbyId);
                $insertStmt->execute();
            }
            $insertStmt->close();
        }
    }
    
    
    $updateSql = "UPDATE Users SET Username=?, Email=?, Gender=?, Nickname=?, City=?, DateOfBirth=?, PhoneNumber=?, Description=? WHERE UserID=?";
    if ($stmt = $link->prepare($updateSql)) {
        $stmt->bind_param("ssssssssi", $username, $email, $gender, $nickname, $city, $dob, $phoneNumber, $description, $userId);
        if ($stmt->execute()) {

            echo "Profile updated successfully.You will be redirected in 5 seconds.";
            echo '<meta http-equiv="refresh" content="5;url=profilepage.php">';
            
            
        } else {
            echo "Error updating profile: " . $stmt->error;
        }
        $stmt->close();
    }
}
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    $userId = $_SESSION["id"];

    $sql = "SELECT * FROM Users WHERE UserID = ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $_SESSION['user_info'] = $user;

        $photoStmt = $link->prepare("SELECT * FROM UserPhotos WHERE UserID = ?");
        $photoStmt->bind_param("i", $userId);
        $photoStmt->execute();
        $photoResult = $photoStmt->get_result();
        $photo = $photoResult->fetch_assoc();

        if ($photo) {
            $_SESSION['user_info']['photo'] = base64_encode($photo['image']);
        }

    } else {
        echo "No user found.";
    }
} else {
    header('Location: index.php');
    exit();
}


if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
    
    $check = getimagesize($_FILES["photo"]["tmp_name"]);
    if ($check !== false) {
        
        $imageData = file_get_contents($_FILES["photo"]["tmp_name"]);
        
        $photoStmt = $link->prepare("SELECT PhotoID FROM UserPhotos WHERE UserID = ?");
        $photoStmt->bind_param("i", $userId);
        $photoStmt->execute();
        $photoResult = $photoStmt->get_result();
        $photoExists = $photoResult->fetch_assoc();

        if ($photoExists) {
           
            $updateStmt = $link->prepare("UPDATE UserPhotos SET image = ? WHERE UserID = ?");
            $updateStmt->bind_param("bi", $null, $userId);
            $updateStmt->send_long_data(0, $imageData); 
            $updateStmt->execute();
            $updateStmt->close();
        } else {
            
            $insertStmt = $link->prepare("INSERT INTO UserPhotos (UserID, image) VALUES (?, ?)");
            $insertStmt->bind_param("ib", $userId, $null);
            $insertStmt->send_long_data(1, $imageData);
            $insertStmt->execute();
            $insertStmt->close();
        }

        
        $_SESSION['user_info']['photo'] = base64_encode($imageData);
        echo "Profile photo updated successfully. You will be redirected in 5 seconds.";
        echo '<meta http-equiv="refresh" content="5;url=profilepage.php">';
    } else {
        echo "File is not an image.";
    }
}



if (isset($_POST['changePassword'])) {
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($newPassword != $confirmPassword) {
        echo "The new password and confirmed password do not match";
    } else {
        $sql = "SELECT Password FROM Users WHERE UserID = ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if ($currentPassword === $row['Password']) {
                $sql = "UPDATE Users SET Password = ? WHERE UserID = ?";
                $stmt = $link->prepare($sql);
                $stmt->bind_param("si", $newPassword, $userId);
                if ($stmt->execute()) {
                    echo "The password has been changed. You will jump in 5 seconds.";
                    echo '<meta http-equiv="refresh" content="5;url=profilepage.php">';
                } else {
                    echo "An error occurred while updating the password：" . $stmt->error;
                }
            } else {
                echo "The current password is incorrect。";
            }
        }
    }
}


mysqli_close($link);
?>