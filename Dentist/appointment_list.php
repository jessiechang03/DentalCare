<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../images/dentalLogo.png" type="image/x-icon">
    <title>Appointment List</title>
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

        #update-button,
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

        #update-button:hover,
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
    include('Dentist.php');

    // Check if the dentist is logged in

    if (!isset($_SESSION['dentistID'])) {
        echo "<p>You are not logged in. Please log in to view your dental records.</p>";
        exit();
    }

    $dentistID = $_SESSION['dentistID'];

    echo "<h1>Appointment List</h1>";
?>

<form class="search-appointment-form" action="dentist_search_appointment.php" method="get">
    <select name="search_criteria" required>
        <option value="" default>- Select option -</option>
        <option value="appointmentID">Appointment ID</option>
        <option value="patientID">Patient ID</option>
    </select>
    <input class="search-appointment-input" type="text" name="search_value" required> &nbsp; &nbsp; &nbsp;
    <input class="search-btn" type="submit" name="search" id="search" value="Search"/>
</form>

<br>
<?php
   $query = "SELECT patientID, appointmentID, appointmentTime, serviceName, appointmentStatus
             FROM appointment a
             WHERE dentistID = '$dentistID'
             ORDER BY appointmentID";
   $result = mysqli_query($conn, $query) or die(mysqli_connect_error());

    echo '<div class="appointment-table">';
    echo '<table class = "bordered">
         <tr>
         <th>No.</th>
         <th>Appointment ID</th>
         <th>Appointment Time</th>
         <th>Service Name</th>
         <th>Appointment Status</th>
         <th>Edit Status</th>
         <th>View</th>
         </tr>';
         $counter = 1;
         while(($row=mysqli_fetch_array($result))){
            $statusColor = '';
            if ($row['appointmentStatus'] == 'Rejected') {
                $statusColor = 'red';
            } else if($row['appointmentStatus'] == 'Pending'){
                $statusColor = '#FFDF00';
            }
              else if ($row['appointmentStatus'] == 'Approved') {
               $statusColor = 'green';
            }
            echo '<tr>
                 <td>'.$counter.'</td>
                 <td>'.$row['appointmentID'].'</td>
                 <td>'.$row['appointmentTime'].'</td>
                 <td>'.$row['serviceName'].'</td>
                 <td><span style="color:' . $statusColor . ';">' . $row['appointmentStatus'] . '</span></td>
                 <td>';
                 if ($row['appointmentStatus'] == 'Pending') {
                    echo '<form action="update_status.php" method="post">
                             <input type="hidden" name="appointmentID" value="' . $row['appointmentID'] . '">
                             <select name="appointmentStatus" required>
                                 <option value="" disabled selected>--- Select Status ---</option>
                                 <option value="Approved" ' . ($row['appointmentStatus'] == 'Approved' ? 'selected' : '') . '>Approved</option>
                                 <option value="Rejected" ' . ($row['appointmentStatus'] == 'Rejected' ? 'selected' : '') . '>Rejected</option>
                             </select>
                             <input type="submit" id="update-button" value="Update">
                         </form>';
                } else {
                    echo 'N/A';
                }
                echo '</td>
                     <td><a href="dentist_view_appointment.php?id=' . $row['appointmentID'] . '">View</a></td>
                     </tr>';
                $counter++;
             }
             echo '</table>';
             echo '</div>';
    
             mysqli_close($conn);    

         echo "<br> <br> <br> <br> <br> <br>";
         echo "<br> <br> <br> <br> <br> <br>";

         include('../footer.php');
?>

</body>
</html>