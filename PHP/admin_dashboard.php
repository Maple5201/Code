<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .form-group { margin-top: 20px; }
        .btn { cursor: pointer; padding: 8px; background-color: blue; color: white; border: none; }
    </style>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <div>
        <h2>User List</h2>
        <!-- User list will be loaded here -->
        <div id="user_list"></div>
        
        <h2>Reports</h2>
        <!-- Reports list will be loaded here -->
        <div id="report_list"></div>
        
        <h2>Admin Actions</h2>
        <!-- Admin actions will be loaded here -->
        <div id="actions_list"></div>
    </div>
    <script src="admin_processor.php?load=userlist"></script>
    <script src="admin_processor.php?load=reports"></script>
    <script src="admin_processor.php?load=actions"></script>
    <script>
    

function processAction(action, reportId, userId) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "admin_processor.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    
    var data = "action=" + action;
    if (action === 'warn' || action === 'ban') {
        data += "&reportId=" + reportId + "&userId=" + userId;
    } else if (action === 'unban' || action === 'delete') {
        data += "&userId=" + userId;
    }

    xhr.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            alert(this.responseText);
            loadData(); 
        }
    };
    xhr.send(data);
}


function loadData() {
    loadSection('userlist', 'user_list');
    loadSection('reports', 'report_list');
    loadSection('actions', 'actions_list');
}

function loadSection(section, elementId) {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "admin_processor.php?load=" + section, true);
    xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 300) {
            document.getElementById(elementId).innerHTML = xhr.responseText;
        } else {
            console.error("Failed to load data:", xhr.responseText);
        }
    };
    xhr.onerror = function () {
        console.error("Error during AJAX request.");
    };
    xhr.send();
}


document.addEventListener("DOMContentLoaded", loadData);
</script>

</body>
</html>