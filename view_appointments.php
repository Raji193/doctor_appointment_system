<?php
session_start();


if (!isset($_SESSION["username"])) {
    header("Location: patient_login.php");
    exit();
}

include("db_connection.php");
$patientUsername = $_SESSION["username"];


$patientID = "";
$sql = "SELECT patientID FROM patient WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $patientUsername);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $patientID = $row["patientID"];
}


$today = date("Y-m-d");
$sql = "SELECT appointmentID, doctorID, appointmentDate, appointmentTime, status FROM appointment WHERE patientID = ? AND appointmentDate >= ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $patientID, $today);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointments</title>
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

        h2 {
            text-align: center;
        }

        .appointment-list {
            list-style-type: none;
            padding: 0;
        }

        .appointment-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin: 10px 0;
            background-color: #fff;
        }

        .btn-danger {
            background-color: #d9534f;
            border-color: #d9534f;
        }

        .btn-danger:hover {
            background-color: #c9302c;
            border-color: #c12e2a;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your Upcoming Appointments</h2>
        <ul class="appointment-list">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $appointmentID = $row["appointmentID"];
                    $doctorID = $row["doctorID"];
                    $appointmentDate = $row["appointmentDate"];
                    $appointmentTime = $row["appointmentTime"];
                    $status = $row["status"];
                    ?>

                    <li class="appointment-item">
                        <div>
                            <p>Doctor: <?php echo $doctorID; ?></p>
                            <p>Date: <?php echo $appointmentDate; ?></p>
                            <p>Time: <?php echo $appointmentTime; ?></p>
                            <p>Status: <?php echo $status; ?></p>
                        </div>
                        <a href="cancel_appointment.php?appointmentID=<?php echo $appointmentID; ?>" class="btn btn-danger">Cancel</a>
                    </li>

                    <?php
                }
            } else {
                echo "<p>No upcoming appointments found.</p>";
            }
            ?>
        </ul>
        <a href="patient_dashboard.php" class="btn btn-primary">Back</a>
    </div>
</body>
</html>
