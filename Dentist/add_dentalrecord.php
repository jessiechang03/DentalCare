<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../images/dentalLogo.png" type="image/x-icon">
    <title>Add Dental Record</title>
    <style>
        body {
            font-family: "Reem Kufi";
        }
        h1 {
            text-align: center;
            margin-top: 30px;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
            border: 2px solid #ccc;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            box-sizing: border-box;
            margin: 0 auto;
        }
        .form-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-bottom: 20px;
        }
        .form-row label {
            width: 30%;
            margin-right: 10px;
            text-align: left;
        }
        .form-row input,
        .form-row select {
            width: 60%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        .go-back-btn,
        .submit-button {
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
        }
        .go-back-btn:hover,
        .submit-button:hover {
            color: black;
            background-image: linear-gradient(135deg, #b2eff1 40%, #a58ef8 100%);
            box-shadow: 0 0 15px 5px rgba(255,255,255,0.75);
        }
        .error-message {
            color: red;
            text-align: center;
        }
        .success-message {
            color: green;
            text-align: center;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const recordDateInput = document.getElementById('recordDate');

            // Set min attribute to today's date
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const minDate = `${yyyy}-${mm}-${dd}T10:00`;
            recordDateInput.setAttribute('min', minDate);

            // Ensure the time is within the operation hours (10 AM to 7 PM)
            recordDateInput.addEventListener('change', function () {
                const selectedDate = new Date(recordDateInput.value);
                const hour = selectedDate.getHours();
                if (hour < 10 || hour >= 19) {
                    alert('Please select a time between 10 AM and 7 PM');
                    recordDateInput.value = '';
                }
            });
        });
    </script>
</head>
<body>
<?php
// Assuming a session variable `$_SESSION['dentistID']` contains the logged-in dentist's ID
include('../config.php');
include('Dentist.php');

$loggedInDentistID = $_SESSION['dentistID'];

echo '<h1>Add Dental Record</h1>';

if (isset($_POST['submitted'])) {
    $errors = array();

    $required_fields = array('recordDate', 'patientID', 'symptoms', 'treatment', 'history', 'appointmentID');

    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $errors[] = "Please enter $field";
        }
    }

    // Validate record date
    $recordDate = $_POST['recordDate'];
    $selectedDate = new DateTime($recordDate);
    $hour = (int)$selectedDate->format('H');
    if ($hour < 10 || $hour >= 19) {
        $errors[] = 'Please select a time between 10 AM and 7 PM';
    }

    if (empty($errors)) {
        $patientID = $_POST['patientID'];
        $symptoms = $_POST['symptoms'];
        $treatment = $_POST['treatment'];
        $history = $_POST['history'];
        $appointmentID = $_POST['appointmentID'];

        // Generate unique ID for the dental record
        $recordID = generateUniqueID('DR', 'dentalrecord', 'recordID');

        // Insert into Dental Record table
        $query = "INSERT INTO dentalrecord (recordID, recordDate, patientID, dentistID, symptoms, treatment, history, appointmentID) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param('ssssssss', $recordID, $recordDate, $patientID, $loggedInDentistID, $symptoms, $treatment, $history, $appointmentID);
            $stmt->execute();
            $stmt->close();

            $update_query = "UPDATE payment SET recordID = ? WHERE appointmentID = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param('ss', $recordID, $appointmentID);
            $stmt->execute();
            $stmt->close();

            echo '<div class="success-message">Dental record is added successfully!</div>';
        } else {
            echo '<div class="error-message">Error preparing the query.</div>';
        }
    } else {
        echo '<div class="error-message">The following errors occurred: <br>';
        foreach ($errors as $error) {
            echo "- $error <br>";
        }
        echo '</div>';
    }
}

