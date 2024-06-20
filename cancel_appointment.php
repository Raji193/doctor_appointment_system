<?php
session_start();


if (!isset($_SESSION["username"])) {
    header("Location: patient_login.php");
    exit();
}
include("db_connection.php");

$confirmationMessage = "";
$errorMessage = "";

if (isset($_GET["appointmentID"])) {
    $appointmentID = $_GET["appointmentID"];

 
    $sql = "SELECT appointmentDate, appointmentTime FROM appointment WHERE appointmentID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $appointmentID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $appointmentDate = $row["appointmentDate"];
        $appointmentTime = $row["appointmentTime"];
    } else {
        $errorMessage = "Appointment not found.";
    }

 
    $today = date("Y-m-d");
    $selectedDate = strtotime($appointmentDate);
    if ($selectedDate < strtotime($today)) {
        $errorMessage = "You can only cancel future appointments.";
    }

    if (empty($errorMessage)) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            
            $cancelSql = "DELETE FROM appointment WHERE appointmentID = ?";
            $stmt = $conn->prepare($cancelSql);
            $stmt->bind_param("s", $appointmentID);

            if ($stmt->execute()) {
                $confirmationMessage = "Appointment on $appointmentDate at $appointmentTime has been canceled.";
                
                
                header("refresh:3;url=view_appointments.php");
            } else {
                $errorMessage = "Error canceling appointment: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Appointment</title>
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
            max-width: 500px;
        }

        .alert {
            margin-top: 20px;
        }

        .btn-danger, .btn-primary {
            width: 100px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Cancel Appointment</h1>

        <?php if (!empty($confirmationMessage)) : ?>
            <div class="alert alert-success">
                <?php echo $confirmationMessage; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)) : ?>
            <div class="alert alert-danger">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($confirmationMessage) && empty($errorMessage)) : ?>
            <p class="text-center">Are you sure you want to cancel the appointment on <?php echo $appointmentDate; ?> at <?php echo $appointmentTime; ?>?</p>
            <form method="POST">
                <button type="submit" class="btn btn-danger">Yes, Cancel</button>
                <a href="view_appointments.php" class="btn btn-primary">No, Go Back</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
