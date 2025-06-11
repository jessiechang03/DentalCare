<?php
// Include the database configuration file
include "../config.php";

// Check if appointment ID is provided in the URL or POST
if (isset($_GET['id'])) {
    $appointmentID = $_GET['id'];
} else if (isset($_POST['id'])) {
    $appointmentID = $_POST['id'];
} else {
    echo '<script>alert("This page has been accessed in error.");</script>';
    include('../footer.php');
    exit();
}


// Initialize message variables
$successMessage = '';
$errorMessage = '';

// Fetch current appointment details before handling form submission
$q = "SELECT * FROM appointment WHERE appointmentID='$appointmentID'";
$r = mysqli_query($conn, $q);

if (mysqli_num_rows($r) == 1) {
    $row = mysqli_fetch_array($r);

    $appointmentTime = $row['appointmentTime'];
    $appointmentDateOnly = date('Y-m-d', strtotime($appointmentTime));
    $appointmentTimeOnly = date('H:i', strtotime($appointmentTime));
    
    $patientID = $row['patientID'];
    $dentistID = $row['dentistID'];
    $dentalServiceType = $row['dentalServiceType'];
    $serviceName = $row['serviceName'];
    $servicePrice = $row['servicePrice'];
    $lengthOfTime = $row['lengthOfTime'];
    $appointmentStatus = $row['appointmentStatus'];
} else {
    echo '<script>alert("This page has been accessed in error.");</script>';
    include('../footer.php');
    exit();
}

// Handle form submission
if (isset($_POST['submitted']) && !($appointmentStatus == 'Approved')) {
    $errors = array();

    // Validate form inputs
    if (empty($_POST['appointmentDateOnly']) || empty($_POST['appointmentTimeOnly'])) {
        $errors[] = 'You forgot to select the appointment date and time.';
    } else {
        $newAppointmentDateOnly = mysqli_real_escape_string($conn, trim($_POST['appointmentDateOnly']));
        $newAppointmentTimeOnly = mysqli_real_escape_string($conn, trim($_POST['appointmentTimeOnly']));
        $newAppointmentTime = $newAppointmentDateOnly . ' ' . $newAppointmentTimeOnly . ':00';
    }

    if (empty($_POST['dentalServiceType'])) {
        $errors[] = 'You forgot to select a dental service type.';
    } else {
        $newdentalServiceType = mysqli_real_escape_string($conn, trim($_POST['dentalServiceType']));
    }

    if (empty($_POST['serviceName'])) {
        $errors[] = 'You forgot to select the service name.';
    } else {
        $newserviceName = mysqli_real_escape_string($conn, trim($_POST['serviceName']));
    }

    if (empty($_POST['servicePrice'])) {
        $errors[] = 'You forgot to enter the price of service.';
    } else {
        $newservicePrice = mysqli_real_escape_string($conn, trim($_POST['servicePrice']));
    }

    if (empty($_POST['lengthOfTime'])) {
        $errors[] = 'You forgot to enter the period.';
    } else {
        $newlengthOfTime = mysqli_real_escape_string($conn, trim($_POST['lengthOfTime']));
    }

    if (empty($errors)) {
        // Check if there are any changes
        if (
            isset($newAppointmentTime) && $newAppointmentTime != $appointmentTime || 
            $newdentalServiceType != $dentalServiceType || 
            $newserviceName != $serviceName || 
            $newservicePrice != $servicePrice || 
            $newlengthOfTime != $lengthOfTime
        ) {
            // Update the appointment details
            $q = "UPDATE appointment SET 
                    appointmentTime='$newAppointmentTime', 
                    dentalServiceType='$newdentalServiceType', 
                    serviceName='$newserviceName', 
                    servicePrice='$newservicePrice', 
                    lengthOfTime='$newlengthOfTime' 
                  WHERE appointmentID='$appointmentID'";
            $r = mysqli_query($conn, $q);

            if ($r) {
                // Refresh data after successful update
                $updatedData = mysqli_query($conn, "SELECT * FROM appointment WHERE appointmentID='$appointmentID'");
                $row = mysqli_fetch_array($updatedData);

                // Update PHP variables with new values
                $appointmentTime = $row['appointmentTime'];
                $dentalServiceType = $row['dentalServiceType'];
                $serviceName = $row['serviceName'];
                $servicePrice = $row['servicePrice'];
                $lengthOfTime = $row['lengthOfTime'];
                $successMessage = 'The appointment has been edited.';
                
            } else {
                $errorMessage = 'The appointment could not be edited due to a system error: ' . mysqli_error($conn);
            }
        } else {
            $successMessage = 'No changes were made.';
        }
    } else {
        $errorMessage = 'The following error(s) occurred:<br />' . implode('<br />', $errors);
    }
}

