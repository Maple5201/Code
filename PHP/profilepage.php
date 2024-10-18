 <?php
session_start(); 


$link = mysqli_connect("sql101.infinityfree.com", "if0_36150369", "zhangIFDB159876", "if0_36150369_Group5");
if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}


if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header('Location: index.php'); 
    exit;
}


$userId = $_SESSION["id"]; 
$sql = "SELECT * FROM Users WHERE UserID = ?";
if ($stmt = $link->prepare($sql)) {
    $stmt->bind_param("i", $userId);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        if ($user) {
            $_SESSION['user_info'] = $user; 
        } else {
            echo "No user found.";
        }
    } else {
        echo "Error executing query: " . $stmt->error;
    }
    $stmt->close();
}

if (isset($_SESSION['user_info']['photo'])) {
    $photoData = $_SESSION['user_info']['photo'];
} else {
    
    $photoSql = "SELECT image FROM UserPhotos WHERE UserID = ?";
    if ($photoStmt = $link->prepare($photoSql)) {
        $photoStmt->bind_param("i", $_SESSION['id']);
        if ($photoStmt->execute()) {
            $result = $photoStmt->get_result();
            if ($row = $result->fetch_assoc()) {
                
                $photoData = 'data:image/jpeg;base64,' . base64_encode($row['image']);
                
                $_SESSION['user_info']['photo'] = $photoData;
            } else {
                
                $photoData = 'path_to_default_avatar.jpg';
            }
        }
        $photoStmt->close();
    }
}

$allHobbiesSql = "SELECT * FROM HobbyList";
$allHobbies = array();

if ($result = $link->query($allHobbiesSql)) {
    while ($row = $result->fetch_assoc()) {
        $allHobbies[] = $row;
    }
}

$userHobbiesSql = "SELECT HobbyID FROM UserHobbies WHERE UserID = ?";
$userHobbies = array();

if ($stmt = $link->prepare($userHobbiesSql)) {
    $stmt->bind_param("i", $userId);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $userHobbies[] = $row['HobbyID'];
        }
    } else {
        echo "Error executing query: " . $stmt->error;
    }
    $stmt->close();
}

mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Profile - LoveConnect</title>
<style>
    /* Base styles */
    body {
        font-family: 'Trebuchet MS', Helvetica, sans-serif;
        background-color: #f4e1e1;
        color: #333;
        line-height: 1.6;
    }

    .profile-container {
        background-color: white;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        max-width: 500px;
        margin: 40px auto;
        padding: 20px;
        border-top: 4px solid #ff758f;
    }

    h1 {
        color: #ff5e99;
        text-align: center;
    }

    /* Form styles */
    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        color: #ff758f;
    }

    .form-group input, .form-group textarea {
        width: calc(100% - 20px);
        padding: 10px;
        border: 1px solid #ffced4;
        border-radius: 4px;
        margin-top: 5px;
    }

    .form-group input:read-only, .form-group textarea:read-only {
        background-color: #f9f9f9;
        color: #666;
    }

    /* Buttons */
    .form-group button {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        background-color: #ff758f;
        color: white;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .form-group button:hover {
        background-color: #ff5e99;
    }

    /* Utility classes */
    .text-center {
        text-align: center;
    }
    .photo-wrapper {
        text-align: center;
        margin-bottom: 20px;
    }
    .photo-image {
        border-radius: 50%;
        width: 100px;
        height: 100px;
        background-color: #ffced4;
        display: inline-block;
        margin-bottom: 10px;
    }

    .change-photo-btn {
        display: block;
        margin: auto;
        margin-bottom: 20px;
        font-size: 0.9em;
    }
#photoImage {
    width: 200px; 
    height: 200px; 
    object-fit: cover; 
    border-radius: 50%; 
}
    .password-fields {
        margin-top: 20px;
    }
    .form-group select {
    width: 100%; 
    padding: 10px;
    border: 1px solid #ffced4;
    border-radius: 4px;
    margin-top: 5px;
    background-color: white; 
}
</style>
<script>var allHobbies = <?php echo json_encode($allHobbies); ?>;</script>
</head>
<body>

<div class="profile-container">
    <h1>User Profile</h1>



<form method="post" action="profile.php" enctype="multipart/form-data" onsubmit="return validatePhoneNumber() && validateHobbiesSelection();">
<div class="photo-wrapper">
    <!-- Display the current avatar as a background image -->
<!--<div class="photo-image" id="photoImage" style="background-image: url('<?php echo $photoData; ?>');"></div>-->
<img id="photoImage" src="<?php echo $photoData; ?>" alt="User Photo"/>
    <!-- Input for new avatar file -->
    <input type="file" id="photoInput" name="photo" hidden onchange="previewPhoto()">
    <label for="photoInput" class="change-photo-btn">Change Avatar</label>
</div>
    <div class="form-group">
        <label for="username">Username:</label>
       <input type="text" id="username" name="username" value="<?php echo isset($user['Username']) ? htmlspecialchars($user['Username']) : ''; ?>" readonly>
    </div>

    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>" readonly>
    </div>

    <div class="form-group">
    <label for="gender">Gender:</label>
    <select id="gender" name="gender" disabled>
        <option value="Male" <?php echo ($user['Gender'] == 'Male' ? 'selected' : ''); ?>>Male</option>
        <option value="Female" <?php echo ($user['Gender'] == 'Female' ? 'selected' : ''); ?>>Female</option>
        <option value="Other" <?php echo ($user['Gender'] == 'Other' ? 'selected' : ''); ?>>Other</option>
    </select>
