<?
session_start();
?>


<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LifeLove</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css bootstrap.min.css" integrity="sha384
  giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
  <link rel="stylesheet" href="model.css">
  

  <style>
    .waterfall-container {
      column-count: 4;
      /* Set number of columns */
      column-gap: 0rem;
      /* Sets the spacing between columns */

    }

    .waterfall-item {
      display: block;
      break-inside: avoid;

      margin-right: 1rem;

      margin-bottom: 1rem;

      background-color: #f9f9f9;

      border-radius: 8px;

      padding: 1rem;

    }

    .page-padding {
      padding: 0 20%;

    }

    .waterfall-item img {
      width: 100%;

      height: auto;

      max-width: 250px;

    }

    .input-center {
      display: flex;
      justify-content: center;
      align-items: center;
      padding-top: 2rem;
    }

    input[type="text"] {
      width: 50%;

      height: 50px;

      font-size: 20px;


    }

    .topnav {
      overflow: hidden;
      background-color: #AA3939;

      padding-left: 20%;

    }

    .topnav a {
      float: left;
      display: block;
      color: #f2f2f2;

      text-align: center;
      padding: 14px 16px;
      text-decoration: none;
    }

    .topnav a:hover {
      background-color: #ddd;

      color: black;

    }

    .topnav a.active {
      background-color: #4CAF50;

      color: white;

    }

    .user-profile {
      display: flex;

      align-items: center;

      justify-content: flex-end;
    }

    .user-profile img {
      width: 40px;

      height: 40px;

      border-radius: 50%;

      margin-left: 10px;

      margin-right: 10px;
    }

    .info {
      font-size: 0.9rem;
    }

    .filter-container {
      margin-bottom: 20px;
    }

    .age-slider {
      display: none;
      margin-top: 10px;
    }

    .age-values {
      display: flex;
      justify-content: space-between;
    }


    /* #close-btn {
      position: absolute;
      top: 0;
      right: 0;
      margin-top: 1rem;
      margin-right: 1rem;
    } */


    .overlay {
      display: block;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);

      z-index: 10;
      /* Make sure it is covered under the modal frame */
    }
    
.report-form-container {
  position: relative; 
  background-color: #ffffff;
  border: 1px solid #eaeaea;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  padding: 1rem;
  margin: 2rem auto;
  width: auto;
  max-width: 800px;
}

.report-form-container h3 {
  text-align: center; 
  color: #333;
}

.form-group label {
  display: block; 
  margin-bottom: 0.5rem;
}

.form-group input, 
.form-group select, 
.form-group textarea {
  width: 100%; 
  border: 1px solid #ced4da; 
  border-radius: 4px; 
  padding: 0.75rem;
}

.form-group button {
  width: 100%;
  padding: 0.5rem; 
  border-radius: 4px; 
  color: #fff;
  background-color: #007bff; 
  border-color: #007bff;
  margin-top: 1rem; 
}

.form-group button:hover {
  background-color: #0069d9;
  border-color: #0062cc; 
}
    .user-profile span {
        color: #FEFAFA; 
    }
#close-report-form {
  border: none;
  background: transparent;
  color: #aaa;
  font-size: 24px;
  cursor: pointer;
}
#close-report-form:hover {
  color: #f00;
}
  </style>
</head>
<?php 



