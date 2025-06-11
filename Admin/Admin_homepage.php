<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../images/dentalLogo.png" type="image/x-icon">
    <title>Admin</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php
        include('Admin.php');
    ?>
    
    <!-- Background -->
     <div class="background">
        <img src="../images/background_admin.jpg"  alt="Admin Background" class="background-image" style="width: 100%">
        <div class="homepage-caption">
            <p class="caption1">Best Dental Appointment Booking System</p>
            <p class="caption2">"Preferred by Dental Professionals Everywhere"</p>
        </div>
     </div>

     <form action="search_user.php" method="post">
        <div class="link">
            <b>
                <a href="manage_appointment.php" class = "homepage-button">Manage Appointment</a>
                <a href="manage_user.php" class = "homepage-button" >Manage User</a>
                <a href="manage_payment.php" class = "homepage-button">Manage Payment</a>
                
            </b>
        </div>

     </form>

     <br><br>
     <?php include '../footer.php'; ?>
</body>
</html>