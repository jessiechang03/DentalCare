<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../images/dentalLogo.png" type="image/x-icon">
    <title>Search Dental Record</title>
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
include('Dentist.php');

echo "<h1>Search Dental Record Result</h1>";

if(isset($_GET['search'])) {
    $search_criteria = $_GET['search_criteria'];
    $search_value = $_GET['search_value'];

    $query = "SELECT * FROM dentalrecord WHERE $search_criteria='$search_value'";
    $result = mysqli_query($conn, $query) or die(mysqli_connect_error());

    $no = mysqli_num_rows($result);
    if( $no > 0) {
        echo '<div class="dental-record-table">';
        echo '<table class="bordered">
                <tr>
                <th>No.</th>
                <th>Record ID</th>
                <th>Record Date</th>
                <th>Treatment</th>
                <th>Patient ID</th>
                <th>View</th>
                </tr>';

        $counter = 1;
        while(($row = mysqli_fetch_array($result))) {
            echo '<tr>
                <td>'.$counter.'</td>
                <td>'.$row['recordID'].'</td>
                <td>'.$row['recordDate'].'</td>
                <td>'.$row['treatment'].'</td>
                <td>'.$row['patientID'].'</td>
                <td><a href="dentist_view_dental_record.php?id=' . $row['recordID'] . '">View</a></td>
                </tr>';
        $counter++;
        }

        echo '</table>';
        echo '</div>';
        echo '<br>';
        echo '<p style="text-align: center;">Total of '.$no.' record found</p>';
        echo '<br><br><br>';
    } else {
        echo '<p style="text-align: center; margin-top: 20px;">No results found for the given criteria.</p>';
    }
    echo '<div style="display: flex; justify-content:center; align-items: center;">';
    echo '<button class="insert-button" onclick="history.back()">Go Back</button>';
    echo '</div>';
}

mysqli_close($conn);

echo "<br> <br> <br> <br> <br> <br>";
echo "<br> <br> <br> <br> <br> <br>";
include('../footer.php');
?>
</body>
</html>
