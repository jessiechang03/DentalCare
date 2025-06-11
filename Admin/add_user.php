<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../images/dentalLogo.png" type="image/x-icon">
    <title>Add User</title>
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
        /* hide the extra details initially */
        .extra-details {
            display: none;
        }

    </style>
    <script>
        function toggleExtraFields() {
            const role = document.getElementById('role').value;
            document.getElementById('adminFields').style.display = 'none';
            document.getElementById('dentistFields').style.display = 'none';
            document.getElementById('patientFields').style.display = 'none';

            if (role == '1') {
                document.getElementById('adminFields').style.display = 'block';
            } else if (role == '2') {
                document.getElementById('dentistFields').style.display = 'block';
            } else if (role == '3') {
                document.getElementById('patientFields').style.display = 'block';
            }
        }
    </script>
</head>
<body>
<?php
include('../config.php');
include('Admin.php');

echo '<h1>Add User</h1>';

if(isset($_POST['submitted'])){
    $errors = array();

    // List all required fields
    $required_fields = array('username', 'password', 'email', 'gender', 'name', 'contactNo', 'userlevel');
    if($_POST['userlevel'] == '3'){
        array_push($required_fields, 'address', 'dateOfBirth');
    } elseif ($_POST['userlevel'] == '2') {
        array_push($required_fields, 'dentist_qualification', 'dentist_specialization');
    } elseif ($_POST['userlevel'] == '1') {
        array_push($required_fields, 'admin_qualification');
    }

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
        $role = $_POST['userlevel'];
        $dateOfBirth = $_POST['dateOfBirth'];
        $address = $_POST['address'];
        $dentist_specialization = $_POST['dentist_specialization'];
        $admin_qualification = $_POST['admin_qualification'];
        $dentist_qualification = $_POST['dentist_qualification'];
        

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

                if($role == 1){
                    // Admin
                     // Generate unique ID for the admin
                    $adminID = generateUniqueID('R', 'admin', 'adminID');
                    $query3 = "INSERT INTO admin (userID, adminID, qualification) VALUES (?, ?, ?)";
                    if($stmt3 = $conn->prepare($query3)){
                        $stmt3->bind_param('sss',$userID, $adminID, $admin_qualification);
                        $stmt3->execute();
                        $stmt3->close();
                    }
                }
                elseif ($role == 2) {
                    // Dentist 
                    // Generate unique ID for the dentist
                    $dentistID = generateUniqueID('D', 'dentist', 'dentistID');
                    $query3 = "INSERT INTO dentist (userID, dentistID, qualification, specialization) VALUES (?, ?, ?, ?)";
                    if($stmt3 = $conn->prepare($query3)){
                        $stmt3->bind_param('ssss', $userID, $dentistID, $dentist_qualification, $dentist_specialization);
                        $stmt3->execute();
                        $stmt3->close();
                    }
                } elseif ($role == 3) {
                    // Patient 
                    // Generate unique ID for the patient
                    $patientID = generateUniqueID('P', 'patient', 'patientID');
                    $query3 = "INSERT INTO patient (userID, patientID, dateOfBirth, address) VALUES (?, ?, ?, ?)";
                    if($stmt3 = $conn->prepare($query3)){
                        $stmt3->bind_param('ssss', $userID, $patientID, $dateOfBirth, $address);
                        $stmt3->execute();
                        $stmt3->close();
                    }
                }

                echo '<div class="success-message">User added successfully!</div>';
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
        <a href="manage_user.php">
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

    <form method="post" action="add_user.php">
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

        <!-- Select User Role -->
        <div class="form-row">
            <label for="role">User Role:</label>
            <select name="userlevel" id="role" class="input-field" onchange="toggleExtraFields()">
                <option value="" selected="selected">-- Select Role --</option>
                <option value="1">Admin</option>
                <option value="2">Dentist</option>
                <option value="3">Patient</option>
            </select>
        </div>

        <!-- Extra user details -->
        <!-- Extra Fields for Admin -->
        <div id="adminFields" class="form-row extra-details">
            <div class="form-row">
                <label for="admin_qualification">Qualification:</label>
                <select name="admin_qualification" id="admin_qualification" class="input-field">
                    <option value="" selected="selected">-- Select Qualification</option>
                    <option value="Reception Management">Reception Management</option>
                    <option value="Office Administration">Office Administration</option>
                </select>
            </div>
        </div>

        <!-- Extra Fields for Dentist -->
        <div id="dentistFields" class="form-row extra-details">
            <div class="form-row">
                <label for="dentist_qualification">Qualification:</label>
                <select name="dentist_qualification" id="dentist_qualification" class="input-field">
                    <option value="" selected="selected">-- Select Qualification</option>
                    <option value="DDS">DDS</option>
                    <option value="DMD">DMD</option>
                    <option value="BDS">BDS</option>
                </select>
            </div>
            <div class="form-row">
                <label for="dentist_specialization">Specialization:</label>
                <select name="dentist_specialization" id="dentist_specialization" class="input-field">
                    <option value="" selected="selected">-- Select Specialization -- </option>
                    <option value="Orthodontics">Orthodontics</option>
                    <option value="Endodontics">Endodontics</option>
                    <option value="General Dentistry">General Dentistry</option>
                </select>
            </div>
        </div>

        <!-- Extra Fields for Patient -->
        <div id="patientFields" class="form-row extra-details">
            <div class="form-row">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" class="input-field" size="40">
            </div>
            <div class="form-row">
                <label for="dateOfBirth">Date of Birth:</label>
                <input type="date" id="dateOfBirth" name="dateOfBirth" class="input-field" size="40">
            </div>
        </div>

    <input class="submit-button" type="submit" value="Submit">
</form>
<br><br><br><br><br>

</body>
</html>
<?php include '../footer.php' ?>
