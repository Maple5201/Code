<?php
function InitializeData(&$users, &$users_image) {
    $servername = "sql101.infinityfree.com"; 
    $username = "if0_36150369"; 
    $password = "zhangIFDB159876";
    $dbname = "if0_36150369_Group5"; 

    $conn = new mysqli($servername, $username, $password, $dbname);


    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }
    
    
    $sql = "SELECT * FROM Users";
    $result = $conn->query($sql);
    $Imagesql = "SELECT * FROM UserPhotos";
    $image_result = $conn->query($Imagesql);

    $users = array();
    $users_image = array();

    
    if ($result->num_rows > 0) {
        
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        
        foreach ($users as &$user) {
            $date_of_birth = new DateTime($user['DateOfBirth']);
            $today = new DateTime(date("Y-m-d"));
            $user['Age'] = $date_of_birth->diff($today)->y;
        }
    } else {
        echo "0 结果";
    }


    if ($image_result->num_rows > 0) {
        while ($row = $image_result->fetch_assoc()) {
            $row['image'] = base64_encode($row['image']);
            $users_image[] = $row;
        }
    } else {
        echo "0 结果";
    }


    $conn->close();
}

function getUserData($userID) {

    $servername = "sql101.infinityfree.com"; 
    $username = "if0_36150369"; 
    $password = "zhangIFDB159876"; 
    $dbname = "if0_36150369_Group5";

    $conn = new mysqli($servername, $username, $password, $dbname);

    
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }

    
    $sql = "SELECT * FROM Users WHERE userID = $userID";
    $result = $conn->query($sql);
    $userData = array();

    
    if ($result->num_rows > 0) {
        
        $userData = $result->fetch_assoc();
    } else {
        echo "0 结果";
    }

    
    $conn->close();

    return $userData;
}
?>
