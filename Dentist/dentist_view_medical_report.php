<!DOCTYPE html>
<html>

<head>
	<title>View Dental Medical Report</title>
	<link rel="shortcut icon" href="../images/transparent-logo.png" type="image/x-icon">
	<link rel="stylesheet" href="../style.css">
    <style>
        body {
            font-family: "Reem Kufi";
        }

        h1 {
            text-align: center;
            margin-top: 30px;
        }

        .outer-appointment-table {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .appointment-table {
            background-color: #E2E1FF;
            border: 2px solid #ccc;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            box-sizing: border-box;
        }

        .profile {
            font-weight: bold;
        }

        label {
            display: block;
            margin-bottom: 10px;
            
        }

        button.insert-button {
            font-size: 15px;
            width: 100%;
            color: white;
            background-color: black;
            border: 1px solid white;
            padding: 15px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 20px;
            margin-left: 5px;
            margin-right: 5px;
        }

        button.insert-button:hover {
            color: black;
            background-image: linear-gradient(135deg, #b2eff1 40%, #a58ef8 100%);
            box-shadow: 0 0 15px 5px rgba(255, 255, 255, 0.75);
        }

    </style>
</head>

<body>
<?php
include "../config.php";
include('Dentist.php');

if((isset($_GET['id']))){
    $id=$_GET['id'];
} else if ((isset($_POST['id']))){
    $id=$_POST['id'];
}else{
    echo '<p>This page has been accessed in error.</p>';
    include('../footer.php');
    exit();
}

$q1 = "SELECT a.patientID, a.dentistID, a.appointmentID, a.appointmentTime , a.dentalServiceType, a.serviceName, d.recordID, d.recordDate, d.symptoms, d.history
        FROM appointment a JOIN dentalrecord d
        USING (appointmentID)
        WHERE appointmentID='$id'";

if($r=mysqli_query($conn,$q1)){
    if(mysqli_num_rows($r)==1){
        $row=mysqli_fetch_array($r);

        echo '<h1 style="text-align: center; margin-top: 50px;">Dental Medical Report</h1>';
        
        echo '<div class="outer-appointment-table">
        <div class="appointment-table">

        <label><span class="profile"> Patient ID: </span>'. $row['patientID'].'</label><br>

        <label><span class="profile"> Dentist ID :  </span>'. $row['dentistID'].'</label><br>

        <label><span class="profile"> Appointment ID: </span>'. $row['appointmentID'].'</label><br>
        
        <label><span class="profile"> Appointment Time: </span>'. $row['appointmentTime'].'</label><br>
        
        <label><span class="profile"> Dental Service Type: </span>'. $row['dentalServiceType'].'</label><br>
        
        <label><span class="profile"> Service Name: </span>'. $row['serviceName'].'</label><br>

        <label><span class="profile"> Record ID: </span>'. $row['recordID'].'</label><br>

        <label><span class="profile"> Record Date: </span>'. $row['recordDate'].'</label><br>
        
        <label><span class="profile"> Symptoms </span>'. $row['symptoms'].'</label><br>

        <label><span class="profile"> History :  </span>'. $row['history'].'</label><br>';

        echo '<div style="display: flex; justify-content:center; align-items: center;">';
        
        echo '<button class="insert-button" style="align:" onclick="window.print()">Print</button>';
        echo '<button class="insert-button" style="align:" onclick="history.back()">Go Back</button>';
        echo '<br>';
        echo '</div>';

        echo '</div> </div>';
    }echo '<br><br><br><br>';
}

include ('../footer.php');
?>

</body>

</html>
