<?php
session_start();


if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];
    

    include("db_connection.php");
    
   
    $query = "SELECT patientID FROM patient WHERE username = '$username'";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $patientID = $row["patientID"];
    }
}


$patientName = isset($_SESSION["patientName"]) ? $_SESSION["patientName"] : "";
$username = isset($_SESSION["username"]) ? $_SESSION["username"] : "";

$patientID = ""; 


if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];
    

    include("db_connection.php");
    
    
    $query = "SELECT patientID FROM patient WHERE username = '$username'";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $patientID = $row["patientID"];
        $_SESSION["patientID"] = $patientID;
    }
}



$patientName = isset($_SESSION["patientName"]) ? $_SESSION["patientName"] : "";
$username = isset($_SESSION["username"]) ? $_SESSION["username"] : "";
$patientID = isset($_SESSION["patientID"]) ? $_SESSION["patientID"] : "";


$patient_username = $username;

include("db_connection.php");

$doctorID = "";
$patientUsername = "";
$hospitalName = "";


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$query = "SELECT DISTINCT hospitalName FROM users";
$result = $conn->query($query);

if (!$result) {
    die("Error fetching hospital names: " . $conn->error);
}


$query = "SELECT patientID FROM patient WHERE username = '$username'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $patientID = $row["patientID"];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(to bottom, #0088cc, #003366) fixed;
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            max-width: 800px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .btn-danger {
            background-color: #d9534f;
            border-color: #d9534f;
        }

        .btn-danger:hover {
            background-color: #c9302c;
            border-color: #c12e2a;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
            color: #fff;
        }

        .btn-info:hover {
            background-color: #117a8b;
            border-color: #10707f;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
        }

        .doctor-box {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            margin-left: 10px;
            margin-right: 10px;
            margin-bottom: 20px;
            width: calc(33.33% - 20px);
            box-sizing: border-box;
        }

        .doctor-box:last-child {
            margin-right: 0;
        }

        .doctor-name {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .book-appointment-button {
            background-color: #0088cc;
            border: none;
            color: #fff;
            border-radius: 5px;
            padding: 5px 10px;
            text-decoration: none;
            display: block;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center mt-3">
            <img src="logo.png" alt="Hospital Logo" width="150">
            <h2 class="mt-3">Patient Dashboard</h2>
        </div>
        <div class="text-right">
            <p>Logged in as: <?php echo $patient_username; ?></p>
         
            <a href="view_appointments.php?username=<?php echo $patient_username; ?>&patientID=<?php echo $patientID; ?>" class="btn btn-primary">View Appointments</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
<form method="GET">
    <label for="hospitalSearch">Search by Hospital:</label>
    <select id="hospitalSearch" name="hospitalSearch">
        <option value="">All Hospitals</option>
        <?php
     
        include("db_connection.php");

    
        $query = "SELECT DISTINCT hospitalName FROM users";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $hospitalName = $row["hospitalName"];
                echo '<option value="' . $hospitalName . '">' . $hospitalName . '</option>';
            }
        }

        $conn->close(); 
        ?>
    </select>
    <input type="submit" value="Search">
</form>


        <div class="row">
            <?php
            include("db_connection.php");

            if (isset($_GET["hospitalSearch"])) {
                $hospitalSearch = $_GET["hospitalSearch"];
                $sql = "SELECT d.*, u.hospitalName 
                        FROM doctor d 
                        INNER JOIN users u ON d.hospitalUsername = u.username 
                        WHERE u.hospitalName LIKE '%$hospitalSearch%'";
            } else {
                $sql = "SELECT * FROM doctor";
            }

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $photoPath = $row["photoPath"];
                    $doctorName = $row["doctorName"];
                    $experience = $row["experience"];
                    $contactNumber = $row["contactNumber"];
                    $designation = $row["designation"];
                    $speciality = $row["speciality"];
                    $hospitalName = $row["hospitalName"];

                    echo '<div class="doctor-box">';
                    echo '<img src="' . $photoPath . '" class="card-img-top" alt="' . $doctorName . '">';
                    echo '<h2>' . $doctorName . '</h2>';
                    echo '<p>Experience: ' . $experience . ' years</p>';
                    echo '<p>Contact: ' . $contactNumber . '</p>';
                    echo '<p>Designation: ' . $designation . '</p>';
                    echo '<p>Speciality: ' . $speciality . '</p>';
                    echo '<p>Hospital: ' . $hospitalName . '</p>';
                    echo '<a href="book_appointment.php?doctorID=' . $row["id"] . '&patientUsername=' . $patient_username . '&patientID=' . $patientID . '" class="book-appointment-button">Book Appointment</a>';


                    echo '</div>';
                }
            } else {
                echo "No doctors found.";
            }

            $conn->close();
            ?>
        </div>
    </div>
</body>
</html>