function generateUniqueID($prefix, $table, $column) {
    global $conn;

    // SQL to find the max numeric part of the IDs
    $query = "SELECT MAX(CAST(SUBSTRING($column, LENGTH(?) + 1) AS UNSIGNED)) as maxID 
              FROM $table
              WHERE $column LIKE ?";
    
    // Prepare and execute the statement
    if ($stmt = $conn->prepare($query)) {
        $likePattern = $prefix . '%';
        $stmt->bind_param('ss', $prefix, $likePattern);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        $maxID = $row['maxID'];
        if ($maxID === NULL) {
            return $prefix . '001';
        } else {
            $nextID = (int)$maxID + 1;
            return $prefix . str_pad($nextID, 3, '0', STR_PAD_LEFT);
        }
    } else {
        // Handle errors
        die("Error preparing the query: " . $conn->error);
    }
}

// Fetch patient IDs who have appointments with the logged-in dentist
$patientIDsQuery = "SELECT DISTINCT p.patientID 
                    FROM appointment a
                    JOIN patient p ON a.patientID = p.patientID
                    WHERE a.dentistID = ?";
$patientIDsStmt = $conn->prepare($patientIDsQuery);
$patientIDsStmt->bind_param('s', $loggedInDentistID);
$patientIDsStmt->execute();
$patientIDs = $patientIDsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$patientIDsStmt->close();

// Fetch appointment IDs for the logged-in dentist
$appointmentIDsQuery = "SELECT appointmentID 
                        FROM appointment 
                        WHERE dentistID = ?";
$appointmentIDsStmt = $conn->prepare($appointmentIDsQuery);
$appointmentIDsStmt->bind_param('s', $loggedInDentistID);
$appointmentIDsStmt->execute();
$appointmentIDs = $appointmentIDsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$appointmentIDsStmt->close();

echo '<div style="display: inline-flex; margin-left: 1100px;">
        <a href="dental_record.php">
        <button class="go-back-btn" style="width: 100px; font-size:15px;">Go Back</button>
        </a>
        <br><br>
      </div>';
?>

<form method="post" action="add_dentalrecord.php">
    <input type="hidden" name="submitted" value="TRUE" />

    <!-- Record Date Input -->
    <div class="form-row">
        <label for="recordDate">Record Date:</label>
        <input type="datetime-local" id="recordDate" name="recordDate" class="input-field">
    </div>

    <!-- Patient ID Selection -->
    <div class="form-row">
        <label for="patientID">Patient ID:</label>
        <select id="patientID" name="patientID" class="input-field">
            <option value="" selected="selected">-- Select Patient --</option>
            <?php foreach ($patientIDs as $patient) { ?>
                <option value="<?= $patient['patientID'] ?>"><?= $patient['patientID'] ?></option>
            <?php } ?>
        </select>
    </div>

    <!-- Symptom Input -->
    <div class="form-row">
        <label for="symptoms">Symptom:</label>
        <input type="text" id="symptoms" name="symptoms" class="input-field">
    </div>

    <!-- Treatment Input -->
    <div class="form-row">
        <label for="treatment">Treatment:</label>
        <select id="treatment" name="treatment" class="input-field">
            <option value="" selected="selected">-- Select Treatment --</option>
            <option value="Cleaning">Cleaning</option>
            <option value="Crown Placement">Crown Placement</option>
            <option value="Routine exam">Routine exam</option>
            <option value="Teeth whitening">Teeth whitening</option>
            <option value="Root Canal">Root Canal</option>
            <option value="Routine Checkup">Routine Checkup</option>
            <option value="Comprehensive CheckUp">Comprehensive CheckUp</option>
        </select>
    </div>

    <!-- History Selection -->
    <div class="form-row">
        <label for="history">History:</label>
        <input type="text" id="history" name="history" class="input-field">
    </div>

    <!-- Appointment ID Input -->
    <div class="form-row">
        <label for="appointmentID">Appointment ID:</label>
        <select id="appointmentID" name="appointmentID" class="input-field">
            <option value="" selected="selected">-- Select Appointment ID --</option>
            <?php foreach ($appointmentIDs as $appointment) { ?>
                <option value="<?= $appointment['appointmentID'] ?>"><?= $appointment['appointmentID'] ?></option>
            <?php } ?>
        </select>
    </div>

    <input class="submit-button" type="submit" value="Submit">
</form>
<br><br><br><br><br>

</body>
</html>
<?php include '../footer.php' ?>
