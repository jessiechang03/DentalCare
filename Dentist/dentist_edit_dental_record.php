<?php
// Include the database configuration file
include "../config.php";

// Set timezone to Malaysia (UTC+8)
date_default_timezone_set('Asia/Kuala_Lumpur');

// Check if record ID is provided in the URL or POST
if (isset($_GET['id'])) {
    $recordID = $_GET['id'];
} else if (isset($_POST['id'])) {
    $recordID = $_POST['id'];
} else {
    echo '<script>alert("This page has been accessed in error.");</script>';
    include('../footer.php');
    exit();
}

// Initialize message variables
$successMessage = '';
$errorMessage = '';

// Fetch current dental record details before handling form submission
$q = "SELECT * FROM dentalrecord WHERE recordID='$recordID'";
$r = mysqli_query($conn, $q);

if (mysqli_num_rows($r) == 1) {
    $row = mysqli_fetch_array($r);

    $recordID = $row['recordID'];
    $recordDate = date('Y-m-d\TH:i', strtotime($row['recordDate'])); // Convert to 'datetime-local' format
    $dentistID = $row['dentistID'];
    $patientID = $row['patientID'];
    $symptoms = $row['symptoms'];
    $treatment = $row['treatment'];
    $history = $row['history'];
    $appointmentID = $row['appointmentID'];
} else {
    echo '<script>alert("This page has been accessed in error.");</script>';
    include('../footer.php');
    exit();
}

// Set the default record date to current date and time if not set
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $recordDate = date('Y-m-d\TH:i'); // Current date and time in 'datetime-local' format
}

// Handle form submission
if (isset($_POST['submitted'])) {
    $errors = array();

    // Validate form inputs
    if (empty($_POST['recordDate'])) {
        $errors[] = 'You forgot to enter the record date.';
    } else {
        $newrecordDate = mysqli_real_escape_string($conn, trim($_POST['recordDate']));
    }

    if (empty($_POST['symptoms'])) {
        $errors[] = 'You forgot to enter the symptom.';
    } else {
        $newsymptoms = mysqli_real_escape_string($conn, trim($_POST['symptoms']));
    }

    if (empty($_POST['treatment'])) {
        $errors[] = 'You forgot to select the treatment.';
    } else {
        $newtreatment = mysqli_real_escape_string($conn, trim($_POST['treatment']));
    }

    if (empty($_POST['history'])) {
        $errors[] = 'You forgot to enter the history.';
    } else {
        $newhistory = mysqli_real_escape_string($conn, trim($_POST['history']));
    }


    if (empty($errors)) {
        // Check if there are any changes
        if (
            $newrecordDate != $recordDate || 
            $newsymptoms != $symptoms || 
            $newtreatment != $treatment || 
            $newhistory != $history 
        ) {
            // Update the dental record details
            $q = "UPDATE dentalrecord SET 
                    recordDate='$newrecordDate', 
                    symptoms='$newsymptoms', 
                    treatment='$newtreatment', 
                    history='$newhistory'
                  WHERE recordID='$recordID'";
            $r = mysqli_query($conn, $q);

            if ($r) {
                // Refresh data after successful update
                $updatedData = mysqli_query($conn, "SELECT * FROM dentalrecord WHERE recordID='$recordID'");
                $row = mysqli_fetch_array($updatedData);

                // Update PHP variables with new values
                $recordDate = date('Y-m-d\TH:i', strtotime($row['recordDate']));
                $symptoms = $row['symptoms'];
                $treatment = $row['treatment'];
                $history = $row['history'];
                
                $successMessage = 'The record has been edited.';
            } else {
                $errorMessage = 'The record could not be edited due to a system error: ' . mysqli_error($conn);
            }
        } else {
            $successMessage = 'No changes were made.';
        }
    } else {
        $errorMessage = 'The following error(s) occurred:<br />' . implode('<br />', $errors);
    }
}



mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../images/dentalLogo.png" type="image/x-icon">
    <title>Edit record</title>
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

            if (successMessage) {
                alert(successMessage);
            }
            if (errorMessage) {
                alert(errorMessage);
            }
        });
    </script>
</head>
<body>
    <?php include "Dentist.php"; ?>

    <h1>Edit record</h1>

    <div style="display: inline-flex; margin-left: 1100px;">
        <a href="dental_record.php">
            <button class="go-back-btn" style="width: 100px; font-size:15px;">Go Back</button>
        </a>
        <br><br>
    </div>

    <form action="dentist_edit_dental_record.php" method="post">
        <label for="recordDate">Record Date:</label>
        <input type="datetime-local" id="recordDate" name="recordDate" value="<?php echo htmlspecialchars($recordDate); ?>"><br><br>

        <label for="patientID">Patient ID:</label>
        <input type="text" id="patientID" name="patientID" class="input-field" value="<?php echo htmlspecialchars($patientID); ?>" disabled>
        <br><br>

        <label for="dentistID">Dentist ID:</label>
        <input type="text" id="dentistID" name="dentistID" class="input-field" value="<?php echo htmlspecialchars($dentistID); ?>" disabled>
        <br><br>

        <label for="symptoms">Symptom:</label>
        <input type="text" id="symptoms" name="symptoms" class="input-field" value="<?php echo htmlspecialchars($symptoms); ?>">
        <br><br>

        <label for="treatment">Treatment:</label>
        <select id="treatment" name="treatment" class="input-field">
            <option value="Cleaning" <?php echo ($treatment == 'Cleaning') ? 'selected' : ''; ?>>Cleaning</option>
            <option value="Crown Placement" <?php echo ($treatment == 'Crown Placement') ? 'selected' : ''; ?>>Crown Placement</option>
            <option value="Routine exam" <?php echo ($treatment == 'Routine exam') ? 'selected' : ''; ?>>Routine exam</option>
            <option value="Teeth whitening" <?php echo ($treatment == 'Teeth whitening') ? 'selected' : ''; ?>>Teeth whitening</option>
            <option value="Root Canal" <?php echo ($treatment == 'Root Canal') ? 'selected' : ''; ?>>Root Canal</option>
        </select>

        <label for="history">History:</label>
        <input type="text" id="history" name="history" class="input-field" value="<?php echo htmlspecialchars($history); ?>">
        <br><br>

        <label for="appointmentID">Appointment ID:</label>
        <input type="text" id="appointmentID" name="appointmentID" value="<?php echo htmlspecialchars($appointmentID); ?> " disabled><br><br>

        <input type="submit" name="submit" value="Update">
        <input type="hidden" name="submitted" value="TRUE">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($recordID); ?>">
    </form>
    <br><br><br><br>

    <?php include '../footer.php'; ?>
</body>
</html>