function InitializeData(&$users, &$users_image,&$users_hobbies,&$hobby_relation) {
    // Database connection information
    $servername = "sql101.infinityfree.com"; // Database host name
    $username = "if0_36150369"; // Database user name
    $password = "zhangIFDB159876"; // Database password
    $dbname = "if0_36150369_Group5"; // database name

    // Create a database connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check whether the connection is successful
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }
    
    // Query user data
    $sql = "SELECT * FROM Users";
    $result = $conn->query($sql);
    $Imagesql = "SELECT * FROM UserPhotos";
    $image_result = $conn->query($Imagesql);

    $Hobbysql = "SELECT * FROM HobbyList";
    $hobby_result = $conn->query($Hobbysql);

    $relationsql = "SELECT * FROM UserHobbies";
    $relation_result = $conn->query($relationsql);
    // initialize array
    $users = array();
    $users_image = array();
    $users_hobbies = array();
    $hobby_relation=array();
    // Check whether there is data
    if ($result->num_rows > 0) {
        // Get user data
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        // Calculate the user age and add to the array
        foreach ($users as &$user) {
            $date_of_birth = new DateTime($user['DateOfBirth']);
            $today = new DateTime(date("Y-m-d"));
            $user['Age'] = $date_of_birth->diff($today)->y;
        }
    } else {
        echo "0 结果";
    }

    // Get user picture data
    if ($image_result->num_rows > 0) {
        while ($row = $image_result->fetch_assoc()) {
            $row['image'] = base64_encode($row['image']);
            $users_image[] = $row;
        }
    } else {
        echo "0 结果";
    }
    // Acquire Hobbies data
    if ($hobby_result->num_rows > 0) {
        while ($row = $hobby_result->fetch_assoc()) {
            $users_hobbies[] = $row;
        }
    } else {
        echo "0 结果";
    }
    if ($relation_result->num_rows > 0) {
        while ($row = $relation_result->fetch_assoc()) {
            $hobby_relation[] = $row;
        }
    } else {
        echo "0 结果";
    }

    $loggedInUserId = $_SESSION['id'];

// Gets the current logged-in user's hobbies
$loggedInUserHobbies = [];
$relationSql = "SELECT * FROM UserHobbies WHERE UserID = $loggedInUserId";
$relationResult = $conn->query($relationSql);
if ($relationResult->num_rows > 0) {
    while ($row = $relationResult->fetch_assoc()) {
        $loggedInUserHobbies[] = $row['HobbyID']; // Get only the hobby ID
    }
}

    // Close database connection
    $conn->close();
    echo "<script>var loggedInUserHobbies = " . json_encode($loggedInUserHobbies) . ";</script>";
}
$users = array();
$users_image = array();
$users_hobbies = array();
$hobby_relation=array();
InitializeData($users,$users_image,$users_hobbies,$hobby_relation);
?>

<body id="body">


  <div class="topnav">
    <div>
      <a class="active" href="#search">Search</a>
      <a href="displayUsername.php">Message</a>
       <a href="#report-form" id="report-link">Report</a> 
    </div>

    
    <div class="user-profile">
        <span><?php echo $_SESSION["username"];?></span>
        
        
      <!-- <img src="https://via.placeholder.com/50" id='user_avatar'alt="Avatar"> -->
      <?php
    // Check if the user's ID is set in the session
    if (isset($_SESSION['id'])) {
      $userID = $_SESSION['id'];
      // Search for the user's avatar image URL based on their ID
      $avatar_flag=0;
      foreach ($users_image as $image) {
          if ($image['UserID'] == $userID) {
              // If found, display the user's avatar image wrapped inside an <a> tag
              echo '<a href="profilepage.php?user_id=' . $userID . '"><img src="data:image/jpeg;base64,' . $image['image'] . '" id="user_avatar" alt="Avatar"></a>';
              $avatar_flag=1;
              break;
          }

      }
                if($avatar_flag==0){
              echo '<a href="profilepage.php?user_id=' . $userID . '"><img src="https://via.placeholder.com/50" id="user_avatar" alt="Avatar"></a>';
          }
  }
    ?>

    </div>
  </div>
  
<?php

