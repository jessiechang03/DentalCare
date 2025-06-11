<?php
include('../config.php');
include('Patient.php');

$userID = $_SESSION['userid'];
$patientQuery = "SELECT * FROM Patient WHERE userID = '$userID'";
$patientResult = mysqli_query($conn, $patientQuery);
$row_user = mysqli_fetch_assoc($patientResult);
$patientID = $row_user['patientID'];

$dentistQuery = "SELECT Dentist.dentistID, User.name FROM Dentist JOIN User ON Dentist.userID = User.userID";
$dentistResult = mysqli_query($conn, $dentistQuery);
$dentists = mysqli_fetch_all($dentistResult, MYSQLI_ASSOC);

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

function generateAppointmentID($conn) {
    $result = mysqli_query($conn, "SELECT COUNT(*) AS count FROM Appointment");
    $row = mysqli_fetch_assoc($result);
    $appointmentID = 'A' . str_pad($row['count'] + 1, 3, '0', STR_PAD_LEFT);
    return $appointmentID;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointmentDate = $_POST['appointment_date'];
    $appointmentTime = $_POST['appointment_time'];
    $dentistID = $_POST['dentist_id'];
    $serviceName = $_POST['service_name'];
    $dentalServiceType = $_POST['dental_service_type'];
    $servicePrice = $_POST['service_price'];
    $lengthOfTime = $_POST['length_of_time'];

    $appointmentID = generateAppointmentID($conn);

    $appointmentTimestamp = $appointmentDate . ' ' . $appointmentTime . ':00';
    $appointmentStatus = 'Pending'; // Initial status set to Pending

    $insertQuery = "INSERT INTO Appointment (appointmentID, appointmentTime, dentalServiceType, serviceName, servicePrice, lengthOfTime, patientID, dentistID, appointmentStatus) 
                    VALUES ('$appointmentID', '$appointmentTimestamp', '$dentalServiceType', '$serviceName', '$servicePrice', '$lengthOfTime', '$patientID', '$dentistID', '$appointmentStatus')";

    if (mysqli_query($conn, $insertQuery)) {
        $_SESSION['success_message'] = "Appointment successfully scheduled!";
        echo "<script>alert('Appointment successfully scheduled!'); window.location.href='Patient_homepage.php';</script>";
        exit;
    } else {
        echo "Error: " . $insertQuery . "<br>" . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Appointment</title>
    <link rel="shortcut icon" href="../images/dentalLogo.png" type="image/x-icon">
    <link rel="stylesheet" href="patient_style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Signika+Negative:wght@300..700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="outer-container">
        <form id="appointmentForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="container">
                <h2>Appointment Information</h2>
                <table>
                    <tr>
                        <td>Appointment Date</td>
                        <td><input type="date" name="appointment_date" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required></td>
                    </tr>
                    <tr>
                        <td>Appointment Time</td>
                        <td><input type="time" name="appointment_time" min="10:00" max="19:00" required></td>
                    </tr>
                    <tr>
                        <td>Dentist Name</td>
                        <td>
                            <select name="dentist_id" required>
                                <option value="">Select Dentist</option>
                                <?php foreach ($dentists as $dentist) : ?>
                                    <option value="<?php echo $dentist['dentistID']; ?>"><?php echo $dentist['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Dental Service Type</td>
                        <td>
                            <select id="dental_service_type" name="dental_service_type" required>
                                <option value="">Select Service Type</option>
                                <?php foreach ($serviceOptions as $type => $services) : ?>
                                    <option value="<?php echo $type; ?>"><?php echo $type; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Service Name</td>
                        <td>
                            <select id="service_name" name="service_name" required>
                                <option value="">Select Service Name</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Service Price</td>
                        <td><input type="text" id="service_price" name="service_price" readonly></td>
                    </tr>
                    <tr>
                        <td>Length of Time</td>
                        <td><input type="text" id="length_of_time" name="length_of_time" readonly></td>
                    </tr>
                </table>
            </div>

            <div class="form-submit-container">
                <button class="make-appointment-button" type="submit">Submit</button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('dental_service_type').addEventListener('change', function () {
            var serviceType = this.value;
            var serviceNameSelect = document.getElementById('service_name');
            var servicePriceInput = document.getElementById('service_price');
            var lengthOfTimeInput = document.getElementById('length_of_time');

            serviceNameSelect.innerHTML = '<option value="">Select Service Name</option>';

            if (serviceType) {
                var serviceOptions = <?php echo json_encode($serviceOptions); ?>;
                var services = serviceOptions[serviceType];
                for (var serviceName in services) {
                    var option = document.createElement('option');
                    option.value = serviceName;
                    option.text = serviceName;
                    serviceNameSelect.appendChild(option);
                }
            }

            servicePriceInput.value = '';
            lengthOfTimeInput.value = '';
        });

        document.getElementById('service_name').addEventListener('change', function () {
            var serviceName = this.value;
            var serviceType = document.getElementById('dental_service_type').value;
            var servicePriceInput = document.getElementById('service_price');
            var lengthOfTimeInput = document.getElementById('length_of_time');

            if (serviceName) {
                var serviceOptions = <?php echo json_encode($serviceOptions); ?>;
                var service = serviceOptions[serviceType][serviceName];
                servicePriceInput.value = service.price;
                lengthOfTimeInput.value = service.length;
            } else {
                servicePriceInput.value = '';
                lengthOfTimeInput.value = '';
            }
        });

        document.getElementById('appointment_time').addEventListener('input', function() {
            var selectedTime = this.value;

            // Parse the selected time
            var selectedHour = parseInt(selectedTime.split(':')[0], 10);
            var selectedMinute = parseInt(selectedTime.split(':')[1], 10);

            // Set the minimum and maximum times
            var minHour = 10;  // 10 AM
            var maxHour = 19;  // 7 PM

            // Convert selected time to 24-hour format for comparison
            var selectedTime24 = (selectedHour < 10 ? '0' : '') + selectedHour + ':' + selectedMinute;

            // Compare with min and max times
            var minTime = (minHour < 10 ? '0' : '') + minHour + ':00';
            var maxTime = (maxHour < 10 ? '0' : '') + maxHour + ':00';

            // Check if selected time is within valid range
            if (selectedTime24 < minTime || selectedTime24 > maxTime) {
                alert('Please select a time between 10:00 AM and 7:00 PM.');
                this.value = '';  // Reset the value
            }
        });


    </script>
</body>
</html>
