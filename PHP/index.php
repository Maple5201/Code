<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /*Basic Style*/
        body {
            font-family: Arial, sans-serif;
            
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-image: url('CS4116.png');
            background-size: cover;
            background-position: center;
            background-blend-mode: overlay;
        }
        .login-container {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            width: 300px;
        }
        .login-container h2 {
            text-align: center;
            margin: 0 0 20px 0;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-group .error-message {
            color: red;
            font-size: 0.8em;
        }
        .form-group .btn {
            width: 100%;
            padding: 10px;
            border: none;
            background-color: #5cb85c;
            color: white;
            cursor: pointer;
        }
        .form-group .btn:hover {
            background-color: #4cae4c;
        }
        .login-container .register {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #5cb85c;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login to Your Account</h2>
        <form action="login_processor.php" method="post">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="error-message"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="error-message"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn" value="Login">
            </div>
            <?php 
            if (!empty($login_err)) {
                echo '<div class="error-message">' . $login_err . '</div>';
            }
            ?>
        </form>
        <a href="register.php">Not registered yet? Sign up</a>
        <a href="admin_login.php" class="register">Admin Login</a>
    </div>
</body>
</html>
