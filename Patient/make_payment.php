<?php
include('../config.php');
include('Patient.php');

date_default_timezone_set('Asia/Kuala_Lumpur');
$userID = $_SESSION['userid'];

$appointmentID = $_POST['appointment_id'] ?? null;
$totalAmount = $_POST['total_amount'] ?? null;

$appointmentQuery = "SELECT * FROM Appointment WHERE appointmentID = '$appointmentID'";
$appointmentResult = mysqli_query($conn, $appointmentQuery);
$appointment = mysqli_fetch_assoc($appointmentResult);

if (!$appointment) {
    echo '<script>alert("Invalid appointment ID."); window.location.href="view_appointment.php";</script>';
    exit();
}

$patientQuery = "SELECT * FROM Patient WHERE userID = '$userID'";
$patientResult = mysqli_query($conn, $patientQuery);
$row_user = mysqli_fetch_assoc($patientResult);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $paymentMethod = $_POST['payment_method'];
    $paymentDate = date('Y-m-d H:i:s');
    $paymentFees = $appointment['servicePrice'];
    $paymentStatus = 'Paid';
    $invoiceNo = generateInvoiceNumber($conn); // Generate unique invoice number

    // Insert payment details into the Payment table
    $insertPaymentQuery = "INSERT INTO Payment (invoiceNo, paymentDate, paymentFees, paymentMethod, paymentStatus, appointmentID)
                           VALUES ('$invoiceNo', '$paymentDate', '$paymentFees', '$paymentMethod', '$paymentStatus', '$appointmentID')";
    $insertPaymentResult = mysqli_query($conn, $insertPaymentQuery);

    if ($insertPaymentResult) {
        // Update Appointment status to Paid
        $updateQuery = "UPDATE Payment SET paymentStatus = 'Paid' WHERE appointmentID = '$appointmentID'";
        $updateResult = mysqli_query($conn, $updateQuery);

        if ($insertPaymentResult) {
            if ($paymentMethod === 'Online Transfer') {
                header("Location: upload_proof.php?appointmentID=$appointmentID");
                
                exit();
            } else {
                // Handle cash payment directly
                $updateQuery = "UPDATE Appointment SET paymentStatus = 'Paid' WHERE appointmentID = '$appointmentID'";
                $updateResult = mysqli_query($conn, $updateQuery);
    
                if ($updateResult) {
                    $_SESSION['payment_success'] = true;
                    echo "<script>alert('Payment successful!'); window.location.href='view_appointment.php';</script>";
                    exit();
                }
            }
        } else {
            echo '<script>alert("Error processing payment. Please try again later.");</script>';
        }
    }
}

function generateInvoiceNumber($conn) {
    // Fetch the current maximum invoice number from the Payment table
    $result = mysqli_query($conn, "SELECT MAX(CAST(SUBSTRING(invoiceNo, 2) AS UNSIGNED)) AS max_id FROM Payment");
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];
    $next_id = $max_id + 1;
    return 'I' . str_pad($next_id, 3, '0', STR_PAD_LEFT);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Payment</title>
    <link rel="shortcut icon" href="../images/dentalLogo.png" type="image/x-icon">
    <link rel="stylesheet" href="patient_style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Signika+Negative:wght@300..700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Signika Negative', sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .outer-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .container {
            background-color: #E9F5FA;
            border: 2px solid #ccc;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            box-sizing: border-box;
        }

        .container h2 {
            color: black;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .form-group input[type="text"], 
        .form-group input[type="number"], 
        .form-group select {
            width: calc(100% - 12px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-submit-container {
            text-align: center;
        }

        .make-payment-button {
            font-size: 15px;
            width: 100%;
            color: white;
            background-image: linear-gradient(135deg, #b2eff1 40%, #a58ef8 100%);
            border: 1px solid white;
            padding: 15px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 20px;
            margin-left: 5px;
            margin-right: 5px;
        }

        .make-payment-button:hover {
            color: black;
            background-image: linear-gradient(135deg, #b2eff1 40%, #a58ef8 100%);
            box-shadow: 0 0 15px 5px rgba(255, 255, 255, 0.75);
        }

        .footer {
            text-align: center;
            margin-top: 20px;
        }

        .footer a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }

        .footer a:hover {
            color: #4CAF50;
        }
        
    </style>
</head>
<body>
    <div class="outer-container">
        <div class="container">
            <h2>Make Payment</h2>

            <form method="post" action="make_payment.php">
                <div class="form-group">
                    <label>Appointment ID</label>
                    <p><?= htmlspecialchars($appointment['appointmentID']); ?></p>
                </div>

                <div class="form-group">
                    <label>Payment Time</label>
                    <p><?php echo date('Y-m-d H:i:s'); ?></p>
                </div>

                <div class="form-group">
                    <label>Payment Fees</label>
                    <p><?= htmlspecialchars($appointment['servicePrice']); ?></p>
                </div>

                <div class="form-group">
                    <label>Payment Method</label>
                    <select name="payment_method" required>
                        <option value="">Select Payment Method</option>
                        <option value="Online Transfer">Online Transfer</option>
                        <option value="Cash">Cash</option>
                    </select>
                </div>

                <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($appointmentID); ?>">
                <input type="hidden" name="total_amount" value="<?= htmlspecialchars($totalAmount); ?>">

                <div class="form-submit-container">
                    <input class="make-payment-button" type="submit" name="submit" value="Pay Now" />
                    <a class="make-payment-button" href="view_appointment.php">Cancel</a>
                </div>
            </form>
        </div>
    </div>
        <br><br><br>
    <?php include '../footer.php'; ?>
</body>
</html>

