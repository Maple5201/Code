<?php
ob_start();
session_start();

//Attempting to connect to the database
$link = mysqli_connect("sql101.infinityfree.com", "if0_36150369", "zhangIFDB159876", "if0_36150369_Group5");

//Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

//Initialize variables
$username = "";
$password = "";
$username_err = "";
$password_err = "";
$login_err = "";

//Process form data when submitting forms
if($_SERVER["REQUEST_METHOD"] == "POST"){

    //Check if the email is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter your email.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    //Check if the password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    //If there are no errors, try logging in
    if(empty($username_err) && empty($password_err)){
        //Prepare a selection statement
        $sql = "SELECT UserID, Email, Password FROM Users WHERE Email = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            //Bind variables to prepared statements as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            //Set parameters
            $param_username = $username;
            
            
            if(mysqli_stmt_execute($stmt)){
                
                mysqli_stmt_store_result($stmt);
                
                //Check if the email exists, and if so, verify the password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    //Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
    if($password == trim($hashed_password)){
        //The password is correct. Now check if the user has any warning or ban records
        $moderation_sql = "SELECT ActionID, ActionTaken, IsActive FROM ModerationActions WHERE UserID = ? AND IsActive = 1 ORDER BY ActionID DESC LIMIT 1";
        
        if ($moderation_stmt = mysqli_prepare($link, $moderation_sql)) {
            mysqli_stmt_bind_param($moderation_stmt, "i", $param_id);
            $param_id = $id;
            
            
            if (mysqli_stmt_execute($moderation_stmt)) {
                mysqli_stmt_store_result($moderation_stmt);
                
                
                if (mysqli_stmt_num_rows($moderation_stmt) >= 1) {
                    mysqli_stmt_bind_result($moderation_stmt, $action_id, $action_taken, $is_active);
                    
                    if (mysqli_stmt_fetch($moderation_stmt)) {
                        if ($action_taken == 'Banned') {
                            //User is banned and unable to log in
                            $login_err = "Your account has been banned. Please contact the administrator to resolve the issue. You will be redirected to the login page in 5 seconds.";
                            echo '<div class="error-message">' . $login_err . '</div>';
                            echo '<script>setTimeout(function(){ window.location = "index.php"; }, 5000);</script>';
                            exit();
                        } elseif ($action_taken == 'Warned') {
                                    $_SESSION["loggedin"] = true;
                                    $_SESSION["id"] = $id;
                                    $_SESSION["username"] = $username;
                                    echo '<script>alert("Your account has been warned. Please contact the administrator for details. In 5 seconds, you will be redirected to the main page."); 
                                    setTimeout(function(){ window.location = "MainPage.php"; }, 5000);</script>';
                                    exit(); 
                                }
                    }
                }
            }
            mysqli_stmt_close($moderation_stmt);
        }
        
        //If the user has not been banned or warned, or there is no relevant record, login normally
        $_SESSION["loggedin"] = true;
        $_SESSION["id"] = $id;
        $_SESSION["username"] = $username;
        
        //Redirect users to the main page
        header("location: MainPage.php");
        exit();
    } else{
        //Incorrect password, displaying an error message
        $login_err = "Invalid password.";
    }
}
                } else{
                    //The email does not exist, displaying an error message
                    $login_err = "Invalid email or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            
            mysqli_stmt_close($stmt);
        }
    }
    
    
    mysqli_close($link);
}

//If there is an error or the user is not logged in, display the login form
if(!empty($login_err)){
    echo '<div class="error-message">' . $login_err . '</div>';
}
?>