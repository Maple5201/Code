<?php
session_start();

$link = mysqli_connect("sql101.infinityfree.com", "if0_36150369", "zhangIFDB159876", "if0_36150369_Group5");

if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $adminId = $_SESSION['id']; 
        $adminEmail = $_SESSION['email']; 

        switch ($action) {
            case 'ban':
            case 'warn':
                
                $reportId = $_POST['reportId'];
                $userId = $_POST['userId'];

                $query = $action === 'ban'
                    ? "INSERT INTO ModerationActions (UserID, ReportID, AdminID, ActionTaken, ActionDate, IsActive, Email) VALUES (?, ?, ?, 'Banned', NOW(), 1, ?)"
                    : "INSERT INTO ModerationActions (UserID, ReportID, AdminID, ActionTaken, ActionDate, IsActive, Email) VALUES (?, ?, ?, 'Warned', NOW(), 1, ?)";
                if ($stmt = mysqli_prepare($link, $query)) {
                    mysqli_stmt_bind_param($stmt, "iiis", $userId, $reportId, $adminId, $adminEmail);
                }
                break;

            case 'unban':
                
                $userId = $_POST['userId'];

                $query = "DELETE FROM ModerationActions WHERE UserID = ? AND ActionTaken = 'Banned'";
                if ($stmt = mysqli_prepare($link, $query)) {
                    mysqli_stmt_bind_param($stmt, "i", $userId);
                }
                break;

            case 'delete':
                
                $userId = $_POST['userId'];

                $query = "DELETE FROM Users WHERE UserID = ?";
                if ($stmt = mysqli_prepare($link, $query)) {
                    mysqli_stmt_bind_param($stmt, "i", $userId);
                }
                break;

            default:
                echo "Invalid action.";
                exit();
        }

        
        if (isset($stmt) && mysqli_stmt_execute($stmt)) {
            echo "Action $action has been processed for user ID $userId.";
        } else {
            echo "Error: " . mysqli_error($link);
        }
        
        if (isset($stmt)) {
            mysqli_stmt_close($stmt);
        }

    } else {
        echo "No action received.";
    }
    mysqli_close($link);
    exit();
}


if (isset($_GET['load'])) {
    switch ($_GET['load']) {
        case 'userlist':
            echo getUserList($link);
            break;
        case 'reports':
            echo getReportList($link);
            break;
        case 'actions':
            echo getActionsList($link);
            break;
    }
    exit();
}

function getUserList($link) {
    $result = mysqli_query($link, "SELECT UserID, Username, Email FROM Users");
    $html = "<table><tr><th>User ID</th><th>Username</th><th>Email</th><th>Actions</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        $html .= "<tr>
                    <td>{$row['UserID']}</td>
                    <td>{$row['Username']}</td>
                    <td>{$row['Email']}</td>
                    <td>
                        <button class='btn' onclick=\"processAction('delete', null, {$row['UserID']})\">Delete</button>
                        <button class='btn' onclick=\"processAction('unban', null, {$row['UserID']})\">Unban</button>
                    </td>
                  </tr>";
    }
    $html .= "</table>";
    return $html;
}


function getReportList($link) {
    // Assuming Reports table contains ReportID, ReporterID, UserID, ContentType, Reason columns
    $result = mysqli_query($link, "SELECT ReportID, ReporterID, UserID, ContentType, Reason FROM Reports");
    $html = "<table><tr><th>Report ID</th><th>Reporter ID</th><th>User ID</th><th>ContentType</th><th>Reason</th><th>Actions</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        $html .= "<tr>
            <td>{$row['ReportID']}</td>
            <td>{$row['ReporterID']}</td>
            <td>{$row['UserID']}</td>
            <td>{$row['ContentType']}</td>
            <td>{$row['Reason']}</td>
            <td>
                <button class='btn' onclick=\"processAction('warn', {$row['ReportID']}, {$row['UserID']})\">Warn</button>
                <button class='btn' onclick=\"processAction('ban', {$row['ReportID']}, {$row['UserID']})\">Ban</button>
            </td>
          </tr>";

    }
    $html .= "</table>";
    return $html;
}

function getActionsList($link) {
    // Assuming ModerationActions table contains ActionID, UserID, ReportID, AdminID, ActionTaken columns
    $result = mysqli_query($link, "SELECT ActionID, UserID, ReportID, AdminID, ActionTaken FROM ModerationActions");
    $html = "<table><tr><th>Action ID</th><th>User ID</th><th>Report ID</th><th>Admin ID</th><th>Action Taken</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        $html .= "<tr>
                    <td>{$row['ActionID']}</td>
                    <td>{$row['UserID']}</td>
                    <td>{$row['ReportID']}</td>
                    <td>{$row['AdminID']}</td>
                    <td>{$row['ActionTaken']}</td>
                  </tr>";
    }
    $html .= "</table>";
    return $html;
}
?>