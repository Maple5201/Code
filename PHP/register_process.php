<?php

session_start(); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("sql101.infinityfree.com", "if0_36150369", "zhangIFDB159876", "if0_36150369_Group5");

    if ($conn->connect_error) {
        die("Connect failed: " . $conn->connect_error);
    }

    
    $firstname = $_POST['firstname'];
    $secondname = $_POST['secondname'];
    $username = $firstname . " " . $secondname;
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);
    
    $gender = $conn->real_escape_string($_POST['gender']);
    $nickname = $conn->real_escape_string($_POST['nickname']);
    $city = $conn->real_escape_string($_POST['city']);
    $dob = $conn->real_escape_string($_POST['dob']);
    $description = $conn->real_escape_string($_POST['description']);
    $other_profile_info = $conn->real_escape_string($_POST['other_profile_info']);
    $PhoneNumber = $conn->real_escape_string($_POST['PhoneNumber']);
    
    //Check if mandatory fields are empty
    if (empty($username) || empty($email) || empty($password) || empty($gender) || empty($nickname) || empty($city) || empty($dob)) {
        $_SESSION['error'] = "All required fields are mandatory!";
        header("location: register.php"); 
        exit();
    }

    
    //Check if the username or email has been registered
    $sql = "SELECT UserID FROM Users WHERE Username = ? OR Email = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $_SESSION['error'] = "The username or email address has been registered.";
            header("location: register.php"); 
            exit();
        }
        $stmt->close();
    }

    
    $sql = "INSERT INTO Users (Username, Email, Password, Gender, Nickname, City, DateOfBirth, Description, OtherProfileInfo, RegistrationDate, PhoneNumber) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
    if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ssssssssss", $username, $email, $password, $gender, $nickname, $city, $dob, $description, $other_profile_info, $PhoneNumber);
    if ($stmt->execute()) {
        $last_id = $conn->insert_id;  
        $_SESSION['registered'] = true;  
    } else {
        echo "User registration error: " . $stmt->error;
    }
    $stmt->close();
    }

    //Process image upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
    $imageContent = file_get_contents($_FILES['photo']['tmp_name']);
    $photoDescription = ''; 
    $stmt_photo = $conn->prepare("INSERT INTO UserPhotos (UserID, image, PhotoDescription, UploadDate) VALUES (?, ?, ?, NOW())");
    $stmt_photo->bind_param("ibs", $last_id, $null, $photoDescription); 
    $stmt_photo->send_long_data(1, $imageContent); 
    if ($stmt_photo->execute()) {
        header("location: success.php"); 
        exit();
    } else {
        echo "Image upload error: " . $stmt_photo->error;
    }
    $stmt_photo->close();
    }    
    $conn->close();
}

?>