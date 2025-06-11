<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../images/dentalLogo.png" type="image/x-icon">
    <title>Dental Medical Report</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body{
            font-family: "Reem Kufi";
        }

        h1{
            text-align: center;
            margin-top: 50px;
        }

        th,td{
            border: 2px solid #D0E4E8;
            text-align: center;
            padding: 10px;
            
        }

        table{
            display: table;
            align-item: center;
            border-collapse: collapse; 
            border: 2px solid #D0E4E8; 
            border-radius: 5px; 
            box-sizing: border-box;
            margin: 0 auto;
            width: 60%;

        }
        
        th{
            background-color: black;
            color: white;
        }

        td a{
            text-decoration: none;
        }

        tr:hover{
            background-color: #CBDFF8;
        }
    </style>
</head>
<body>
<?php
    include('../config.php');
    include('Dentist.php');

    // Check if the dentist is logged in
    if (!isset($_SESSION['dentistID'])) {
        echo "<p>You are not logged in. Please log in to view your dental records.</p>";
        exit();
    }

    $dentistID = $_SESSION['dentistID'];

    echo "<h1>Dental Medical Report</h1>";

    $query = "SELECT a.patientID, a.appointmentID, a.appointmentTime , a.serviceName, d.recordID, d.recordDate, d.symptoms, d.history, a.dentistID
              FROM appointment a JOIN dentalrecord d
              ON a.appointmentID = d.appointmentID
              WHERE a.dentistID = '$dentistID' 
              ORDER BY a.patientID ASC";
    $result = mysqli_query($conn, $query) or die(mysqli_connect_error());

    echo '<div class="appointment-table">';
    echo '<table class = "bordered">
            <tr>
            <th>No.</th>
            <th>Patient ID</th>
            <th>Appointment ID</th>
            <th>Appointment Time</th>
            <th>Service Name</th>
            <th>Record ID</th>
            <th>Record Date</th>
            <th>Symptoms</th>
            <th>History</th>
            <th>View</th>
            </tr>';

        $counter = 1;
            while(($row=mysqli_fetch_array($result))){
            echo '<tr>
                    <td>'.$counter.'</td>
                    <td>'.$row['patientID'].'</td>
                    <td>'.$row['appointmentID'].'</td>
                    <td>'.$row['appointmentTime'].'</td>
                    <td>'.$row['serviceName'].'</td>
                    <td>'.$row['recordID'].'</td>
                    <td>'.$row['recordDate'].'</td>
                    <td>'.$row['symptoms'].'</td>
                    <td>'.$row['history'].'</td>
                    <td><a href="dentist_view_medical_report.php?id='.$row['appointmentID'].'"
                    onClick=\'return confirm("Confirm to view full appointment information?")\'>View</a></td>
                    </tr>';
                $counter++;
                }
            echo '</table>';
            echo '</div>';

            mysqli_close($conn);

            echo "<br> <br> <br> <br> <br> <br> <br>";
             echo "<br> <br> <br> <br> <br> <br>";
            include('../footer.php');

    ?>

</body>
</html>