<!DOCTYPE html>
<html>

<head>
	<title>Search User Profile</title>
	<link rel="shortcut icon" href="../images/transparent-logo.png" type="image/x-icon">
    <link rel="stylesheet" href="../style.css">
    <style>


        .outer-user-table {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .user-table {
            background-color: #D8F3F4;
            border: 2px solid #ccc;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            box-sizing: border-box;
        }

        .profile {
            font-weight: bold;
        }

        label {
            display: block;
            margin-bottom: 10px;
            
        }

        button.insert-button {
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

        button.insert-button:hover {
            color: black;
            background-image: linear-gradient(135deg, #b2eff1 40%, #a58ef8 100%);
            box-shadow: 0 0 15px 5px rgba(255, 255, 255, 0.75);
        }

    </style>
</head>

<body>
<?php
include('Admin.php');
include "../config.php";

if(isset($_POST['name'])){
    $name=$_POST['name'];
    $sql = "SELECT * FROM user WHERE name ='$name'";
   if($r = mysqli_query($conn, $sql)){
    if (mysqli_num_rows($r) == 1) { 
        $row=mysqli_fetch_array($r);
        $id=$row['userID'];
        $sql2 = "SELECT * FROM login WHERE fk_userid ='$id'";
        $r2 = mysqli_query($conn, $sql2);
        $row2=mysqli_fetch_array($r2);

        echo '<h1 style="text-align: center; margin-top: 50px;">User Information</h1>';
        
        echo '<div class="outer-user-table">
        <div class="user-table">

        <label><span class="profile"> Full Name: </span>'. $row['name'].'</label><br>
        
        <label><span class="profile"> Gender: </span>'. $row['gender'].'</label><br>
        
        <label><span class="profile"> Contact Number: </span>'. $row['contactNo'].'</label><br>
        
        <label><span class="profile"> Email: </span>'. $row['email'].'</label><br>';
        
        // Check user role to display additional information
        if($row2['userlevel'] == 1){
            echo "<label><span class='profile'> Role: Admin</span></label><br>";
            // Display admin-specific information
            $q3 = "SELECT * FROM admin WHERE userID = '$id'";
            $r3 = mysqli_query($conn, $q3);
            $admin_info = mysqli_fetch_assoc($r3);
            echo "<label><span class='profile'> Admin ID: </span>". $admin_info['adminID']."</label><br>";
            echo "<label><span class='profile'> Qualification: </span>". $admin_info['qualification']."</label><br>";
        } else if ($row2['userlevel'] == 2){
            echo "<label><span class='profile'> Role: Dentist</span></label><br>";
            // Display dentist-specific information
            $q3 = "SELECT * FROM dentist WHERE userID = '$id'";
            $r3 = mysqli_query($conn, $q3);
            $dentist_info = mysqli_fetch_assoc($r3);
            echo "<label><span class='profile'> Dentist ID: </span>". $dentist_info['dentistID']."</label><br>";
            echo "<label><span class='profile'> Qualification: </span>". $dentist_info['qualification']."</label><br>";
            echo "<label><span class='profile'> Specialization: </span>". $dentist_info['specialization']."</label><br>";
        } else if ($row2['userlevel'] == 3){
            echo "<label><span class='profile'> Role: Patient</span></label><br>";
            // Display patient-specific information
            $q3 = "SELECT * FROM patient WHERE userID = '$id'";
            $r3 = mysqli_query($conn, $q3);
            $patient_info = mysqli_fetch_assoc($r3);
            echo "<label><span class='profile'> Patient ID: </span>". $patient_info['patientID']."</label><br>";
            echo "<label><span class='profile'> Date of Birth: </span>". $patient_info['dateOfBirth']."</label><br>";
            echo "<label><span class='profile'> Address: </span>". $patient_info['address']."</label><br>";
        }

        echo '<div style="display: flex; justify-content:center; align-items: center;">';
        echo '<button class="insert-button" style="align:" onclick="history.back()">Go Back</button>';
        echo '<br>';
        echo '</div>';

        echo '</div> </div>';
    }else{
        header("Location: manage_user.php");
    }
}
}echo '<br><br><br><br>';
mysqli_close($conn);
include ('../footer.php');
?>

</body>

</html>
