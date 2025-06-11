<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient</title>
    <link rel="shortcut icon" href="../images/dentalLogo.png" type="image/x-icon">
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Signika+Negative:wght@300..700&display=swap" rel="stylesheet">
</head>

<body>
<?php
        include('Patient.php');
    ?>    


    <div class="background">
        <img src="../images/background_patient.jpg" alt="Patient Background" class="background-image" style="width: 100%">
        <div class="homepage-caption">
            <p class="caption1">We Provide The Best Dental Care</p>
            <p class="caption2">"YOUR ONE STOP DENTAL HEALTH SOLUTION"</p>

        </div>
     </div>

     <form action="search_user.php" method="post">
        <div class="link">
            <b>
                <a href="make_appointment.php" class = "homepage-button">Make Appointment</a>
                <a href="view_appointment.php" class = "homepage-button" >View Appointment</a>
                <a href="dentalrecord_history.php" class = "homepage-button">Dental Record</a>
                <a href="payment_history.php" class = "homepage-button">Payment History</a>

            </b>
        </div>

     </form>

     <br><br>
     <?php include '../footer.php'; ?>
</body>
</html>