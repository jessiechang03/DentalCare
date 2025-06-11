<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../images/dentalLogo.png" type="image/x-icon">
    <title>Manage Appointment</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body{
            font-family: "Reem Kufi";
        }
        h1{
            text-align: center;
            margin-top: 50px;
        }
        form{
            align: center;
            display: flex;
            justify-content: center;
        }

        .search-appointment-input{
            border-radius: 5px;
            padding: 10px;
            width: 20%;
        }

        .insert-button,
        .search-btn{
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

        .insert-button:hover,
        .search-btn:hover{
            color: black;
            background-image: linear-gradient(135deg, #b2eff1 40%, #a58ef8 100%);
            box-shadow: 0 0 15px 5px rgba(255,255,255,0.75);
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
    include('Admin.php');
    echo "<h1>Appointment List</h1>";


?>

<form class="search-appointment-form" action="search_appointment.php" method="post">
    <select name="search_criteria" required>
        <option value="" default>- Select option -</option>
        <option value="dentistID">Dentist ID</option>
        <option value="patientID">Patient ID</option>
        <option value="appointmentID">Appointment ID</option>
    </select>
    <input class="search-appointment-input" type="text" name="search_value" required> &nbsp &nbsp &nbsp;
    <input class="search-btn" type="submit" name="search" id="search" value="Search"/>
</form>


    <?php
       $query = "SELECT a.patientID, a.appointmentID, a.appointmentTime, a.serviceName, a.dentistID
                 FROM appointment a
                 ORDER BY a.appointmentID, a.appointmentTime";
       $result = mysqli_query($conn, $query) or die(mysqli_connect_error());

       // Variable to track the row number
       $no = 1;

        // adding user button
        echo '<div style="display: inline-flex; margin-left: 1100px;">
        <a href="add_appointment.php"><button class="insert-button" style="display: width: 100px; font-size:18px;">Add</button></a>
        <br><br>
        </div>';

        echo '<div class="appointment-table">';
        echo '<table class = "bordered">
             <tr>
             <th>No</th>
             <th>Appointment Time</th>
             <th>Service Name</th>
             <th>Edit</th>
             <th>View</th>
             </tr>';

             while(($row=mysqli_fetch_array($result))){
                echo '<tr>
                     <td>'.$no.'</td>
                     <td>'.$row['appointmentTime'].'</td>
                     <td>'.$row['serviceName'].'</td>
                     <td><a href="edit_appointment.php?id=' . $row['appointmentID'] . '">Edit</a></td>
                     <td><a href="view_appointment.php?id=' . $row['appointmentID'] . '">View</a></td>
                     </tr>';

                     $no++;
                    }
             echo '</table>';
             echo '</div>';

             mysqli_close($conn);

             echo "<br> <br> <br>";

             include('../footer.php');

    ?>

</body>
</html>