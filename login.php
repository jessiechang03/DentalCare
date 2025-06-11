<?php
session_start();

// Database connection
$conn = mysqli_connect('localhost', 'DentalSystem', 'dental2024', 'dentaldb')
    or die('Unable to connect: ' . mysqli_connect_error());

if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['userlevel'])) {
    $myusername = $_POST['username'];
    $mypassword = md5($_POST['password']);
    $myuserlevel = $_POST['userlevel'];

    // Create SQL query to check if the user exists with the provided credentials and is active
    $sql = "SELECT * FROM Login WHERE username = ? AND password = ? AND userlevel = ? AND status = 'active'";

    // Prepare and execute the query
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('sss', $myusername, $mypassword, $myuserlevel);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_array(MYSQLI_ASSOC);
        $count = $result->num_rows;
    }

    // A matching row was found
    if ($count == 1) {
        // Set session variables
        $_SESSION['login'] = "YES";
        $_SESSION['level'] = $rows['userlevel'];
        $_SESSION['userid'] = $rows['fk_userid'];
        $_SESSION['username'] = $rows['username'];

        // Set a cookie with the username
        setcookie('username', $myusername, time() + (86400 * 30), "/"); // Expires in 30 days

        $userID = $_SESSION['userid'];
        $userQuery = "SELECT name FROM User WHERE userID = '$userID'";
        $userResult = mysqli_query($conn, $userQuery);
        if ($userRow = mysqli_fetch_assoc($userResult)) {
            $_SESSION['name'] = $userRow['name'];
        }

        // Check if the user is an admin
        if ($_SESSION['level'] == "1") {
            // Fetch the adminID associated with the logged-in admin
            $adminUsername = $_SESSION['username'];
            $adminQuery = "SELECT adminID FROM Admin WHERE userID = (SELECT fk_userid FROM Login WHERE username = '$adminUsername')";
            $adminResult = mysqli_query($conn, $adminQuery);
            if ($adminRow = mysqli_fetch_assoc($adminResult)) {
                $adminID = $adminRow['adminID'];

                // Set adminID in session
                $_SESSION['adminID'] = $adminID;

                // Redirect to the admin dashboard
                header("Location: Admin/Admin_homepage.php?userid=" . $_SESSION['userid']);
                exit();
            } else {
                // If adminID not found, redirect to login page
                $_SESSION['login'] = "NO";
                header("Location: login.html");
                exit();
            }
        } else if ($_SESSION['level'] == "2") {
            // Fetch the dentistID associated with the logged-in dentist
            $dentistUsername = $_SESSION['username'];
            $dentistQuery = "SELECT dentistID FROM Dentist WHERE userID = (SELECT fk_userid FROM Login WHERE username = ?)";
            if ($stmt = $conn->prepare($dentistQuery)) {
                $stmt->bind_param('s', $dentistUsername);
                $stmt->execute();
                $dentistResult = $stmt->get_result();
                if ($dentistRow = $dentistResult->fetch_assoc()) {
                    $dentistID = $dentistRow['dentistID'];

                    // Set dentistID in session
                    $_SESSION['dentistID'] = $dentistID;

                    // Redirect to the dentist dashboard
                    header("Location: Dentist/Dentist_homepage.php");
                    exit();
                } else {
                    // If dentistID not found, redirect to login page
                    $_SESSION['login'] = "NO";
                    header("Location: login.html");
                    exit();
                }
            }
        } else if ($_SESSION['level'] == "3") {
            header("Location: Patient/Patient_homepage.php");
            exit();
        }
    } else {
        $_SESSION['login'] = "NO";
        header("Location: login.html");
        exit();
    }
}
?>