if (isset($_SESSION['report_success_message'])) {
    echo "<div class='alert alert-success text-center'>" . $_SESSION['report_success_message'] . "</div>";
    // Reset session variables so that messages are displayed only once
    unset($_SESSION['report_success_message']);
    // Use JavaScript for automatic redirection
    echo "<script>setTimeout(() => window.location.href = 'MainPage.php', 3000);</script>";
}
?>

  <div class="input-center">
    <input type="text" id="search_hobby" placeholder="Search hobby" autocomplete="off">
    <div id="suggestion-box" style="display: none; position: absolute; background-color: white; border: 1px solid #ddd;">
        
    </div>
  </div>
  <div class="filter-container">
    <button id="filter-btn">Filter</button>
        <div id="selected-hobbies" style="display: flex; flex-wrap: wrap; margin-left: 10px;">
        
    </div>
    <div class="age-slider" id="age-slider" style="display: none;">
      <p>Max-age: </p>
      <input type="range" min="18" max="100" value="50" id="myRange" oninput="change()" onchange="change()">
      <span id='max-age'>50</span>
      <script>
        function change() {
          var value = document.getElementById('myRange').value;
          document.getElementById('max-age').innerHTML = value;
        }
      </script>
      <div class="age-values">
        <!-- <span id="min-age">0</span> -->
        <!-- <span id="max-age">100</span> -->

      </div>
      <br>
          <select id="gender-select">
        <option value="Any">Any</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
        <option value="Other">Other</option>
    </select>
    <br>
        <button id="reset-filter-btn">Reset Filter</button>
      <button id="filter-active-btn">Start Filter</button>

    </div>
  </div>
  <hr>


 <div class="report-form-container" id="report-form" style="display: none; padding: 1rem; margin-top: 2rem;">
  <h3>Report a User</h3>
  <button id="close-report-form" style="position: absolute; top: 10px; right: 10px; border: none; background: none; cursor: pointer; font-size: 20px;">&times;</button> <!-- 添加关闭按钮 -->
    <form action="report_processor.php" method="post">
        <div class="form-group">
            <label for="reportedEmail">User Email:</label>
            <input type="email" id="reportedEmail" name="reportedEmail" required>
        </div>
        <div class="form-group">
            <label for="contentType">Content Type:</label>
            <select id="contentType" name="contentType" required>
                <option value="Message">Message</option>
                <option value="Image">Image</option>
                
                <!-- Add additional content types as needed -->
            </select>
        </div>
        <div class="form-group">
            <label for="reportReason">Reason:</label>
            <textarea id="reportReason" name="reportReason" required></textarea>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-danger">Submit Report</button>
        </div>
    </form>
