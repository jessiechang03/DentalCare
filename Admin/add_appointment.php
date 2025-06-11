<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../images/dentalLogo.png" type="image/x-icon">
    <title>Add Appointment</title>
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
        .add-user-btn{
            font-size: 15px;
            width: 15%;
            color: white;
            background-color: black;
            border: 1px solid white;
            padding: 10px 0px;
            border-radius: 100px;
            cursor: pointer;
            font-weight: bold;
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
        .add-user-btn:hover,
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
</head>
<body>
<?php
include('../config.php');
include('Admin.php');

echo '<h1>Add Appointment</h1>';

if(isset($_POST['submitted'])){
    $errors = array();

    $required_fields = array('appointmentDate','appointmentTime', 'patientID', 'dentistID', 'dentalServiceType', 'serviceName', 'servicePrice', 'lengthOfTime');

    foreach($required_fields as $field) {
        if(!isset($_POST[$field]) || empty($_POST[$field])) {
            $errors[] = "Please enter $field";
        }
    }

    if(empty($errors)) {
        $appointmentDate = $_POST['appointmentDate'];
        $appointmentTime = $_POST['appointmentTime'];
        $patientID = $_POST['patientID'];
        $dentistID = $_POST['dentistID'];
        $dentalServiceType = $_POST['dentalServiceType'];
        $serviceName = $_POST['serviceName'];
        $servicePrice = $_POST['servicePrice'];
        $lengthOfTime = $_POST['lengthOfTime'];
        $appointmentStatus = 'Pending'; // Default value

        // Combine appointmentDate and appointmentTime into a single datetime string
        $appointmentDateTime = $appointmentDate . ' ' . $appointmentTime;
        // Generate unique ID for the appointment
        $appointmentID = generateUniqueID('A', 'appointment', 'appointmentID');

        // Insert into Appointment table
        $query = "INSERT INTO appointment (appointmentID, appointmentTime, patientID, dentistID, dentalServiceType, serviceName, servicePrice, lengthOfTime, appointmentStatus) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if($stmt = $conn->prepare($query)){
            $stmt->bind_param('sssssssss', $appointmentID, $appointmentDateTime, $patientID, $dentistID, $dentalServiceType, $serviceName, $servicePrice, $lengthOfTime, $appointmentStatus);
            $stmt->execute();
            $stmt->close();
            echo '<div class="success-message">Appointment added successfully!</div>';
        } else {
            echo '<div class="error-message">Error preparing the query.</div>';
        }
    } else {
        echo '<div class="error-message">The following errors occurred:<br>';
        foreach($errors as $error) {
            echo "- $error<br>";
        }
        echo '</div>';
    }
}

function generateUniqueID($prefix, $table, $column) {
    global $conn;
    $query = "SELECT MAX(SUBSTRING($column, 2)) as maxID FROM $table";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $maxID = $row['maxID'];
    if ($maxID === NULL) {
        return $prefix . '001';
    } else {
        $nextID = (int)$maxID + 1;
        return $prefix . str_pad($nextID, 3, '0', STR_PAD_LEFT);
    }
}

// Fetch patient and dentist IDs
$patientIDs = $conn->query("SELECT patientID FROM patient")->fetch_all(MYSQLI_ASSOC);
$dentistIDs = $conn->query("SELECT dentistID FROM dentist")->fetch_all(MYSQLI_ASSOC);

$serviceOptions = [
    'Treatment' => [
        'Teeth Cleaning' => ['price' => 100, 'length' => '60 mins'],
        'Crown Placement' => ['price' => 350, 'length' => '90 mins'],
        'Teeth Whitening' => ['price' => 250, 'length' => '60 mins'],
        'Root Canal' => ['price' => 500, 'length' => '120 mins']
    ],
    'CheckUp' => [
        'Routine Checkup' => ['price' => 80, 'length' => '30 mins'],
        'Comprehensive Checkup' => ['price' => 100, 'length' => '45 mins']
    ]
];

echo '<div style="display: inline-flex; margin-left: 1100px;">
        <a href="manage_appointment.php">
        <button class="go-back-btn" style="width: 100px; font-size:15px;">Go Back</button>
        </a>
        <br><br>
      </div>';

?>


