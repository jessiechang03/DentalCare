
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../images/dentalLogo.png" type="image/x-icon">
    <title>Edit User</title>
    
    <style>
        body {
            font-family: "Reem Kufi";
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        h1 {
            text-align: center;
            margin-top: 30px;
        }
        form {
            max-width: 500px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px 5px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .go-back-btn,
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            color: white;
            background-color: black;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        .go-back-btn:hover,
        input[type="submit"]:hover {
            color: black;
            background-image: linear-gradient(135deg, #b2eff1 40%, #a58ef8 100%);
            box-shadow: 0 0 15px 5px rgba(255,255,255,0.75);
        }
        p{
            text-align: center;
        }
    </style>
    
</head>
<body>
    <?php
    include "../config.php";
    include "Admin.php";
    
    echo "<h1>Edit User Information</h1>";

    // Check if user ID is provided in the URL
    if(isset($_GET['id'])){
        // Get the user ID from the URL parameter
        $id = $_GET['id'];
    } else if((isset($_POST['id']))){
        $id = $_POST['id'];
    } else{
        echo '<p>This page has been accessed in error.</p>';
        include('../footer.php');
        exit();
    }

    if (isset($_POST['submitted'])) {
        $errors = array();

        if (empty($_POST['password'])) {
            $errors[] = 'You forgot to enter a password.';
        } else {
            $password = mysqli_real_escape_string($conn, trim($_POST['password']));
            $password = md5($password);
        }

        if (empty($_POST['email'])) {
            $errors[] = 'You forgot to enter a email.';
        } else {
            $email = mysqli_real_escape_string($conn, trim($_POST['email']));
        }

        if (empty($_POST['contactNo'])) {
            $errors[] = 'You forgot to enter a contact number.';
        } else {
            $contactNo = mysqli_real_escape_string($conn, trim($_POST['contactNo']));
        }



        if (empty($errors)) {
            $q = "UPDATE user SET email='$email', contactNo='$contactNo' where userID='$id'";
            $q2 = "UPDATE login SET password='$password' where fk_userid='$id'";
            $r = @mysqli_query($conn, $q);
            $r2 = @mysqli_query($conn, $q2);

            if (($r && $r2)) {
                echo '<p>The user record has been edited.</p>';
            } else {
                echo '<p class="error">The user could not be edited due to a system error. </p>';
                echo '<p>' . mysqli_error($conn) . '<br />Query: ' . $q . '</p>';
            }
        } else {
            echo '<p class="error">The following error(s) occurred:<br />';
            foreach ($errors as $msg) {
                echo " - $msg<br />\n";
            }
            echo '</p><p>Please try again.</p>';
        }
    }

    $q1 = "SELECT * FROM user WHERE userID='$id'";
    $q2 = "SELECT * FROM login WHERE fk_userid='$id'";
    

    if ($r = mysqli_query($conn, $q1)){
        $r2 = mysqli_query($conn, $q2);
        if (mysqli_num_rows($r) == 1){

            $row = mysqli_fetch_array($r);
            $row2 = mysqli_fetch_array($r2);
    
            echo '<div style="display: inline-flex; margin-left: 1100px;">
            <a href="manage_user.php">
            <button class="go-back-btn" style="width: 100px; font-size:15px;">Go Back</button>
            </a>
            <br><br>
            </div>';

            echo '<div class = "outer-user-table">';
            echo '<form action="edit_user.php" method="post">

            <label for="userID">User ID:</label>
            <input type="text" id="userID" name="userID" value=" '. $row['userID'] . '" disabled><br><br>

            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="' . $row['name'] . '" disabled><br><br>

            <label for="username">Username:</label>';
            echo '<input type="text" id="username" name="username" value="' . $row2['username'] . '" disabled><br><br>';
            

            echo '<label for="password">Password:</label>';
            
            if ($_SESSION['userid'] == $id) {
                echo '<input type="password" id="password" name="password" value="' . $row2['password'] . '"><br><br>';
            } else {
                echo '<input type="password" id="password" name="password" value="' . $row2['password'] . '" disabled><br><br>';
            }

            echo '<label for="gender">Gender:</label>
            <input type="text" id="gender" name="gender"  value="' . $row['gender'] . '" disabled><br><br>

            <label for="contactNo">Contact Number:</label>
            <input type="text" id="contactNo" name="contactNo" value="' . $row['contactNo'] . '"><br><br>

            <label for="email">Email:</label>
            <input type="text" id="email" name="email" value="' . $row['email'] . '"><br><br>';

            
            echo '<label class="button-profile">
	        	<input class="button-profile-update" type="submit" name="submit" value="Update" id="updateButton"/>
				<input type="hidden" name="submitted" value="TRUE" />
				<input type="hidden" name="id" value="' . $id . '" />
			</label>';
    
            echo '</form></div><br><br><br><br>';

    } else {
        echo '<p class="error">This page has been accessed in error.</p>';
    }
    mysqli_close($conn); 
     
}

include '../footer.php';  

?>

</body>
</html>