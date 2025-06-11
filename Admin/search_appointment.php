<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../images/dentalLogo.png" type="image/x-icon">
    <title>Search Appointment</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body{
            font-family: "Reem Kufi";
        }
        h1{
            text-align: center;
            margin-top: 50px;
        }

        th, td{
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

        .insert-button{
            font-size: 15px;
            width: 100px;
            color: white;
            background-color: black;
            border: 1px solid white;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .insert-button:hover{
            color: black;
            background-image: linear-gradient(135deg, #b2eff1 40%, #a58ef8 100%);
            box-shadow: 0 0 15px 5px rgba(255,255,255,0.75);
        }
    </style>
</head>
<body>
<?php
include('../config.php');
include('Admin.php');

echo "<h1>Search Appointment Result</h1>";

if(isset($_POST['search'])) {
    $search_criteria = $_POST['search_criteria'];
    $search_value = $_POST['search_value'];

    $query = "SELECT * FROM appointment WHERE $search_criteria='$search_value'";
    $result = mysqli_query($conn, $query) or die(mysqli_connect_error());

    if(mysqli_num_rows($result) > 0) {
        echo '<div class="appointment-table">';
        echo '<table class="bordered">
                <tr>
                <th>Patient ID</th>
                <th>Appointment ID</th>
                <th>Appointment Time</th>
                <th>Service Name</th>
                <th>Dentist ID</th>
                <th>Edit</th>
                <th>View</th>
                </tr>';

        while(($row = mysqli_fetch_array($result))) {
            echo '<tr>
                  <td>' . $row['patientID'] . '</td>
                  <td>' . $row['appointmentID'] . '</td>
                  <td>' . $row['appointmentTime'] . '</td>
                  <td>' . $row['serviceName'] . '</td>
                  <td>' . $row['dentistID'] . '</td>
                  <td><a href="edit_appointment.php?id=' . $row['appointmentID'] . '">Edit</a></td>
                  <td><a href="view_appointment.php?id=' . $row['appointmentID'] . '">View</a></td>
                  </tr>';
        }

        echo '</table>';
        echo '</div>';
        echo '<br><br><br><br>';
    } else {
        echo '<p style="text-align: center; margin-top: 20px;">No results found for the given criteria.</p>';
    }

    echo '<div style="display: flex; justify-content:center; align-items: center;">';
    echo '<button class="insert-button" onclick="history.back()">Go Back</button>';
    echo '</div>';
}

mysqli_close($conn);

echo "<br><br><br>";
include('../footer.php');
?>
</body>
</html>