<form method="post" action="add_appointment.php">
    <input type="hidden" name="submitted" value="TRUE" />

     <!-- Appointment Date Input -->
     <div class="form-row">
        <label for="appointmentDate">Appointment Date:</label>
        <input type="date" id="appointmentDate" name="appointmentDate" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" class="input-field">
    </div>

    <!-- Appointment Time Input -->
    <div class="form-row">
        <label for="appointmentTime">Appointment Time:</label>
        <input type="time" id="appointmentTime" name="appointmentTime" min="10:00" max="19:00" class="input-field">
    </div>

    <!-- Patient ID Selection -->
    <div class="form-row">
        <label for="patientID">Patient ID:</label>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <select id="patientID" name="patientID" class="input-field">
            <option value="" selected="selected">-- Select Patient --</option>
            <?php foreach ($patientIDs as $patient) { ?>
                <option value="<?= $patient['patientID'] ?>"><?= $patient['patientID'] ?></option>
            <?php } ?>
        </select>
    </div>

    <!-- Dentist ID Selection -->
    <div class="form-row">
        <label for="dentistID">Dentist ID:</label>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <select id="dentistID" name="dentistID" class="input-field">
            <option value="" selected="selected">-- Select Dentist --</option>
            <?php foreach ($dentistIDs as $dentist) { ?>
                <option value="<?= $dentist['dentistID'] ?>"><?= $dentist['dentistID'] ?></option>
            <?php } ?>
        </select>
    </div>

    <!-- Dental Service Type Selection -->
      <div class="form-row">
        <label for="dentalServiceType">Dental Service Type:</label>
        <select id="dentalServiceType" name="dentalServiceType" class="input-field" onchange="updateServiceOptions()">
            <option value="" selected="selected">-- Select Service Type --</option>
            <?php foreach (array_keys($serviceOptions) as $type) { ?>
                <option value="<?= $type ?>"><?= $type ?></option>
            <?php } ?>
        </select>
    </div>

    <!-- Service Name Selection -->
    <div class="form-row">
        <label for="serviceName">Service Name:</label>
        <select id="serviceName" name="serviceName" class="input-field" onchange="updateServiceDetails()">
            <option value="" selected="selected">-- Select Service --</option>
        </select>
    </div>

    <!-- Service Price Display -->
    <div class="form-row">
        <label for="servicePrice">Service Price:</label>
        <input type="text" id="servicePrice" name="servicePrice" readonly>
    </div>

    <!-- Length of Time Display -->
    <div class="form-row">
        <label for="lengthOfTime">Length of Time:</label>
        <input type="text" id="lengthOfTime" name="lengthOfTime" readonly>
    </div>

    <!-- Appointment Status Input (Disabled) -->
    <div class="form-row">
        <label for="appointmentStatus">Appointment Status:</label>
        <input type="text" id="appointmentStatus" name="appointmentStatus" class="input-field" value="Pending" disabled>
    </div>

    <input class="submit-button" type="submit" value="Submit">
</form>
<br><br><br><br><br>
<script>
     const serviceOptions = <?php echo json_encode($serviceOptions); ?>;

    function updateServiceOptions() {
        const serviceTypeSelect = document.getElementById('dentalServiceType');
        const serviceNameSelect = document.getElementById('serviceName');
        const selectedType = serviceTypeSelect.value;

        // Clear the current options
        serviceNameSelect.innerHTML = '<option value="" selected="selected">-- Select Service --</option>';

        if (selectedType && serviceOptions[selectedType]) {
            const services = serviceOptions[selectedType];
            for (const serviceName in services) {
                const option = document.createElement('option');
                option.value = serviceName;
                option.textContent = serviceName;
                serviceNameSelect.appendChild(option);
            }
        }

        // Clear the service details fields
        document.getElementById('servicePrice').value = '';
        document.getElementById('lengthOfTime').value = '';
    }

    function updateServiceDetails() {
        const serviceTypeSelect = document.getElementById('dentalServiceType');
        const serviceNameSelect = document.getElementById('serviceName');
        const selectedType = serviceTypeSelect.value;
        const selectedService = serviceNameSelect.value;

        if (selectedType && selectedService && serviceOptions[selectedType] && serviceOptions[selectedType][selectedService]) {
            const serviceDetails = serviceOptions[selectedType][selectedService];
            document.getElementById('servicePrice').value = serviceDetails.price;
            document.getElementById('lengthOfTime').value = serviceDetails.length;
        } else {
            // Clear the service details fields if the selection is invalid
            document.getElementById('servicePrice').value = '';
            document.getElementById('lengthOfTime').value = '';
        }
    }
</script>
</body>
</html>

<?php include '../footer.php' ?>