// Fetch all patients and dentists for dropdown lists
$patients_q = "SELECT p.patientID, u.name FROM patient p JOIN user u ON p.userID = u.userID";
$patients_r = mysqli_query($conn, $patients_q);

$dentists_q = "SELECT d.dentistID, u.name FROM dentist d JOIN user u ON d.userID = u.userID";
$dentists_r = mysqli_query($conn, $dentists_q);

// Service Options
$serviceOptions = [
    'Treatment' => [
        'Teeth Cleaning' => 'Teeth Cleaning',
        'Crown Placement' => 'Crown Placement',
        'Teeth Whitening' => 'Teeth Whitening',
        'Root Canal' => 'Root Canal'
    ],
    'CheckUp' => [
        'Routine Checkup' => 'Routine Checkup',
        'Comprehensive Checkup' => 'Comprehensive Checkup'
    ]
];

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../images/dentalLogo.png" type="image/x-icon">
    <title>Edit Appointment</title>
    <style>
        body {
            font-family: "Reem Kufi";
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        h1 {
            text-align: center;
            margin-top: 30px;
        }
        form {
            max-width: 500px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px 5px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }
        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .go-back-btn,
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            color: white;
            background-color: black;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        .go-back-btn:hover,
        input[type="submit"]:hover {
            color: black;
            background-image: linear-gradient(135deg, #b2eff1 40%, #a58ef8 100%);
            box-shadow: 0 0 15px 5px rgba(255, 255, 255, 0.75);
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var successMessage = '<?php echo $successMessage; ?>';
            var errorMessage = '<?php echo $errorMessage; ?>';
            var appointmentStatus = '<?php echo $appointmentStatus; ?>'

            if (successMessage) {
                alert(successMessage);
                setTimeout(function() {
                window.location.href = 'manage_appointment.php';
            }, 0); // Redirect directly after clicking OK (adjust as needed)
            }
            if (errorMessage) {
                alert(errorMessage);
            }
            if (appointmentStatus === 'Approved') {
            alert('The appointment is approved and cannot be edited.');
            document.querySelector('form').style.display = 'none';
            setTimeout(function() {
                window.location.href = 'manage_appointment.php';
            }, 0); // Redirect directly after clicking OK (adjust as needed)
        }
    });
    </script>
</head>
<body>
    <?php include "Admin.php"; ?>

    <h1>Edit Appointment</h1>

    <div style="display: inline-flex; margin-left: 1100px;">
        <a href="manage_appointment.php">
            <button class="go-back-btn" style="width: 100px; font-size:15px;">Go Back</button>
        </a>
        <br><br>
    </div>

    <form action="edit_appointment.php" method="post">
        <label for="appointmentDateOnly">Appointment Date:</label>
        <input type="date" id="appointmentDateOnly" name="appointmentDateOnly" value="<?php echo htmlspecialchars($appointmentDateOnly); ?>" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required><br><br>

        <label for="appointmentTimeOnly">Appointment Time:</label>
        <input type="time" id="appointmentTimeOnly" name="appointmentTimeOnly" value="<?php echo htmlspecialchars($appointmentTimeOnly); ?>" min="10:00" max="19:00" required><br><br>

        <!-- Hidden input for storing the combined datetime -->
        <input type="hidden" id="appointmentTime" name="appointmentTime" value="<?php echo htmlspecialchars($appointmentTime); ?>">

        <label for="patientID">Patient:</label>
        <select id="patientID" name="patientID" disabled>
            <?php
            while ($patient = mysqli_fetch_array($patients_r)) {
                echo '<option value="' . $patient['patientID'] . '"' . ($patient['patientID'] == $patientID ? ' selected' : '') . '>' . htmlspecialchars($patient['name']) . '</option>';
            }
            ?>
        </select><br><br>

        <label for="dentistID">Dentist:</label>
        <select id="dentistID" name="dentistID" disabled>
            <?php
            while ($dentist = mysqli_fetch_array($dentists_r)) {
                echo '<option value="' . $dentist['dentistID'] . '"' . ($dentist['dentistID'] == $dentistID ? ' selected' : '') . '>' . htmlspecialchars($dentist['name']) . '</option>';
            }
            ?>
        </select><br><br>

        <label for="dentalServiceType">Dental Service Type:</label>
        <select id="dentalServiceType" name="dentalServiceType">
            <option value="CheckUp" <?php echo ($dentalServiceType == 'CheckUp') ? 'selected' : ''; ?>>CheckUp</option>
            <option value="Treatment" <?php echo ($dentalServiceType == 'Treatment') ? 'selected' : ''; ?>>Treatment</option>
        </select><br><br>

        <label for="serviceName">Service Name:</label>
        <select id="serviceName" name="serviceName">
        <?php
        if ($dentalServiceType && isset($serviceOptions[$dentalServiceType])) {
            foreach ($serviceOptions[$dentalServiceType] as $serviceName => $displayName) {
                echo '<option value="' . $serviceName . '"' . ($serviceName == $serviceName ? ' selected' : '') . '>' . htmlspecialchars($displayName) . '</option>';
            }
        }
        ?>
        </select><br><br>

        <label for="servicePrice">Price(RM): </label>
        <input type="text" id="servicePrice" name="servicePrice" value="<?php echo htmlspecialchars($servicePrice); ?>"><br><br>

        <label for="lengthOfTime">Period:</label>
        <input type="text" id="lengthOfTime" name="lengthOfTime" value="<?php echo htmlspecialchars($lengthOfTime); ?>"><br><br>

        <label for="appointmentStatus">Appointment Status:</label>
        <input type="text" id="appointmentStatus" name="appointmentStatus" value="<?php echo htmlspecialchars($appointmentStatus); ?>" disabled><br><br>

        <input type="submit" name="submit" value="Update">
        <input type="hidden" name="submitted" value="TRUE">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($appointmentID); ?>">
    </form>
    <br><br><br><br>

    <?php include '../footer.php'; ?>
    <script>
        document.getElementById('dentalServiceType').addEventListener('change', function() {
        var selectedServiceType = this.value;
        var serviceNameSelect = document.getElementById('serviceName');

        // Clear existing options
        serviceNameSelect.innerHTML = '';

        // Populate options based on selected service type
        if (selectedServiceType in <?php echo json_encode($serviceOptions); ?>) {
            // Check if the selectedServiceType exists in the PHP array $serviceOptions (converted to JavaScript object)

            var services = <?php echo json_encode($serviceOptions); ?>[selectedServiceType];
            for (var serviceName in services) {
                // Create a new <option> element
                var option = document.createElement('option');
                
                // Set the value attribute of the <option> to the service name
                option.value = serviceName;
                
                // Set the text content of the <option> to the display name of the service
                option.text = services[serviceName];
                
                // Append the newly created <option> element to the serviceNameSelect <select> element
                serviceNameSelect.appendChild(option);
            }
        }
});

    </script>
</body>
</html>
