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

$dentalRecordQuery = "
    SELECT DentalRecord.recordID, DentalRecord.recordDate, DentalRecord.symptoms, DentalRecord.treatment, DentalRecord.history, User.name AS dentistName, Payment.paymentStatus
    FROM DentalRecord
    LEFT JOIN Dentist ON DentalRecord.dentistID = Dentist.dentistID
    LEFT JOIN User ON Dentist.userID = User.userID
    LEFT JOIN Payment ON DentalRecord.recordID = Payment.recordID
    WHERE DentalRecord.patientID = '$patientID'
    ORDER BY DentalRecord.recordDate DESC
";
$dentalRecordResult = mysqli_query($conn, $dentalRecordQuery);
if (!$dentalRecordResult) {
    die('Failed to retrieve dental records: ' . mysqli_error($conn));
}

$dentalRecords = mysqli_fetch_all($dentalRecordResult, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dental Record History</title>
    <link rel="shortcut icon" href="../images/dentalLogo.png" type="image/x-icon">
    <link rel="stylesheet" href="patient_style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Signika+Negative:wght@300..700&display=swap" rel="stylesheet">

    <style>
        .payment-button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
            transition: background-color 0.3s;
        }

        .payment-button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="outer-container">
        <div class="container">
            <h2>Dental Record History</h2>
            <?php if (count($dentalRecords) > 0) : ?>
                <table>
                    <tr>
                        <th>Record ID</th>
                        <th>Record Date</th>
                        <th>Symptoms</th>
                        <th>Treatment</th>
                        <th>History</th>
                        <th>Dentist</th>
                    </tr>
                    <?php foreach ($dentalRecords as $record) : ?>
                        <tr>
                            <td><?= htmlspecialchars($record['recordID']) ?></td>
                            <td><?= date('Y-m-d H:i:s', strtotime($record['recordDate'])) ?></td>
                            <td><?= htmlspecialchars($record['symptoms']) ?></td>
                            <td><?= htmlspecialchars($record['treatment']) ?></td>
                            <td><?= htmlspecialchars($record['history']) ?></td>
                            <td><?= htmlspecialchars($record['dentistName']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else : ?>
                <p>No dental records found.</p>
            <?php endif; ?>
        </div>
    </div>
    <br><br><br><br><br><br><br><br><br>
    
</body>
</html>
<?php include '../footer.php'; ?>