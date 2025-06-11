<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../images/dentalLogo.png" type="image/x-icon">
    <title>Dentist</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php
        include('Dentist.php');
    ?>

    <!-- Background -->
     <div class="background">
        <img src="../images/background_dentist.jpg"  alt="Dentist Background" class="background-image" style="width: 100%">
        <div class="homepage-caption">
            <p class="caption1">Best Dental Appointment Booking System</p>
            <p class="caption2">"Preferred by Dental Professionals Everywhere"</p>
        </div>
     </div>

     <form action="search_user.php" method="post">
        <div class="link">
            <b>
                <a href="appointment_list.php" class = "homepage-button"> Search Appointment List</a>
                <a href="dental_record.php" class = "homepage-button">Search Dental Record</a>
                <a href="medical_report.php" class = "homepage-button" >Report</a>
                
            </b>
        </div>
     </form>

     <br><br>
     <?php include '../footer.php'; ?>
</body>
</html>