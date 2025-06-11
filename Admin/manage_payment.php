<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../images/dentalLogo.png" type="image/x-icon">
    <title>Manage Payment</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body{
            font-family: "Reem Kufi";
        }
        h1{
            text-align: center;
            margin-top: 50px;
        }
        table{
            border-collapse: collapse;
            width: 80%;
            margin: 0 auto;
        }
        th, td{
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        th{
            background-color: #f2f2f2;
        }
       
        .insert-button {
            font-size: 15px;
            width: 100px;
            color: white;
            background-color: #6AA78D;
            border: 1px solid white;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-align: center;
            display: block;
            margin: 20px auto;
            text-decoration: none;
        }
        .insert-button:hover {
            background-color: #4F7967;
        }
        tr:hover{
            background-color: #E1FFF2;
        }
        a{
            text-decoration: none;
            color: blue; /* Ensure links are visible */
        }
        a:hover{
            color: red; /* Darken link color on hover */
        }
    </style>
</head>
<body>
<?php
    // Include database configuration
    include('../config.php');
    include('Admin.php');
    
    echo "<h1>Payment List</h1>";

    // Retrieve all payment records
    $query = "SELECT * FROM Payment";
    $result = mysqli_query($conn, $query) or die(mysqli_error($conn));


    // Display table header
    echo '<table>
            <tr>
                <th>Invoice No</th>
                <th>Payment Date</th>
                <th>Payment Fees</th>
                <th>Payment Status</th>
                <th>Payment Method</th>
            </tr>';

    // Fetch and display each payment record
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['invoiceNo']) . "</td>";
        echo "<td>" . htmlspecialchars($row['paymentDate']) . "</td>";
        echo "<td>" . htmlspecialchars($row['paymentFees']) . "</td>";
        echo "<td>" . htmlspecialchars($row['paymentStatus']) . "</td>";
        echo "<td>" . htmlspecialchars($row['paymentMethod']) . "</td>";
        echo "</tr>";
    }

    // Close table
    echo '</table>';
    echo '<br><br><br><br>';
    // Include footer and close database connection
    
    mysqli_close($conn);
    include('../footer.php');
?>
</body>
</html>
