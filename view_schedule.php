<?php

session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST["logout"])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

include("db_connection.php");


$hospitalName = isset($_SESSION["hospitalName"]) ? $_SESSION["hospitalName"] : "";
$username = isset($_SESSION["username"]) ? $_SESSION["username"] : "";


$searchDoctorName = "";
$respond = "";


if (isset($_POST["search"])) {
    $searchDoctorName = $_POST["doctorName"];
}


$doctorQuery = "SELECT id, doctorName FROM doctor WHERE hospitalName = ?";
$stmt = $conn->prepare($doctorQuery);
$stmt->bind_param("s", $hospitalName);
$stmt->execute();
$doctorResult = $stmt->get_result();


if (isset($_POST["update_status"])) {
    $appointmentID = $_POST["appointmentID"];
    $newStatus = $_POST["respond"];

    $updateQuery = "UPDATE appointment SET status = ? WHERE appointmentID = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $newStatus, $appointmentID);
    $stmt->execute();
}

$query = "SELECT a.appointmentID, d.doctorName, p.name AS patientName, a.appointmentDate, a.appointmentTime, a.status
            FROM appointment a
            INNER JOIN patient p ON a.patientID = p.patientID
            INNER JOIN doctor d ON a.doctorID = d.id
            WHERE d.hospitalName = ?";

if (!empty($searchDoctorName)) {
    $query .= " AND d.id = ?";
}

$stmt = $conn->prepare($query);

if (!empty($searchDoctorName)) {
    $stmt->bind_param("si", $hospitalName, $searchDoctorName);
} else {
    $stmt->bind_param("s", $hospitalName);
}

$stmt->execute();
$result = $stmt->get_result();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Schedule</title>
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
            border: 1;
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
        <h2>View Schedule</h2>
        <form method="POST">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="doctorName">Search by Doctor Name:</label>
                    <select name="doctorName" id="doctorName" class="form-control">
                        <option value="">All Doctors</option>
                        <?php while ($row = $doctorResult->fetch_assoc()) { ?>
                            <option value="<?php echo $row["id"]; ?>"><?php echo $row["doctorName"]; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <button type="submit" name="search" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Doctor Name</th>
                    <th>Patient Name</th>
                    <th>Appointment Date</th>
                    <th>Appointment Time</th>
                    <th>Status</th>
                    <th>Respond</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <form method="POST">
                        <tr>
                            <td><?php echo $row["doctorName"]; ?></td>
                            <td><?php echo $row["patientName"]; ?></td>
                            <td><?php echo $row["appointmentDate"]; ?></td>
                            <td><?php echo $row["appointmentTime"]; ?></td>
                            <td><?php echo $row["status"]; ?></td>
                            <td>
                                <select name="respond" class="form-control">
                                    <option value="Appointment Successful">Appointment Successful</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                                <input type="hidden" name="appointmentID" value="<?php echo $row["appointmentID"]; ?>">
                                <button type="submit" name="update_status" class="btn btn-primary">Update</button>
                            </td>
                        </tr>
                    </form>
                <?php } ?>
            </tbody>
        </table>
        <div class="logout-btn">
            <form method="POST">
                <button type="submit" name="logout" class="btn btn-danger">Logout</button>
            </form>
        </div>
    </div>
</body>
</html>

