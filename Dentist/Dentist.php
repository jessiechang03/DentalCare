<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dentist</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <!-- header container -->
     <div class="header-container">
        <div class="logo-title">
            <a href="Dentist_homepage.php"><img src="../images/dentalLogo.png" alt="dentalLogo" id="dentalLogo"></a>
            <p class="title">Dental Appointment Booking System</p>
        </div>
     
     <div class="header-dropdown">
        <button class="dropbtn"><img src="../images/Dentist_icon.png" alt="Dentist Logo" id="header-logo">
        <p class="header-name">
                <?php
                if (isset($_SESSION['name'])) {
                    echo $_SESSION['name'];
                } else {
                    echo "Dentist";
                }
                ?>
        </p>
        </button>
        <div class="dropdown-content">
            <a href="edit_dentist_profile.php">Profile</a>
            <a href="../login.html">Log Out</a>
        </div>
      </div>
    </div>

     <!-- Navigation Menu -->
     <nav class="menu-nav">
        <ul>
            <li><a href="Dentist_homepage.php">Home</a></li>
            <li><a href="appointment_list.php">Appointment List</a></li>
            <li><a href="dental_record.php">Dental Record</a></li>
            <li><a href="medical_report.php">Report</a></li>
        </ul>
        
     </nav>
</body>
</html>