<?php
    $conn = mysqli_connect('localhost','DentalSystem','dental2024','dentaldb');
    if(!$conn){
        die("Unable to connect: " .mysqli_connect_error());
    }

?>