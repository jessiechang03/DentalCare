<?php
include('../config.php');
include('Patient.php');
$userID = $_SESSION['userid'];

$patientQuery = "SELECT * FROM Patient WHERE userID = '$userID'";
$patientResult = mysqli_query($conn, $patientQuery);
$row_user = mysqli_fetch_assoc($patientResult);
$patientID = $row_user['patientID'];

// $currentDate = date('Y-m-d H:i:s');
$requestingAppointmentQuery = "SELECT * FROM Appointment WHERE patientID = '$patientID' AND appointmentTime != '' AND appointmentStatus = 'Pending' ORDER BY appointmentTime DESC";
$requestingAppointmentResult = mysqli_query($conn, $requestingAppointmentQuery);

$upcomingAppointmentQuery = "SELECT * FROM Appointment WHERE patientID = '$patientID' AND appointmentTime != '' AND appointmentStatus = 'Approved' ORDER BY appointmentTime ASC";
$upcomingAppointmentResult = mysqli_query($conn, $upcomingAppointmentQuery);

// Handle appointment cancellation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel_appointment_id'])) {
    $appointmentID = $_POST['cancel_appointment_id'];

    $deletePaymentsQuery = "DELETE FROM payment WHERE appointmentID = '$appointmentID'";
    if (mysqli_query($conn, $deletePaymentsQuery)) {
        // Now delete the appointment
        $deleteQuery = "DELETE FROM Appointment WHERE appointmentID = '$appointmentID' AND patientID = '$patientID'";
        if (mysqli_query($conn, $deleteQuery)) {
            $_SESSION['cancel_success'] = true;
            echo "<script>alert('Appointment successfully cancelled!'); window.location.href='view_appointment.php';</script>";
            exit();
        } else {
            echo '<script>alert("Error canceling appointment. Please try again later.");</script>';
        }
    } else {
        echo '<script>alert("Error canceling appointment. Please try again later.");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointments</title>
    <link rel="shortcut icon" href="../images/dentalLogo.png" type="image/x-icon">
    <link rel="stylesheet" href="patient_style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Signika+Negative:wght@300..700&display=swap" rel="stylesheet">
    
    <style>
        
        .container h3 {
            margin-bottom: 10px;
            align-self: flex-start;
        }

        .appointment-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .appointment-table th,
        .appointment-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .action-column {
            text-align: center;
        }


        .make-payment-button {
            text-align: center;

            background-color: #45a049;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .make-payment-button:hover {
            background-color: #45a049;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }

        .confirm-btn {
            text-align: center;

        },

        .confirm-btn:hover,

        .form-submit-container {
            display: flex;
            justify-content: center;
        }

        .make-appointment-button {
            background-color: #4CAF50;
            color: white;
            padding: 8px 16px;
            border: none;
            cursor: pointer;

        }

        .make-appointment-button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="outer-container">
        <!-- Requesting Appointments Section -->
        <div class="container">
            <h3>Requesting Appointments</h3>
            <?php if (mysqli_num_rows($requestingAppointmentResult) > 0) : ?>
                <table class="appointment-table">
                    <tr>
                        <th>Appointment ID</th>
                        <th>Appointment Time</th>
                        <th>Dental Service Type</th>
                        <th>Service Name</th>
                        <th>Service Price</th>
                        <th>Length of Time</th>
                        <th>Dentist ID</th>
                        <th>Action</th>
                    </tr>
                    <?php while ($appointment = mysqli_fetch_assoc($requestingAppointmentResult)) : ?>
                        <tr>
                            <td><?= $appointment['appointmentID'] ?></td>
                            <td><?= $appointment['appointmentTime'] ?></td>
                            <td><?= $appointment['dentalServiceType'] ?></td>
                            <td><?= $appointment['serviceName'] ?></td>
                            <td><?= $appointment['servicePrice'] ?></td>
                            <td><?= $appointment['lengthOfTime'] ?></td>
                            <td><?= $appointment['dentistID'] ?></td>
                            <td class="action-column">
                                <form id="form_cancel_<?= $appointment['appointmentID'] ?>" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                    <input type="hidden" name="cancel_appointment_id" value="<?= $appointment['appointmentID'] ?>">
                                    <button type="button" class="cancel-appointment-button" onclick="confirmCancel('<?= $appointment['appointmentID'] ?>')">Cancel</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else : ?>
                <p>No requesting appointments found.</p>
            <?php endif; ?>
        </div>

        
        <!-- Upcoming Appointments Section -->
        <div class="container">
            <h3>Upcoming Appointments</h3>
            <?php if (mysqli_num_rows($upcomingAppointmentResult) > 0) : ?>
                <table class="appointment-table">
                    <tr>
                        <th>Appointment ID</th>
                        <th>Appointment Time</th>
                        <th>Dental Service Type</th>
                        <th>Service Name</th>
                        <th>Service Price</th>
                        <th>Length of Time</th>
                        <th>Dentist ID</th>
                        <th>Action</th>

                    </tr>
                    <?php while ($appointment = mysqli_fetch_assoc($upcomingAppointmentResult)) : ?>
                        <tr>
                            <td><?= $appointment['appointmentID'] ?></td>
                            <td><?= $appointment['appointmentTime'] ?></td>
                            <td><?= $appointment['dentalServiceType'] ?></td>
                            <td><?= $appointment['serviceName'] ?></td>
                            <td><?= $appointment['servicePrice'] ?></td>
                            <td><?= $appointment['lengthOfTime'] ?></td>
                            <td><?= $appointment['dentistID'] ?></td>

                            <td class="action-column">
                                <?php
                                // Fetch payment status for the current appointment
                                $paymentStatusQuery = "SELECT paymentStatus FROM Payment WHERE appointmentID = '{$appointment['appointmentID']}'";
                                $paymentStatusResult = mysqli_query($conn, $paymentStatusQuery);
                                if (mysqli_num_rows($paymentStatusResult) > 0) {
                                    $paymentStatus = mysqli_fetch_assoc($paymentStatusResult)['paymentStatus'];
                                } else {
                                    $paymentStatus = 'UnPaid';
                                }

                                if ($paymentStatus != 'Paid') {
                                    ?>
                                    <form action="make_payment.php" method="post">
                                        <input type="hidden" name="appointment_id" value="<?= $appointment['appointmentID'] ?>">
                                        <input type="hidden" name="total_amount" value="<?= $appointment['servicePrice'] ?>">
                                        <button type="submit" class="make-payment-button">Pay Now</button>
                                    </form>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else : ?>
                <p>No upcoming appointments found.</p>
            <?php endif; ?>
            <br><br><br>
        </div>
        
    </div>

    

    <!-- Modal for Confirmation -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <p>Are you sure you want to cancel this appointment?</p>
            <form id="confirm_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="hidden" id="confirm_appointment_id" name="cancel_appointment_id">
                <button type="submit" class="confirm-btn">Yes</button>
                <button type="button" class="cancel-appointment-button" onclick="closeModal()">No</button>
            </form>
        </div>
    </div>

    <script>
        function confirmCancel(appointmentID) {
            document.getElementById('confirm_appointment_id').value = appointmentID;
            var modal = document.getElementById('confirmModal');
            modal.style.display = "block";
        }

        function closeModal() {
            var modal = document.getElementById('confirmModal');
            modal.style.display = "none";
        }
    </script>
    <br><br><br>
    
</body>
</html>
<?php include '../footer.php'; ?>