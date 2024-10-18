<?php
ob_start();
session_start();

$link = mysqli_connect("sql101.infinityfree.com", "if0_36150369", "zhangIFDB159876", "if0_36150369_Group5");
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

$email = $password = $email_err = $password_err = $login_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if(empty($email)){
        $email_err = "Please enter your email.";
    }
    
    if(empty($password)){
        $password_err = "Please enter your password.";
    }

    if(empty($email_err) && empty($password_err)){
        $sql = "SELECT AdminID, Email, Password FROM Admins WHERE Email = ?";

        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = $email;

            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);

                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    mysqli_stmt_bind_result($stmt, $id, $email, $hashed_password);

                    if(mysqli_stmt_fetch($stmt)){
                        if($password == trim($hashed_password)){
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["email"] = $email;                            

                            header("location: admin_dashboard.php");
                            exit();
                        } else{
                            $login_err = "Invalid password.";
                        }
                    }
                } else{
                    $login_err = "No account found with that email.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        }
    }

    mysqli_close($link);

    if(!empty($login_err)){
        echo '<div class="error-message">' . $login_err . '</div>';
    }
}
?>