</div>

  <div class="container page-padding">
    <div class="row">

      <div class="col">
        <div class="waterfall-container" id="waterfall-container">

          <!-- Use php to dynamically generate water waterfall items -->
          <?php foreach ($users as $user) : ?>
            <div class="waterfall-item" id="<?php echo $user['UserID']; ?>">
              <div>
                <?php foreach ($users_image as $row) :
                  $flag = 0;
                  if ($row['UserID'] === $user['UserID']) {
                    $base64image = $row['image'];
                    echo "<img src='data:image/jpeg;base64," . $base64image . "'>";
                    $flag = 1;
                    break;
                  }
                ?>
                  <!--<img src="https://via.placeholder.com/300x200"> -->
                <?php endforeach;
                if ($flag == 0)
                  echo "<img src='https://via.placeholder.com/300'>";
                ?>
              </div>
              <div class="info">
                <p>Name: <?php echo $user['Nickname']; ?></p>

                <p>Age: <?php echo $user['Age']; ?></p>
                <p>Location: <?php echo $user['City']; ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>



        <script>
          // Get filter buttons and age sliders
          const filterBtn = document.getElementById('filter-btn');
          const ageSlider = document.getElementById('age-slider');
          // Get the filter button and age slider container

          const minAgeSpan = document.getElementById('min-age');
          const maxAgeSpan = document.getElementById('max-age');
          // Switch the age slider to show/hide when you click the button
          filterBtn.addEventListener('click', () => {
            if (ageSlider.style.display == 'none') {
              ageSlider.style.display = 'block';
            } else {
              ageSlider.style.display = 'none';
            }
          });

        //   $('#age-slider').slider({
        //     tooltip: 'always' 
        //   });


          // $('#age-slider').on('slide', function(slideEvt) {
          //   minAgeSpan.textContent = slideEvt.value[0];
          //   maxAgeSpan.textContent = slideEvt.value[1];
          // });
        </script>

        <!-- Repeat the following water waterfall entries to create the layout
        <div class="waterfall-container">
           
          <div class="waterfall-item">
            <img src="https://via.placeholder.com/300" class="img-fluid" alt="Placeholder Image">
          </div>
          -->

        <!-- End water waterfall item-->
      </div>
    </div>
  </div>

  <script>

 document.addEventListener('DOMContentLoaded', function() {
  // Get all water waterfall items
  const waterfallContainer = document.getElementById('waterfall-container');
  const waterfallItems = Array.from(document.querySelectorAll('.waterfall-item'));

  // Create an array to store cascade entries and their priorities
  let itemsWithPriority = waterfallItems.map(item => {
    const userId = item.id;
    const userHobbies = u_hobbies.filter(hobby => hobby.UserID === userId);
    const hasCommonHobbies = userHobbies.some(hobby => loggedInUserHobbies.includes(hobby.HobbyID));
    return { item, hasCommonHobbies };
  });

  // Sort by whether they have common interests, and the users who have common interests come first
  itemsWithPriority.sort((a, b) => b.hasCommonHobbies - a.hasCommonHobbies);

  // Empty the waterfall stream container and re-add the sorted items
  waterfallContainer.innerHTML = '';
  itemsWithPriority.forEach(obj => {
    // If you have a common interest, add a green border
    if (obj.hasCommonHobbies) {
      obj.item.style.border = "2px solid green";
    }
    waterfallContainer.appendChild(obj.item);
  });
});


    let users = <?php echo json_encode($users); ?>;
    let u_image = <?php echo json_encode($users_image); ?>;
    let hobby_list=<?php echo json_encode($users_hobbies); ?>;
    let u_hobbies=<?php echo json_encode($hobby_relation); ?>;
    // Add click event listeners to each waterfall-item
    document.addEventListener('DOMContentLoaded', function() {
      const waterfallItems = document.querySelectorAll('.waterfall-item');
      waterfallItems.forEach(item => {
        item.addEventListener('click', () => {

          const modal = document.createElement('div');
          modal.classList.add('modal-box');
          const modal_top = document.createElement('div');
          modal_top.classList.add('modal-box-top');
          const modal_content = document.createElement('div');
          //add modal content(include user info)
          modal_content.classList.add('modal-box-content');
          // Gets the user's data user id
          var dataUserId = item.getAttribute('id');
          // Finds the user information corresponding to the data user id
          var img_flag = 0;
          u_image.forEach(image => {
            if (parseInt(image.UserID) == parseInt(dataUserId)) {
              const user_img = document.createElement('img');
              user_img.src = "data:image/jpeg;base64," + image.image;
              modal_content.appendChild(user_img);
              img_flag = 1;
              return;
            }
          })
          if (img_flag == 0) {
            const user_img = document.createElement('img');
            user_img.src = 'https://via.placeholder.com/300';
            modal_content.appendChild(user_img);
          }
          users.forEach(user => {
            if (parseInt(user.UserID) == parseInt(dataUserId)) {

              const user_email = document.createElement('p');
              user_email.textContent = 'Email: ' + user.Email;
              modal_content.appendChild(user_email);

              //nickname
              const user_nickname = document.createElement('p');
              user_nickname.textContent = 'Nickname: ' + user.Nickname;
              modal_content.appendChild(user_nickname);
              //gender
              const user_gender = document.createElement('p');
              user_gender.textContent = 'Gender: ' + user.Gender;
              modal_content.appendChild(user_gender);
              //age
              let dateOfBirth = new Date(user.DateOfBirth);
              let today = new Date();
              let age = user.Age;
              const user_age = document.createElement('p');
              user_age.textContent = 'Age: ' + age;
              modal_content.appendChild(user_age);
              //city
              const user_city = document.createElement('p');
              user_city.textContent = 'City: ' + user.City;
              modal_content.appendChild(user_city);
              //description
              const user_description = document.createElement('p');
              user_description.textContent = 'Description: ' + user.Description;
              modal_content.appendChild(user_description);
              //otherInfo
              const user_otherinfo = document.createElement('p');
              user_otherinfo.textContent = 'OtherInfo: ' + user.OtherProfileInfo;
              modal_content.appendChild(user_otherinfo);
            }
          })
          // Gets the user's UserID
        const userId = item.id;

        // Example Query the hobby name of a user
        const userHobbies = u_hobbies.filter(hobby => hobby.UserID == userId).map(uh => {
            // For each hobby ID, find the corresponding hobby name
            const hobbyDetail = hobby_list.find(hl => hl.HobbyID == uh.HobbyID);
            return hobbyDetail ? hobbyDetail.HobbyName : "";
        });
        //add text desription about hobbies
        const user_hobbies = document.createElement('p');
        user_hobbies.textContent="Hobbies:"
        modal_content.appendChild(user_hobbies);
        // Create a hobby list element
        const hobbiesList = document.createElement('ul');
        userHobbies.forEach(hobbyName => {
            const hobbyItem = document.createElement('li');
            hobbyItem.textContent = hobbyName;
            hobbiesList.appendChild(hobbyItem);
        });

        // Add hobby list to modal box content
        modal_content.appendChild(hobbiesList);

          var talkBtn = document.createElement("button");
           talkBtn.textContent = "Talk";
           talkBtn.style.marginRight = "10px";
           talkBtn.onclick = function() {
               window.location.href = `talk_page.php?current_user=${encodeURIComponent('<?php echo $_SESSION["username"]; ?>')}&talk_to_user_id=${dataUserId}`;
           };
          modal_top.appendChild(talkBtn); 

          var closeBtn = document.createElement("button");
          closeBtn.textContent = "Close";
          closeBtn.setAttribute("id", "close-btn");

          var overlay = document.createElement('div');
          overlay.classList.add('overlay');

          modal_top.appendChild(closeBtn);

          modal.appendChild(modal_top);
          modal.appendChild(modal_content);
          const body = document.getElementById('body');
          body.appendChild(overlay);
          body.appendChild(modal);
          closeBtn.addEventListener("click", function() {
            if (body.contains(modal)) {
              body.removeChild(modal);
            }
            if (body.contains(overlay)) {
              body.removeChild(overlay);
            }
          });



        });
      });



      let selectedHobbies = [];
      document.getElementById('search_hobby').addEventListener('input', function() {
    let inputText = this.value.toLowerCase();
    let suggestionBox = document.getElementById('suggestion-box');
    suggestionBox.innerHTML = ''; // Clear out existing proposals
suggestionBox.style.left = document.getElementById('search_hobby').offsetLeft + 'px';
suggestionBox.style.top = (document.getElementById('search_hobby').offsetTop + document.getElementById('search_hobby').offsetHeight) + 'px';
suggestionBox.style.width = document.getElementById('search_hobby').offsetWidth + 'px';
    if (inputText.length > 0) {
        // Filter matching hobbies
        let matchedHobbies = hobby_list.filter(hobby => hobby.HobbyName.toLowerCase().includes(inputText));

        // Create and display a list of suggestions
        matchedHobbies.forEach(hobby => {
            let option = document.createElement('div');
            option.textContent = hobby.HobbyName;
            option.style.padding = '10px';
            option.style.cursor = 'pointer';
            option.addEventListener('click', function() {
                onSuggestionClicked({ HobbyID: hobby.HobbyID, HobbyName: hobby.HobbyName });
            });
            suggestionBox.appendChild(option);
        });

        suggestionBox.style.display = matchedHobbies.length > 0 ? 'block' : 'none';
    } else {
        suggestionBox.style.display = 'none';
    }
});
// Add a new hobby to the list of selected hobbies
function addHobbyToSelected(hobby) {
    // Check whether the hobby has been added, based on HobbyID
    if (selectedHobbies.some(h => h.HobbyID === hobby.HobbyID)) {
        return; // If it is already added, it is not added again
    }
    selectedHobbies.push(hobby); // Add to array

    let selectedHobbiesContainer = document.getElementById('selected-hobbies');
    let hobbyTag = document.createElement('div');
    hobbyTag.textContent = hobby.HobbyName;
    hobbyTag.setAttribute('data-hobby-id', hobby.HobbyID); // Store hobby ID
    hobbyTag.style.margin = '5px';
    hobbyTag.style.padding = '5px';
    hobbyTag.style.border = '1px solid #ddd';
    hobbyTag.style.borderRadius = '5px';
    hobbyTag.style.background = '#efefef';

    // Create delete button
    let deleteBtn = document.createElement('span');
    deleteBtn.textContent = ' ×';
    deleteBtn.style.marginLeft = '10px';
    deleteBtn.style.color = 'red';
    deleteBtn.style.cursor = 'pointer';
    deleteBtn.onclick = function() {
        hobbyTag.remove(); // Removes the label from the DOM
        // Remove hobbies from the array according to HobbyID
        selectedHobbies = selectedHobbies.filter(h => h.HobbyID !== hobby.HobbyID); 
    };

    hobbyTag.appendChild(deleteBtn);
    selectedHobbiesContainer.appendChild(hobbyTag); // Add to the container of the selected hobby
}

function userHasSelectedHobbies(userID, selectedHobbies, usersHobbies) {
  // Return true if no hobbies are selected (because there is no need to filter through hobbies)
  if (selectedHobbies.length === 0) {
    return true;
  }
  // Check if the user has any of the selected hobbies
  return usersHobbies.some(userHobby => 
    userHobby.UserID === userID && selectedHobbies.some(hobby => hobby.HobbyID === userHobby.HobbyID)
  );
}
// This function is called when a suggestion is clicked
function onSuggestionClicked(hobby) {
    document.getElementById('search_hobby').value = hobby.HobbyName;
    addHobbyToSelected(hobby); // Add to selected hobbies
    document.getElementById('suggestion-box').style.display = 'none';
}
      let filter_btn = document.getElementById('filter-active-btn');
      //const waterfallItems = document.querySelectorAll('.waterfall-item');
      filter_btn.addEventListener("click", function() {
        let value = document.getElementById('myRange').value;
        //let search_text = document.getElementById('search_text').value;
        let selectedGender = document.getElementById('gender-select').value.toLowerCase(); // Get the selected gender

  waterfallItems.forEach(item => {
    let btn_UserId = parseInt(item.getAttribute('id'));

    // Use the userHasSelectedHobbies function here to filter users
    let user = users.find(user => parseInt(user.UserID) === btn_UserId);
    if (user) {
      //let userNicknameLower = user.Nickname.toLowerCase();
      let genderMatch = (selectedGender === 'any' || user.Gender.toLowerCase() === selectedGender);
      let ageMatch = parseInt(user.Age) <= parseInt(value);
      //let nameMatch = search_text === '' || userNicknameLower.includes(search_text);
      let hobbyMatch = userHasSelectedHobbies(user.UserID, selectedHobbies, u_hobbies);

      // Make sure all conditions are met
      if (ageMatch && genderMatch && hobbyMatch  ) {
        item.style.display = 'block';
      } else {
        item.style.display = 'none';
      }
    } else {
      item.style.display = 'none';
    }
  })
      })
    });

var resetFilterBtn = document.getElementById('reset-filter-btn');
// var ageSlider = document.getElementById('myRange');
 var genderSelect = document.getElementById('gender-select');

// Add a click event listener to the reset button
resetFilterBtn.addEventListener('click', function() {

    document.getElementById('myRange').value = 50;
    document.getElementById('max-age').innerHTML = document.getElementById('myRange').value;
    //ageSlider.input();

    genderSelect.value = "Any";



    // After the reset is complete, you can select the click event that triggers the filter button to refresh the results
    document.getElementById('filter-active-btn').click();


    // document.querySelectorAll('.waterfall-item').forEach(item => {
    //     item.style.display = 'block';
    // });
});

document.addEventListener('DOMContentLoaded', function() {
  const reportLink = document.getElementById('report-link');
  const reportForm = document.getElementById('report-form');
  const closeBtn = document.getElementById('close-report-form');

  reportLink.addEventListener('click', function(event) {
    event.preventDefault();
    reportForm.style.display = 'block'; // Show report form
  });

  closeBtn.addEventListener('click', function() {
    reportForm.style.display = 'none'; // Hide the report form when you click the cross
  });
});
  </script>

</body>
</html>