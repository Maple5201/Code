<?php
session_start();
if (!isset($_SESSION['registered'])) {
    header('Location: register.php'); // If you visit this page directly without registration, redirect to the registration page
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Success</title>
</head>
<body>
    <div id="success-message">
        <p>Registration successful! Redirecting to login page in 5 seconds...</p>
    </div>
    <script>
        setTimeout(function() {
            window.location.href = 'index.php'; // Change to your login page file path
        }, 5000);  // 5000 Millisecond jump
    </script>
</body>
</html>
<?php
unset($_SESSION['registered']); // Clear session variables so that the page can be displayed again the next time you register
?>
