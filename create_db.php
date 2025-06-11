<?php
    $conn = mysqli_connect('localhost','DentalSystem','dental2024');
    if(!$conn){
        die('Unable to connect: ' .mysqli_connect_error());
    }

    // create database
    if(mysqli_query($conn, "CREATE DATABASE dentaldb")){
        echo "Database is created successfully.";
    }
    else{
        echo "Error to create database: " .mysqli_connect_error();
    }
    mysqli_close($conn);
?>