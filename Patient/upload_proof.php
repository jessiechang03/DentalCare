<?php
session_start();
include('../config.php');

date_default_timezone_set('Asia/Kuala_Lumpur');
$userID = $_SESSION['userid'];
$appointmentID = $_POST['appointment_id'] ?? null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $proofOfPayment = $_FILES['proof_of_payment']['name'];
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES["proof_of_payment"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["proof_of_payment"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo '<script>alert("File is not an image.");</script>';
        $uploadOk = 0;
    }

    if (file_exists($targetFile)) {
        echo '<script>alert("Sorry, file already exists.");</script>';
        $uploadOk = 0;
    }

    if ($_FILES["proof_of_payment"]["size"] > 5000000) {
        echo '<script>alert("Sorry, your file is too large.");</script>';
        $uploadOk = 0;
    }

    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo '<script>alert("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");</script>';
        $uploadOk = 0;
    } else
        echo "<script>alert('Proof of payment uploaded successfully!'); window.location.href='view_appointment.php';</script>";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Proof of Payment</title>
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
            text-align: center;
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

        .form-group input[type="file"] {
            width: calc(100% - 12px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-submit-container {
            text-align: center;
        }

        .upload-button {
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

        .upload-button:hover {
            color: black;
            background-image: linear-gradient(135deg, #b2eff1 40%, #a58ef8 100%);
            box-shadow: 0 0 15px 5px rgba(255, 255, 255, 0.75);
        }
    </style>
</head>
<body>
    <div class="outer-container">
        <div class="container">
            <h2>Upload Proof of Payment</h2>
            <p>Please scan the QR code with TNG and upload the proof of payment below:</p>
            <img src="../images/tng_qrpay.png" alt="TNG QR Code" style="width: 300px; height: 450px; margin-bottom: 20px;">

            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Proof of Payment</label>
                    <input type="file" name="proof_of_payment" required>
                </div>

                <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($appointmentID); ?>">

                <div class="form-submit-container">
                    <input class="upload-button" type="submit" name="submit" value="Upload Proof" />
                </div>
            </form>
        </div>
    </div>
</body>
</html>
