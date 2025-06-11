<?php
include('../config.php');
session_start();

if (isset($_GET['id'])) {
    $userID = $_GET['id'];

    // Fetch user details
    $user_query = "SELECT u.name, l.status FROM user u JOIN login l ON u.userID = l.fk_userid WHERE u.userID = '$userID'";
    $user_result = mysqli_query($conn, $user_query);
    $user_data = mysqli_fetch_assoc($user_result);

    if ($user_data) {
        $userName = $user_data['name'];
        $status = $user_data['status'];
    }

    // Check if the confirmation was given
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        // Update the user status to 'Inactive'
        $query = "UPDATE Login SET status='Inactive' WHERE fk_userid=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $userID);

        if ($stmt->execute()) {
            echo "<script>alert('User account deactivated successfully!'); window.location.href = 'manage_user.php';</script>";
        } else {
            echo "<script>alert('Error deactivating user account.'); window.location.href = 'manage_user.php';</script>";
        }

        $stmt->close();
        $conn->close();
    }
} else {
    // If no user ID is provided in the URL, redirect back to the manage_user.php page
    header("Location: manage_user.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../images/dentalLogo.png" type="image/x-icon">
    <link rel="stylesheet" href="../style.css">
    <title>Confirm Deactivation</title>
    <style>
        body {
            font-family: "Reem Kufi";
            text-align: center;
            margin-top: 50px;
        }
        .confirm-box {
            display: inline-block;
            background-color: #D8F3F4;
            padding: 20px;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
        }
        .confirm-box button {
            font-size: 15px;
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .confirm-box .yes-btn,
        .confirm-box .no-btn {
            background-color: black;
            color: white;
        }

        .confirm-box .yes-btn:hover{
            background-color: #28a745;
        }
        .confirm-box .no-btn:hover{
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="confirm-box">
        <h2>Are you sure you want to deactivate the account for <?php echo htmlspecialchars($userName); ?>?</h2>
        <form method="post" action="">
            <input type="hidden" name="confirm" value="yes">
            <button type="submit" class="yes-btn">Yes</button>
            <button type="button" class="no-btn" onclick="window.location.href='manage_user.php'">No</button>
        </form>
    </div>
</body>
</html>
