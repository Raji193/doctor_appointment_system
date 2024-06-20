<?php
session_start();
include("db_connection.php");

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST["back"])) {
    header("Location: hospital_dashboard.php");
    exit();
}

$alertMessage = "";


$doctorId = "";
$doctorName = "";
$speciality = "";
$experience = "";
$qualification = "";
$designation = "";
$contactNumber = "";
$workingDays = [];
$startTime = "";
$endTime = "";

if (isset($_GET["id"])) {
    $doctorId = $_GET["id"];

    $stmt = $conn->prepare("SELECT * FROM doctor WHERE id = ?");
    $stmt->bind_param("i", $doctorId);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $doctorName = $row["doctorName"];
            $speciality = $row["speciality"];
            $experience = $row["experience"];
            $qualification = $row["qualification"];
            $designation = $row["designation"];
            $contactNumber = $row["contactNumber"];

            $workingDays = explode(', ', $row["workingDays"]);
            $startTimeFromDB = $row["startTime"];
            $endTimeFromDB = $row["endTime"];

            if (empty($startTimeFromDB)) {

                $startTime = "";
                $endTime = "";
            } else {
                $startTime = $startTimeFromDB;
                $endTime = $endTimeFromDB;
            }
        } else {
            $alertMessage = "Doctor not found.";
        }
    } else {
        $alertMessage = "Error retrieving doctor details.";
    }

    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doctorName = $_POST['doctorName'];
    $speciality = $_POST['speciality'];
    $experience = $_POST['experience'];
    $qualification = $_POST['qualification'];
    $designation = $_POST['designation'];
    $contactNumber = $_POST['contactNumber'];

    $workingDays = isset($_POST['workingDays']) ? $_POST['workingDays'] : [];
    $startTime = $_POST['startTime'];
    $endTime = $_POST['endTime'];

    $stmt = $conn->prepare("UPDATE doctor SET doctorName=?, speciality=?, experience=?, qualification=?, designation=?, contactNumber=?, workingDays=?, startTime=?, endTime=? WHERE id=?");
    $stmt->bind_param("sssssssssi", $doctorName, $speciality, $experience, $qualification, $designation, $contactNumber, $workingDaysStr, $startTime, $endTime, $doctorId);

    $workingDaysStr = implode(', ', $workingDays);

    if ($stmt->execute()) {
        $alertMessage = "Doctor details updated successfully.";
    } else {
        $alertMessage = "Error updating doctor details. Please try again.";
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Doctor</title>
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
            color: #0088cc;
            text-align: center;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        input[type="tel"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="checkbox"] {
            margin-right: 5px;
        }

        .form-group input[type="time"] {
            width: 150px;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .btn-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .btn-common {
            width: 150px;
            height: 40px;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
            transition: background-color 0.3s;
        }

        .btn-back {
            background-color: #d9534f;
            border: none;
            color: #fff;
        }

        .btn-back:hover {
            background-color: #c9302c;
        }

        .btn-update-doctor {
            background-color: #0088cc;
            border: none;
            color: #fff;
        }

        .btn-update-doctor:hover {
            background-color: #005580;
        }

        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Doctor</h2>
        <form method="POST">
            <div class="form-group">
                <label for="doctorName">Doctor Name</label>
                <input type="text" class="form-control" id="doctorName" name="doctorName" value="<?php echo $doctorName; ?>" required>
            </div>
            <div class="form-group">
                <label for="speciality">Speciality</label>
                <input type="text" class="form-control" id="speciality" name="speciality" value="<?php echo $speciality; ?>" required>
            </div>
            <div class="form-group">
                <label for="experience">Experience</label>
                <input type="number" class="form-control" id="experience" name="experience" value="<?php echo $experience; ?>" required>
            </div>
            <div class="form-group">
                <label for="qualification">Qualification</label>
                <input type="text" class "form-control" id="qualification" name="qualification" value="<?php echo $qualification; ?>" required>
            </div>
            <div class="form-group">
                <label for="designation">Designation</label>
                <input type="text" class="form-control" id="designation" name="designation" value="<?php echo $designation; ?>" required>
            </div>
            <div class="form-group">
                <label for="contactNumber">Contact Number</label>
                <input type="tel" class="form-control" id="contactNumber" name="contactNumber" value="<?php echo $contactNumber; ?>" required>
            </div>
            <div class="working-days-group">
                <label>Working Days:</label><br>
                <?php
                $daysOfWeek = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
                foreach ($daysOfWeek as $day) {
                ?>
                <input type="checkbox" name="workingDays[]" value="<?php echo $day; ?>" <?php if (in_array($day, $workingDays)) echo 'checked'; ?>> <?php echo $day; ?>
                <?php } ?>
            </div>
            <div class="working-hours-group">
                <label for="startTime">Start Time:</label>
                <?php
                if (empty($startTimeFromDB)) {
                ?>
                <input type="time" class="form-control time-input" id="startTime" name="startTime" required>
                <?php
                } else {
                ?>
                <input type="text" class="form-control" value="<?php echo $startTimeFromDB; ?>" readonly>
                <input type="hidden" name="startTime" value="<?php echo $startTimeFromDB; ?>">
                <?php
                }
                ?>
                <label for="endTime">End Time:</label>
                <?php
                if (empty($endTimeFromDB)) {
                ?>
                <input type="time" class="form-control time-input" id="endTime" name="endTime" required>
                <?php
                } else {
                ?>
                <input type="text" class="form-control" value="<?php echo $endTimeFromDB; ?>" readonly>
                <input type="hidden" name="endTime" value="<?php echo $endTimeFromDB; ?>">
                <?php
                }
                ?>
            </div>
            <div class="btn-box">
                <button type="submit" name="back" class="btn btn-secondary">Back to Dashboard</button>
                <button type="submit" class="btn btn-primary">Update Doctor</button>
            </div>
        </form>
        <?php if (!empty($alertMessage)): ?>
            <div class="alert alert-primary" role="alert">
                <?php echo $alertMessage; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
