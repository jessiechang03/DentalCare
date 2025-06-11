<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/dentalLogo.png" type="image/x-icon">
    <title>Register Patient</title>
    <style>
        body {
            font-family: "Reem Kufi";
        }
        h1 {
            text-align: center;
            margin-top: 30px;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
            border: 2px solid #ccc;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            box-sizing: border-box;
            margin: 0 auto;
        }
        .form-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-bottom: 20px;
        }
        .form-row label {
            width: 30%;
            margin-right: 10px;
            text-align: left;
        }
        .form-row input,
        .form-row select {
            width: 60%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        .go-back-btn,
        .submit-button {
            font-size: 15px;
            width: 100%;
            color: white;
            background-color: black;
            border: 1px solid white;
            padding: 15px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 20px;
        }
        .go-back-btn:hover,
        .submit-button:hover {
            color: black;
            background-image: linear-gradient(135deg, #b2eff1 40%, #a58ef8 100%);
            box-shadow: 0 0 15px 5px rgba(255,255,255,0.75);
        }
        .error-message {
            color: red;
            text-align: center;
        }
        .success-message {
            color: green;
            text-align: center;
        }
    </style>
</head>
<body>
<?php
include('config.php');

echo '<h1>Dental Appointment Booking System</h1>';

if(isset($_POST['submitted'])){
    $errors = array();

    // List all required fields
    $required_fields = array('username', 'password', 'email', 'gender', 'name', 'contactNo', 'address', 'dateOfBirth');

    foreach($required_fields as $field) {
        if(!isset($_POST[$field]) || empty($_POST[$field])) {
            $errors[] = "Please enter $field";
        }
    }

    if(empty($errors)) {
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        $email = $_POST['email'];
        $gender = $_POST['gender'];
        $name = $_POST['name'];
        $contactNo = $_POST['contactNo'];
        $address = $_POST['address'];
        $dateOfBirth = $_POST['dateOfBirth'];
        $role = 3;  // Patient role

        // Generate unique ID for the user
        $userID = generateUniqueID('U', 'user', 'userID');

        // Insert into User table
        $query = "INSERT INTO User (userID, name, gender, contactNo, email) 
                  VALUES (?, ?, ?, ?, ?)";

        if($stmt = $conn->prepare($query)){
            $stmt->bind_param('sssss', $userID, $name, $gender, $contactNo, $email);
            $stmt->execute();
            $stmt->close();

            // Insert into Login  
            $query2 = "INSERT INTO Login (username, password, userlevel, fk_userid) 
                       VALUES (?, ?, ?, ?)";
            if($stmt2 = $conn->prepare($query2)){
                $stmt2->bind_param('ssis', $username, $password, $role, $userID);
                $stmt2->execute();
                $stmt2->close();

                // Generate unique ID for the patient
                $patientID = generateUniqueID('P', 'patient', 'patientID');
                $query3 = "INSERT INTO patient (userID, patientID, dateOfBirth, address) VALUES (?, ?, ?, ?)";
                if($stmt3 = $conn->prepare($query3)){
                    $stmt3->bind_param('ssss', $userID, $patientID, $dateOfBirth, $address);
                    $stmt3->execute();
                    $stmt3->close();
                }

                echo '<div class="success-message">Patient added successfully!</div>';
            } else {
                echo '<div class="error-message">Error preparing the query.</div>';
            }
        } else {
            echo '<div class="error-message">Error preparing the query.</div>';
        }
    } else {
        echo '<div class="error-message">The following errors occurred:<br>';
        foreach($errors as $error) {
            echo "- $error<br>";
        }
        echo '</div>';
    }
}

echo '<div style="display: inline-flex; margin-left: 1100px;">
        <a href="login.html">
        <button class="go-back-btn" style="width: 100px; font-size:15px;">Go Back</button>
        </a>
        <br><br>
      </div>';

function generateUniqueID($prefix, $table, $column) {
    global $conn;
    $query = "SELECT MAX(SUBSTRING($column, 2)) as maxID FROM $table";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $maxID = $row['maxID'];
    if ($maxID === NULL) {
        return $prefix . '001';
    } else {
        $nextID = (int)$maxID + 1;
        return $prefix . str_pad($nextID, 3, '0', STR_PAD_LEFT);
    }
}
?>

    <form method="post" action="register_user.php">
        <input type="hidden" name="submitted" value="TRUE" />

        <!-- Name Input -->
        <div class="form-row">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" class="input-field" size="30" maxlength="40">
        </div>

        <!-- Username Input -->
        <div class="form-row">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" class="input-field" size="30" maxlength="40">
        </div>

        <!-- Password Input -->
        <div class="form-row">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" class="input-field" size="20" maxlength="30">
        </div>

        <!-- Confirm Password Input -->
        <div class="form-row">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" class="input-field" size="20" maxlength="30">
        </div>

        <!-- Gender Input -->
        <div class="form-row">
            <label for="gender">Gender:</label>
            <select id="gender" name="gender" class="input-field">
                <option value="" selected="selected">-- Select Gender --</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>

        <!-- Contact Number Input -->
        <div class="form-row">
            <label for="contactNo">Contact Number:</label>
            <input type="text" id="contactNo" name="contactNo" class="input-field" size="20" maxlength="20">
        </div>

        <!-- Email Input -->
        <div class="form-row">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="input-field" size="40">
        </div>

        <!-- Address Input -->
        <div class="form-row">
            <label for="address">Address:</label>
            <input type="text" id="address" name="address" class="input-field" size="40">
        </div>

        <!-- Date of Birth Input -->
        <div class="form-row">
            <label for="dateOfBirth">Date of Birth:</label>
            <input type="date" id="dateOfBirth" name="dateOfBirth" class="input-field" size="40">
        </div>

        <input class="submit-button" type="submit" value="Sign Up">
    </form>
    <br><br><br><br><br>

</body>
</html>
<script>
// Wait for the DOM content to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Get references to the password and confirm_password elements
    const password = document.getElementById('password');
    const confirm_password = document.getElementById('confirm_password');

    // Define a function to validate if passwords match
    function validatePassword() {
        // Compare the values of password and confirm_password inputs
        if (password.value !== confirm_password.value) {
            // If passwords don't match, set a custom validity message
            confirm_password.setCustomValidity("Password do not match, please try again.");
        } else {
            // If passwords match, clear any custom validity message
            confirm_password.setCustomValidity('');
        }
    }

    // Add event listeners to both password and confirm_password inputs
    password.addEventListener('input', validatePassword);
    confirm_password.addEventListener('input', validatePassword);
});
</script>