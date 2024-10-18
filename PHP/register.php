<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            padding-top: 50px;
            font-family: Arial, sans-serif;
            background: rgba(0, 0, 0, 0.5); 
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
        .error-message {
            color: red;
            font-size: 0.8em;
            margin-top: 5px;
        }
        .register-container {
            margin-top: 200px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        .register-container h2 {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 10px;
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            font-size: 13px;
            align-self: flex-start;
        }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="tel"],
        .form-group input[type="password"],
        .form-group input[type="date"],
        .form-group select {
            font-size: 14px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin: 5px 0;
        }
        .form-group input[type="file"] {
            border: none;
            margin-top: 15px;
        }
        .form-group .photo-label {
            display: inline-block;
            padding: 6px 12px;
            cursor: pointer;
            background: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-top: 15px;
        }
        .form-group input[type="file"] {
            display: none;
        }
        .form-group button {
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px;
            margin-top: 20px; 
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #d32f2f;
        }
        .half-input-container {
            display: flex;
            justify-content: space-between;
        }
        .half-input-container .form-group {
            flex: 1;
            max-width: 48%; 
        }
        .password-container {
            display: flex;
            justify-content: space-between;
        }
        .password-container .form-group {
            flex: 1;
        }
        .password-show {
            background: none;
            color: #f44336;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php
        session_start();
        if (isset($_SESSION['error'])) {
            echo '<div class="error-message">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
    ?>
    <div class="register-container">
        <h2>CREATE YOUR ACCOUNT</h2>
        <form action="./register_process.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Nickname*</label>
                <input type="text" name="nickname" required>
                <?php if (isset($_SESSION['error']['nickname'])): ?>
                <div class="error-message"><?php echo htmlspecialchars($_SESSION['error']['nickname']); ?></div>
                <?php unset($_SESSION['error']['nickname']); ?>
                <?php endif; ?>
            </div>

            <div class="half-input-container">
                <div class="form-group">
                    <input type="text" name="firstname" placeholder="First name*" required>
                </div>
                <div class="form-group">
                    <input type="text" name="secondname" placeholder="Second name">
                </div>
            </div>
            <div class="form-group">
                <label>Mobile number*</label>
                <input type="tel" name="PhoneNumber" required pattern="\d{10}" title="Mobile number should be 10 digits">
                <?php if (isset($_SESSION['error']['PhoneNumber'])): ?>
                <div class="error-message"><?php echo htmlspecialchars($_SESSION['error']['PhoneNumber']); ?></div>
                <?php unset($_SESSION['error']['PhoneNumber']); ?>
                <?php endif; ?>
            </div>
    
            <div class="form-group">
                <label>Email address*</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Gender*</label>
                <select name="gender" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label>City*</label>
                <input type="text" name="city" required
            </div>
            <div class="form-group">
                <label>Date of Birth*</label>
                <input type="date" name="dob" required max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>">
            </div>
            <div class="form-group">
    <label>Description</label>
    <textarea name="description" rows="4"></textarea>
</div>

<div class="form-group">
    <label>Registration Date</label>
    <input type="text" name="registration_date" value="<?php echo date('Y-m-d H:i:s'); ?>" disabled>
</div>

<div class="form-group">
    <label>Other Profile Info</label>
    <textarea name="other_profile_info" rows="4"></textarea>
</div>

<div class="password-container">
    <div class="form-group">
        <input type="password" name="password" placeholder="Password*" required>
    </div>
    <button type="button" class="password-show">Show</button>
</div>

<div class="password-container">
    <div class="form-group">
        <input type="password" name="confirmpassword" placeholder="Confirm password*" required>
        <div class="error-message" id="password-error"></div>
    </div>
    <button type="button" class="password-show">Show</button>
</div>

<label for="photo-upload" class="photo-label">Photo</label>
<input type="file" name="photo" id="photo-upload" required>
<div class="error-message" id="photo-error"></div>

<div class="form-group">
    <button type="submit">Register</button>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const passwordInput = document.querySelector('input[name="password"]');
    const confirmPasswordInput = document.querySelector('input[name="confirmpassword"]');
    const passwordError = document.getElementById('password-error');
    const dobInput = document.querySelector('input[name="dob"]');
    const dobError = document.createElement('div');
    dobError.className = 'error-message';
    dobInput.parentNode.insertBefore(dobError, dobInput.nextSibling);
    const photoUploadInput = document.getElementById('photo-upload');
    const photoError = document.getElementById('photo-error');

    function checkAge() {
        const userDOB = new Date(dobInput.value);
        const today = new Date();
        const cutoffDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());
        if (userDOB > cutoffDate) {
            dobError.textContent = 'You must be at least 18 years old to register.';
        } else {
            dobError.textContent = '';
        }
    }

    dobInput.addEventListener('change', checkAge);

    form.addEventListener('submit', function(event) {
        let formValid = true;
        if (passwordInput.value !== confirmPasswordInput.value) {
            passwordError.textContent = 'Passwords do not match, please ensure both passwords are identical.';
            formValid = false;
        } else {
            passwordError.textContent = '';  
        }

        if (!photoUploadInput.files.length) {
            photoError.textContent = 'Please select a photo to upload.';
            formValid = false;
        } else {
            photoError.textContent = '';
        }

        if (!formValid) {
            event.preventDefault(); // Prevent form submission
        }
    });

    // Toggle password visibility
    document.querySelectorAll('.password-show').forEach(button => {
        button.addEventListener('click', function() {
            let input = this.previousElementSibling.querySelector('input');
            if (input.type === 'password') {
                input.type = 'text';
                this.textContent = 'Hide';
            } else {
                input.type = 'password';
                this.textContent = 'Show';
            }
        });
    });
});
</script>
</body>
</html>