<?php
session_start();
include("db_connection.php");


if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$hospitalUsername = $_SESSION['username'];

$sql = "SELECT * FROM doctor WHERE hospitalUsername = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $hospitalUsername);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Doctors</title>
    
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

        .doctor-box {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 20px;
            margin: 10px;
            text-align: center;
        }

        .doctor-box h3 {
            color: #0088cc;
            margin-bottom: 10px;
        }

        .doctor-box img {
            max-width: 100px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .btn-group {
            margin-top: 10px;
        }

        .btn {
            margin-right: 5px;
        }

        .add-doctor-button {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Doctors List</h2>
        <div class="d-flex justify-content-between mb-3">
            <a href="hospital_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
            <a href="add_doctors.php" class="btn btn-success">Add Doctor</a>
        </div>
        <div class="row">
            <?php
            while ($row = $result->fetch_assoc()) {
                $doctorId = $row['id'];
                $doctorName = $row['doctorName'];
                $speciality = $row['speciality'];
                $doctorPhoto = $row['photoPath'];
            ?>

            <div class="col-md-4">
                <div class="doctor-box">
                    <img src="<?php echo $doctorPhoto; ?>" alt="<?php echo $doctorName; ?>'s Photo">
                    <h3><?php echo $doctorName; ?></h3>
                    <p><?php echo $speciality; ?></p>
                    <div class="btn-group">
                        <a href="edit_doctor.php?id=<?php echo $doctorId; ?>" class="btn btn-primary">Edit</a>
                        <a href="delete_doctor.php?id=<?php echo $doctorId; ?>" class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>

            <?php } ?>
        </div>

    </div>
</body>
</html>