</div>

    <div class="form-group">
        <label for="nickname">Nickname:</label>
        <input type="text" id="nickname" name="nickname" value="<?php echo htmlspecialchars($user['Nickname']); ?>" readonly>
    </div>

    <div class="form-group">
        <label for="city">City:</label>
        <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($user['City']); ?>" readonly>
    </div>


    <div class="form-group">
        <label for="dob">Date of Birth:</label>
        <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($user['DateOfBirth']); ?>" readonly>
    </div>
    
<div class="form-group">
    <label for="phoneNumber">Phone Number:</label>
    <input type="text" id="phoneNumber" name="phoneNumber" value="<?php echo htmlspecialchars($user['PhoneNumber']); ?>" readonly>
</div>


    <div class="form-group">
        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4" readonly><?php echo htmlspecialchars($user['Description']); ?></textarea>
    </div>


<div class="form-group">
    <label for="hobby">Select Hobbies (up to 3):</label>
    <select id="hobby" name="hobbies[]" multiple size="5" disabled>
        
        <?php foreach ($allHobbies as $hobby) : ?>
            <option value="<?= htmlspecialchars($hobby['HobbyID']); ?>">
                <?= htmlspecialchars($hobby['HobbyName']); ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
<div class="form-group">
    <label>Current Hobbies:</label>
    <ul id="currentHobbies">
        <?php foreach ($userHobbies as $hobby) : ?>
            <li><?= htmlspecialchars($hobby); ?></li>
        <?php endforeach; ?>
    </ul>
</div>


    <div class="form-group text-center">
            <button type="button" onclick="editProfile()">Edit Profile</button>
            <button type="submit" name="updateProfile">Save Changes</button>
            
            <button type="submit" name="logout">Logout</button>
            <a href="MainPage.php" class="button">Back</a>
            
        </div>
</form>

<form method="post" action="profile.php">
    <div class="form-group">
        <label>Current Password</label>
        <input type="password" name="currentPassword" required>
    </div>
    <div class="form-group">
        <label>New Password</label>
        <input type="password" name="newPassword" required>
    </div>
    <div class="form-group">
        <label>Confirm New Password</label>
        <input type="password" name="confirmPassword" required>
    </div>
    <button type="submit" name="changePassword">Change Password</button>
</form>
</div>
<script src="js/hobbies.js"></script>
<script>

    function previewPhoto() {
        
    var input = document.getElementById('photoInput');
    var photoImage = document.getElementById('photoImage');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            photoImage.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
    }

    function validatePhoneNumber() {
    var phoneNumber = document.getElementById('phoneNumber').value;
    var regex = /^\d{10}$/; 
    if (!regex.test(phoneNumber)) {
        alert('Please enter a valid 10-digit phone number.');
        return false;
    }
    return true;
}

var userHobbies = <?php echo json_encode($userHobbies); ?>; 
var allHobbies = <?php echo json_encode($allHobbies); ?>; 


document.addEventListener('DOMContentLoaded', function() {
    var select = document.getElementById('hobby');
    var currentHobbiesList = document.getElementById('currentHobbies');
    currentHobbiesList.innerHTML = ''; 
    allHobbies.forEach(function(hobby) {
        var option = document.createElement('option');
        option.value = hobby.HobbyID;
        option.textContent = hobby.HobbyName;

        
        if (userHobbies.includes(parseInt(hobby.HobbyID))) {
            
            var li = document.createElement('li');
            li.textContent = hobby.HobbyName;
            currentHobbiesList.appendChild(li);
        }

        select.appendChild(option);
    });
    select.att
});

function fillHobbySelection() {
    var select = document.getElementById('hobby');
    select.innerHTML = ''; 
    allHobbies.forEach(function(hobby) {
        var option = document.createElement('option');
        option.value = hobby.HobbyID;
        option.textContent = hobby.HobbyName;
        select.appendChild(option);
    });
}
document.addEventListener('DOMContentLoaded', fillHobbySelection);



   function editProfile() {
   
    var inputs = document.querySelectorAll('input[type="text"], input[type="date"], textarea');
    var selects = document.querySelectorAll('select');
    var select = document.getElementById('hobby');

    select.disabled=false;
    
    inputs.forEach(function(input) {
        input.removeAttribute('readOnly'); 
    });
 document.getElementById('phoneNumber').removeAttribute('readOnly');
    
    selects.forEach(function(select) {
        select.removeAttribute('disabled'); 
        
     document.getElementById('hobbies').style.display = 'none';
    document.getElementById('hobbySelection').style.display = 'block';
    fillHobbySelection();
        
    });   
}


</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var userHobbies = <?php echo json_encode($userHobbies); ?>; 
        var allHobbies = <?php echo json_encode($allHobbies); ?>; 
        var hobbySelect = document.getElementById('hobby');
        for (var i = 0; i < hobbySelect.options.length; i++) {
        if (userHobbies.includes(parseInt(hobbySelect.options[i].value))) {
            hobbySelect.options[i].selected = true;
        }
        }
   
    document.getElementById('hobby').addEventListener('change', function() {
        var selectedOptions = Array.from(this.selectedOptions);
        var currentHobbiesList = document.getElementById('currentHobbies');
        var maxOptions = 3;
        if (selectedOptions.length > maxOptions) {
           
            selectedOptions[selectedOptions.length - 1].selected = false;
            alert('You can only select up to ' + maxOptions + ' hobbies.');
        }else{
                    
        currentHobbiesList.innerHTML = '';
        selectedOptions.forEach(function(option) {
            var li = document.createElement('li');
            li.textContent = option.text;
            currentHobbiesList.appendChild(li);
        });
        }


    });
</script>
</body>
</html>