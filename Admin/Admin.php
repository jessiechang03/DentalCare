<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <!-- header container -->
     <div class="header-container">
        <div class="logo-title">
            <a href="Admin_homepage.php"><img src="../images/dentalLogo.png" alt="dentalLogo" id="dentalLogo"></a>
            <p class="title">Dental Appointment Booking System</p>
        </div>
     

     <div class="header-dropdown">
        <button class="dropbtn"><img src="../images/Admin_icon.png" alt="Admin Logo" id="header-logo">
        <p class="header-name">
                <?php
                if (isset($_SESSION['name'])) {
                    echo $_SESSION['name'];
                } else {
                    echo "Admin";
                }
                ?>
        </p>
        </button>
        <div class="dropdown-content">
            <a href="edit_admin_profile.php">Profile</a>
            <a href="../login.html">Log Out</a>
        </div>
      </div>
    </div>

     <!-- Navigation Menu -->
     <nav class="menu-nav">
        <ul>
            <li><a href="Admin_homepage.php">Home</a></li>
            <li><a href="manage_appointment.php">Appointment</a></li>
            <li><a href="manage_user.php">User Management</a></li>
            <li><a href="manage_payment.php">Payment</a></li>
        </ul>
        
     </nav>
</body>
</html>