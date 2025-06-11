<?php
include('../config.php');
include('Patient.php');

$userID = $_SESSION['userid'];
$name = "";
$dateOfBirth = "";
$address = "";
$email = "";
$username = "";
$password = "";

$patientQuery = "SELECT p.*, u.name, u.email, l.username, l.password 
                 FROM Patient p
                 JOIN User u ON p.userID = u.userID
                 JOIN Login l ON u.userID = l.fk_userid
                 WHERE p.userID = '$userID'";
$patientResult = mysqli_query($conn, $patientQuery);
$row_user = mysqli_fetch_assoc($patientResult);

if ($row_user) {
    $name = $row_user['name'];
    $username = $row_user['username'];
    $password = $row_user['password'];
    $dateOfBirth = $row_user['dateOfBirth'];
    $address = $row_user['address'];
    $email = $row_user['email'];
} else {
    die("Error: Patient profile not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dateOfBirth = $_POST['dateOfBirth'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $updateQuery = "UPDATE User u
                    JOIN Patient p ON u.userID = p.userID
                    JOIN Login l ON u.userID = l.fk_userid
                    SET p.dateOfBirth = '$dateOfBirth', p.address = '$address', u.email = '$email', l.password = '$password'
                    WHERE u.userID = '$userID'";

    if (mysqli_query($conn, $updateQuery)) {
        echo '<script>alert("Profile updated successfully!");</script>';
        echo '<script>window.history.back();</script>';
        exit;
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Profile</title>
    <link rel="shortcut icon" href="../images/dentalLogo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Reem Kufi";
            background:
                linear-gradient(rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.8)),
                url('../images/background_patient.jpg'); 
            background-size: cover;
            background-position: center;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto; 
            background: #fff;
            box-shadow: 0px 8px 24px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center; 
            padding: 20px;
        }

        .profile-header {
            background-color: #9DB8ED ;
            color: white;
            padding: 20px;
            text-align: center;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .profile-header img {
            width: 80px; 
            height: 80px;
            margin-bottom: 2px;
        }

        .profile-header h1 {
            margin: 10px 0 0;
            font-size: 24px;
        }

        .profile-content {
            padding: 20px;
            border-top: 5px solid #9DB8ED;
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
        }

        .profile-content .profile-info {
            margin-bottom: 10px;
        }

        .profile-content .profile-info .label {
            font-weight: 500;
            color: #333;
        }

        .profile-content .profile-info .value {
            font-weight: 300;
        }

        .edit-profile {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
        }

        .edit-profile button {
            background-color: #88e4f9;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .edit-profile button:hover {
            background-color: #45a049;
        }

        .edit-profile button:disabled {
            background-color: #88e4f9;
            cursor: not-allowed;
        }

        .edit-profile button:not(:disabled):hover {
            background-color: #45a049;
        }

        .edit-form {
            margin-top: 20px;
            width: 100%; 
        }

        .edit-form input,
        .edit-form textarea {
            width: calc(100% - 16px); 
            padding: 8px;
            margin: 5px 0;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        .edit-form button {
            background-color: #88e4f9;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        .edit-form button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="profile-header">
            <img src="../images/Patient_icon.png" alt="Patient Logo">
            <h1>Profile Information</h1>
        </div>
        <div class="profile-content">
            <div class="profile-info">
                <div class="label">Name</div>
                <div class="value"><?= $name ?></div>
            </div>
            <div class="profile-info">
                <div class="label">Username</div>
                <div class="value"><?= $username ?></div>
            </div>
            <div class="profile-info">
                <div class="label">Date of Birth</div>
                <div class="value"><?= $dateOfBirth ?></div>
            </div>
            <div class="profile-info">
                <div class="label">Address</div>
                <div class="value"><?= $address ?></div>
            </div>
            <div class="profile-info">
                <div class="label">Email</div>
                <div class="value"><?= $email ?></div>
            </div>
        </div>
        <div class="edit-profile">
            <button id="editButton" onclick="toggleEdit()">Edit Profile</button>
        </div>
        <div class="edit-form" id="editForm">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="profile-info">
                    <div class="label">Name</div>
                    <input type="text" name="name" value="<?= $name ?>" readonly>
                </div>
                <div class="profile-info">
                    <div class="label">Username</div>
                    <input type="text" name="username" value="<?= $username ?>" readonly>
                </div>
                <div class="profile-info">
                    <div class="label">Password</div>
                    <input type="password" name="password" value="<?= $password ?>" required>
                </div>
                <div class="profile-info">
                    <div class="label">Date of Birth</div>
                    <input type="date" name="dateOfBirth" value="<?= $dateOfBirth ?>" readonly>
                </div>
                <div class="profile-info">
                    <div class="label">Address</div>
                    <textarea name="address" rows="4" required><?= $address ?></textarea>
                </div>
                <div class="profile-info">
                    <div class="label">Email</div>
                    <input type="email" name="email" value="<?= $email ?>" required>
                </div>
                <div class="edit-profile">
                    <button type="submit">Save Changes</button>&nbsp;
                    <button type="button" onclick="toggleEdit()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <br>
    <br>
    <br>

    <?php include '../footer.php'; ?>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var editForm = document.getElementById("editForm");
            var editButton = document.getElementById("editButton");

            editForm.style.display = "none"; 

            editButton.addEventListener("click", function () {
                editForm.style.display = "block";
                editButton.style.display = "none";
            });
        });

        function toggleEdit() {
            var editForm = document.getElementById("editForm");
            var editButton = document.getElementById("editButton");

            if (editForm.style.display === "none") {
                editForm.style.display = "block";
                editButton.style.display = "none";
            } else {
                editForm.style.display = "none";
                editButton.style.display = "block";
            }
        }
    </script>
</body>

</html>
