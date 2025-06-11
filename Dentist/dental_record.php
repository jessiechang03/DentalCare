<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../images/dentalLogo.png" type="image/x-icon">
    <title>Dental Record</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body{
            font-family: "Reem Kufi";
            height: 100%;
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

        .search-dental-record-input{
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
    include('Dentist.php');

    // Check if the dentist is logged in
    if (!isset($_SESSION['dentistID'])) {
        echo "<p>You are not logged in. Please log in to view your dental records.</p>";
        exit();
    }

    $dentistID = $_SESSION['dentistID'];

    echo "<h1>Dental Record List</h1>";
?>

<form class="search-dental-record-form" action="dentist_search_dental_record.php" method="get">
    <select name="search_criteria" required>
        <option value="" default>- Select option -</option>
        <option value="recordID">Record ID</option>
        <option value="patientID">Patient ID</option>
    </select>
    <input class="search-dental-record-input" type="text" name="search_value" required> &nbsp &nbsp &nbsp;
    <input class="search-btn" type="submit" name="search" id="search" value="Search"/>
</form>


    <br>
    <?php
       $query = "SELECT r.recordID, r.recordDate, r.symptoms, r.treatment, r.history, r.patientID, r.dentistID
                 FROM dentalrecord r
                 WHERE r.dentistID = '$dentistID'
                 ORDER BY r.recordID, r.recordDate";
       $result = mysqli_query($conn, $query) or die(mysqli_connect_error());

        // adding dental record button
        echo '<div style="display: inline-flex; margin-left: 1100px;">
        <a href="add_dentalrecord.php"><button class="insert-button" style="display: width: 100px; font-size:18px;">Add</button></a>
        <br><br>
        </div>';

        echo '<div class="dental-record-table">';
        echo '<table class = "bordered">
             <tr>
             <th>No.</th>
             <th>Record ID</th>
             <th>Record Date</th>
             <th>Treatment</th>
             <th>View</th>
             <th>Edit</th>
             </tr>';

             $counter = 1;
             while(($row=mysqli_fetch_array($result))){
                echo '<tr>
                     <td>'.$counter.'</td>
                     <td>'.$row['recordID'].'</td>
                     <td>'.$row['recordDate'].'</td>
                     <td>'.$row['treatment'].'</td>
                     <td><a href="dentist_view_dental_record.php?id=' . $row['recordID'] . '">View</a></td>
                     <td><a href="dentist_edit_dental_record.php?id=' . $row['recordID'] . '" style="color:#0000ff">Edit</a></td>
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
