<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../images/dentalLogo.png" type="image/x-icon">
    <link rel="stylesheet" href="../style.css">
    <title>Manage User</title>
    <style>
        body{
            font-family: "Reem Kufi";
        }
        h1{
            text-align: center;
            margin-top: 50px;
        }
        form{
            align: center;
            display: flex;
            justify-content: center;
        }

        .search-user-input{
            border-radius: 5px;
            padding: 10px;
            width: 20%;
        }

        .insert-button,
        .search-btn{
            font-size: 15px;
            width: 100px;
            color: white;
            background-color: black;
            border: 1px solid white;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .insert-button:hover,
        .search-btn:hover{
            color: black;
            background-image: linear-gradient(135deg, #b2eff1 40%, #a58ef8 100%);
            box-shadow: 0 0 15px 5px rgba(255,255,255,0.75);
        }

        th,td{
            border: 2px solid #D0E4E8;
            text-align: center;
            padding: 10px;
            
        }

        table{
            display: table;
            align-item: center;
            border-collapse: collapse; 
            border: 2px solid #D0E4E8; 
            border-radius: 5px; 
            box-sizing: border-box;
            margin: 0 auto;
            width: 60%;

        }
        
        th{
            background-color: black;
            color: white;
        }

        td a{
            text-decoration: none;
        }

        tr:hover{
            background-color: #d0f3f5f7;
        }



    </style>
</head>
<body>
    <?php
        include('../config.php');
        include('Admin.php');
        $loggedInUserID = $_SESSION['userid']; 
        echo '<h1>List of All User</h1>'


    ?>

    <form class="search-user-form" action="search_user.php" method="post">
        <input class="search-user-input" type="text" name="name"> &nbsp &nbsp &nbsp;
        <input class="search-btn" type="submit" name="search" id="search" value="Search"/>
    </form>

    <?php
       $query = "SELECT u.userID, u.name, l.username, l.userlevel, l.status
                 FROM User u
                 JOIN Login l ON u.userID = l.fk_userid
                 ORDER BY l.userlevel, u.userID";
       $result = mysqli_query($conn, $query) or die(mysqli_connect_error());
       // Variable to track the row number
       $no = 1;


        // adding user button
        echo '<div style="display: inline-flex; margin-left: 1100px;">
        <a href="add_user.php"><button class="insert-button" style="display: width: 100px; font-size:18px;">Add</button></a>
        <br><br>
        </div>';

        echo '<div class="user-table">';
        echo '<table class = "bordered">
             <tr>
             <th>No</th>
             <th>Name</th>
             <th>Username</th>
             <th>Role</th>
             <th>Edit</th>
             <th>Deactivate/Activate Account</th>
             <th>View</th>
             </tr>';

             while(($row=mysqli_fetch_array($result))){
                echo '<tr>
                     <td>'.$no.'</td>
                     <td>'.$row['name'].'</td>
                     <td>'.$row['username'].'</td>
                     <td>';
                     if($row['userlevel'] == 1){
                        echo "Admin";
                     }
                     else if($row['userlevel'] == 2){
                        echo "Dentist";
                     }
                     else if($row['userlevel'] == 3){
                        echo "Patient";
                     }
                     echo '</td>
                     <td><a href="edit_user.php?id=' . $row['userID'] . '">Edit</a></td>';
                     if ($row['userID'] == $loggedInUserID) {
                        echo '<td>N/A</td>';
                     } else {
                        if ($row['status'] == 'Active') {
                            echo '<td><a href="deactivate_user.php?id=' . $row['userID'] . '">Deactivate</a></td>';
                        } else {
                            echo '<td><a href="activate_user.php?id=' . $row['userID'] . '">Activate</a></td>';
                        }
                     }
                     echo '<td><a href="view_user.php?id=' . $row['userID'] . '">View</a></td>
                     </tr>';

                     $no++;

                    }
             echo '</table>';
             echo '</div>';

             mysqli_close($conn);

             echo "<br> <br> <br>";

             include('../footer.php');

    ?>
</body>
</html>