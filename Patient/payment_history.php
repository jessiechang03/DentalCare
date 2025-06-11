<?php
include('../config.php');
include('Patient.php');

$userID = $_SESSION['userid'];

$patientQuery = "SELECT patientID FROM Patient WHERE userID = '$userID'";
$patientResult = mysqli_query($conn, $patientQuery);
if ($patientResult) {
    $row_patient = mysqli_fetch_assoc($patientResult);
    $patientID = $row_patient['patientID'];
} else {
    die('Failed to retrieve patient ID: ' . mysqli_error($conn));
}

$paymentQuery = "
    SELECT Payment.invoiceNo, Payment.paymentDate, Payment.paymentFees, Payment.paymentStatus, Payment.paymentMethod, Appointment.appointmentTime
    FROM Payment
    JOIN Appointment ON Payment.appointmentID = Appointment.appointmentID
    WHERE Appointment.patientID = '$patientID' AND Payment.paymentStatus = 'Paid'
    ORDER BY Payment.paymentDate DESC
";
$paymentResult = mysqli_query($conn, $paymentQuery);
if (!$paymentResult) {
    die('Failed to retrieve payment records: ' . mysqli_error($conn));
}

$payments = mysqli_fetch_all($paymentResult, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Records</title>
    <link rel="shortcut icon" href="../images/dentalLogo.png" type="image/x-icon">
    <link rel="stylesheet" href="patient_style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Signika+Negative:wght@300..700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="outer-container">
        <div class="container">
            <h2>Payment Records</h2>
            <?php if (count($payments) > 0) : ?>
                <table>
                    <tr>
                        <th>Invoice No</th>
                        <th>Payment Date</th>
                        <th>Payment Fees</th>
                        <th>Payment Method</th>
                        <th>Appointment Time</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($payments as $payment) : ?>
                        <tr>
                            <td><?= htmlspecialchars($payment['invoiceNo']) ?></td>
                            <td><?= date('Y-m-d H:i:s', strtotime($payment['paymentDate'])) ?></td>
                            <td>$<?= number_format($payment['paymentFees'], 2) ?></td>
                            <td><?= htmlspecialchars($payment['paymentMethod']) ?></td>
                            <td><?= date('Y-m-d H:i:s', strtotime($payment['appointmentTime'])) ?></td>
                            <td>
                                <span>Paid</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else : ?>
                <p class="no-records">No paid payment records found.</p>
            <?php endif; ?>
        </div>
    </div>
    <br>
    <br>
    <br>

    <br><br>
    <br><br><br><br>
</body>

</html>
<?php include '../footer.php'; ?>