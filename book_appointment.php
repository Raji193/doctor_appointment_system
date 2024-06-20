<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: patient_login.php");
    exit();
}

include("db_connection.php");

$patientUsername = $_GET["patientUsername"];
$doctorID = $_GET["doctorID"];
$patientID = $_GET["patientID"];
$patientID = ""; 

$patientIDQuery = "SELECT patientID FROM patient WHERE username = ?";
$patientIDStmt = $conn->prepare($patientIDQuery);
$patientIDStmt->bind_param("s", $patientUsername);
$patientIDStmt->execute();
$patientIDResult = $patientIDStmt->get_result();

if ($patientIDResult->num_rows > 0) {
    $patientIDRow = $patientIDResult->fetch_assoc();
    $patientID = $patientIDRow["patientID"];
}
$doctorName = "";
$startTime = "";
$endTime = "";

$sql = "SELECT doctorName, startTime, endTime FROM doctor WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $doctorID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $doctorName = $row["doctorName"];
    $startTime = $row["startTime"];
    $endTime = $row["endTime"];
}


$appointmentDate = "";
$appointmentTime = "";

$validationError = "";
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointmentDate = $_POST["appointmentDate"];
    $appointmentTime = $_POST["appointmentTime"];

    
    $selectedDate = strtotime($appointmentDate);
    $tomorrow = strtotime('tomorrow');
    $nextMonth = strtotime('+1 month');

    if ($selectedDate < $tomorrow || $selectedDate > $nextMonth) {
        $validationError = "Invalid appointment date. Please select a date between tomorrow and within a month.";
    }

    if (empty($validationError)) {
        
        $slotTimestamp = strtotime($appointmentTime);
        $startTimestamp = strtotime($startTime);
        $endTimestamp = strtotime($endTime);

        if ($slotTimestamp < $startTimestamp || $slotTimestamp > $endTimestamp) {
            $validationError = "Invalid appointment time. Please select a time within the doctor's working hours.";
        } else {
            
            $checkSql = "SELECT COUNT(*) AS count FROM appointment WHERE doctorID = ? AND appointmentDate = ? AND appointmentTime = ?";
            $stmt = $conn->prepare($checkSql);
            $stmt->bind_param("sss", $doctorID, $appointmentDate, $appointmentTime);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $count = $row["count"];

            if ($count > 0) {
                $validationError = "The selected time slot is already booked. Please choose another slot.";
            } else {
                
                $status = "Booked";
                $insertSql = "INSERT INTO appointment (patientID, doctorID, appointmentDate, appointmentTime, status) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insertSql);
                $stmt->bind_param("sssss", $patientID, $doctorID, $appointmentDate, $appointmentTime, $status);

                if ($stmt->execute()) {
                    $successMessage = "Appointment booked successfully!";
                    $stmt->close();

                    
                    header("refresh:3;url=view_appointments.php?username=$patientUsername");
                } else {
                    $validationError = "Error: " . $conn->error;
                    $stmt->close();
                }
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
    <title>Book Appointment</title>
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

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="date"],
        input[type="radio"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .btn-primary {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .alert {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Book Appointment</h2>

        <?php if (!empty($validationError)) : ?>
            <div class="alert alert-danger">
                <?php echo $validationError; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($successMessage)) : ?>
            <div class="alert alert-success">
                <?php echo $successMessage; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="patientUsername">Patient Username</label>
                <input type="text" class="form-control" id="patientUsername" name="patientUsername" value="<?php echo $patientUsername; ?>" readonly>
            </div>
            <div class="form-group">
                <label for="doctorName">Doctor Name</label>
                <input type="text" class="form-control" id="doctorName" name="doctorName" value="<?php echo $doctorName; ?>" readonly>
            </div>
            <div class="form-group">
                <label for="appointmentDate">Appointment Date</label>
                <input type="date" class="form-control" id="appointmentDate" name="appointmentDate" required>
            </div>
            <div class="form-group">
                <label for="appointmentTime">Appointment Time</label>
                <select class="form-control" id="appointmentTime" name="appointmentTime" required>
                    
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Confirm Appointment</button>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            
            const now = new Date();
            now.setDate(now.getDate() + 1); 

            const minDate = now.toISOString().split('T')[0];
            const maxDate = new Date(now.getTime() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0]; // Add 30 days

            
            document.getElementById("appointmentDate").setAttribute("min", minDate);
            document.getElementById("appointmentDate").setAttribute("max", maxDate);

           
            const startTime = "<?php echo $startTime; ?>";
            const endTime = "<?php echo $endTime; ?>";
            function populateAppointmentSlots() {
                const select = document.getElementById("appointmentTime");
                select.innerHTML = "";

                const startTimestamp = new Date("1970-01-01 " + startTime).getTime();
                const endTimestamp = new Date("1970-01-01 " + endTime).getTime();
                const interval = 30 * 60 * 1000; 

                for (let time = startTimestamp; time < endTimestamp; time += interval) {
                    const timeString = new Date(time).toLocaleTimeString("en-US", { hour: '2-digit', minute: '2-digit' });
                    const option = document.createElement("option");
                    option.value = timeString;
                    option.text = timeString;

                    select.appendChild(option);
                }
            }

            
            populateAppointmentSlots();
        });
    </script>
</body>
</html>
