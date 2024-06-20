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

$hospitalUsername = $_SESSION["username"];
$hospitalName = $_SESSION["hospitalName"]; 

$stmt = $conn->prepare("SELECT hospitalName FROM users WHERE username = ?");
$stmt->bind_param("s", $hospitalUsername);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hospitalName = $row["hospitalName"];
}

$stmt->close();

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
    if (isset($_FILES['doctorPhoto']) && $_FILES['doctorPhoto']['size'] > 0) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($_FILES['doctorPhoto']['name']);
        
        $check = getimagesize($_FILES['doctorPhoto']['tmp_name']);
        if ($check !== false) {
    
            if (move_uploaded_file($_FILES['doctorPhoto']['tmp_name'], $targetFile)) {
                $photoPath = $targetFile;
            } else {
                $alertMessage = "Error uploading photo. Please try again.";
            }
        } else {
            $alertMessage = "File is not an image. Please upload a valid image.";
        }
    } else {
        $photoPath = "uploads/no_image.png"; 
    }

$stmt = $conn->prepare("INSERT INTO doctor (doctorName, speciality, experience, qualification, designation, contactNumber, hospitalUsername, hospitalName, workingDays, startTime, endTime, photoPath) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)");

$stmt->bind_param("ssssssssssss", $doctorName, $speciality, $experience, $qualification, $designation, $contactNumber, $hospitalUsername, $hospitalName, $workingDaysStr, $startTime, $endTime, $photoPath);
$workingDaysStr = implode(', ', $workingDays);


    if ($stmt->execute()) {
        $alertMessage = "Doctor added successfully.";
    } else {
        $alertMessage = "Error adding doctor. Please try again.";
    }

    
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Doctor</title>
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

        .logo {
            display: block;
            margin: 0 auto;
            margin-bottom: 20px;
            max-width: 150px;
        }

        h2 {
            color: #0088cc;
            margin-bottom: 20px;
            font-size: 28px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
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

        .btn-add-doctor {
            background-color: #0088cc;
            border: none;
            color: #fff;
        }

        .btn-add-doctor:hover {
            background-color: #005580;
        }

        .working-days-group {
            margin-bottom: 20px;
        }

        .working-days-group label {
            font-weight: normal;
        }

        .working-days-group input[type="checkbox"] {
            margin-right: 5px;
        }

        .working-hours-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .time-input {
            width: 80px;
            padding: 5px;
        }

        .form-group label {
            color: #0088cc;
            font-weight: bold;
            display: block;
        }

        input[type="text"],
        input[type="number"],
        input[type="tel"] {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            width: 100%;
        }

        .time-input {
            width: 80px;
            padding: 5px;
        }

        .form-group input[type="file"] {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 5px;
            width: 100%;
        }

        .logout-btn {
            text-align: left;
            margin-top: 10px;
        }

        .form-group:first-child {
            margin-top: 0;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center mt-3">
            <img src="logo.png" alt="Hospital Logo" width="150">
            <h2 class="mt-3">Doctor Appointment System</h2>
        </div>

        <h2>Add Doctor</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="doctorName">Doctor Name</label>
                <input type="text" class="form-control" id="doctorName" name="doctorName" required>
            </div>
            <div class="form-group">
                <label for="speciality">Speciality</label>
                <input type="text" class="form-control" id="speciality" name="speciality" required>
            </div>
            <div class="form-group">
                <label for="experience">Experience</label>
                <input type="number" class="form-control" id="experience" name="experience" required>
            </div>
            <div class="form-group">
                <label for="qualification">Qualification</label>
                <input type="text" class="form-control" id="qualification" name="qualification" required>
            </div>
            <div class="form-group">
                <label for="designation">Designation</label>
                <input type="text" class="form-control" id="designation" name="designation" required>
            </div>
            <div class="form-group">
                <label for="contactNumber">Contact Number</label>
                <input type="tel" class="form-control" id="contactNumber" name="contactNumber" required>
            </div>
            <div class="form-group">
    <label for="hospitalName">Hospital Name</label>
    <input type="text" class="form-control" id="hospitalName" name="hospitalName" value="<?php echo $hospitalName; ?>" required readonly>
</div>
<div class="form-group">
    <label for="hospitalUsername">Hospital Username</label>
    <input type="text" class="form-control" id="hospitalUsername" name="hospitalUsername" value="<?php echo $hospitalUsername; ?>" required readonly>
</div>

            <div class="working-days-group">
                <label>Working Days:</label><br>
                <?php
                $daysOfWeek = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
                foreach ($daysOfWeek as $day) {
                ?>
                <input type="checkbox" name="workingDays[]" value="<?php echo $day; ?>"> <?php echo $day; ?>
                <?php } ?>
            </div>
            <div class="working-hours-group">
                <label for="startTime">Start Time:</label>
                <input type="time" class="form-control time-input" id="startTime" name="startTime" required>
                <label for="endTime">End Time:</label>
                <input type="time" class="form-control time-input" id="endTime" name="endTime" required>
            </div>
            <div class="form-group">
                <label for="doctorPhoto">Doctor Photo</label>
                <input type="file" class="form-control" id="doctorPhoto" name="doctorPhoto" accept="image/*">
            </div>
            <div class="row mt-3">
            <div class="col-md-6">
    <a href="hospital_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>
                <div class="col-md-6 text-right">
                    <button type="submit" class="btn btn-primary">Add Doctor</button>
                </div>
            </div>
        </form>
        <?php if (!empty($alertMessage)): ?>
        <div class="alert alert-primary" role="alert">
            <?php echo $alertMessage; ?>
        </div>
        <?php endif; ?>
    </div>
    <script>
        <?php if (!empty($alertMessage)): ?>
        alert("<?php echo $alertMessage; ?>");
        <?php endif; ?>
    </script>
</body>
</html>
