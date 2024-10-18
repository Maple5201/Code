<?php
session_start();

// Connect database
$link = mysqli_connect("sql101.infinityfree.com", "if0_36150369", "zhangIFDB159876", "if0_36150369_Group5");

if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reportedEmail = $_POST['reportedEmail'];
     $contentType = $_POST['contentType'];
    $reportReason = $_POST['reportReason'];

    // Gets the ID of the current logged-in user as the whistleblower ID
    $reporterID = $_SESSION['id']; // Make sure the user is logged in

    // Query the ID of the reported user
    $query = "SELECT UserID FROM Users WHERE Email = ?";
    if ($stmt = mysqli_prepare($link, $query)) {
        mysqli_stmt_bind_param($stmt, "s", $reportedEmail);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $UserID);
                if (mysqli_stmt_fetch($stmt)) {
                    // Create a report
                    $insertQuery = "INSERT INTO Reports (ReporterID, UserID, ContentType, Reason) VALUES (?, ?, ?, ?)";
                    if ($insertStmt = mysqli_prepare($link, $insertQuery)) {
                        $contentType = "Message"; 
                        mysqli_stmt_bind_param($insertStmt, "iiss", $reporterID, $UserID, $contentType, $reportReason);
                        if (mysqli_stmt_execute($insertStmt)) {
                           $_SESSION['report_success_message'] = "Your report has been successfully submitted.";
            header("Location: MainPage.php"); // Redirects back to the main page
            exit();
                        } else {
                            echo "ERROR: Could not submit report.";
                        }
                        mysqli_stmt_close($insertStmt);
                    }
                }
            } else {
                echo "No user found with that email.";
            }
        } else {
            echo "ERROR: Could not prepare query: $query. " . mysqli_error($link);
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($link);
}
?>
