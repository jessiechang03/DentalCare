<?php
include('../config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $appointmentID = $_POST['appointmentID'];
    $appointmentStatus = $_POST['appointmentStatus'];

    $query = "UPDATE appointment SET appointmentStatus = ? WHERE appointmentID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $appointmentStatus, $appointmentID);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: appointment_list.php');
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>