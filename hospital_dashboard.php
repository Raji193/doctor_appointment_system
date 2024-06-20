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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Dashboard</title>
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
            height: 100vh; 
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
            margin-bottom: 10px;
            max-width: 150px; 
        }

        h2 {
            color: #0088cc;
            margin-bottom: 20px;
            font-size: 28px;
            text-align: center; 
        }

        .welcome {
            text-align: left;
            margin-bottom: 10px;
        }

        .welcome p {
            margin-bottom: 10px;
        }

        .user-info {
            text-align: right;
            margin-bottom: 10px;
        }

        .user-info p {
            margin-bottom: 10px;
            display: inline-block; 
        }

        .logout-btn {
            text-align: center;
        }

        .logout-btn button {
            width: auto;
            font-size: 14px;
            padding: 5px 10px; 
        }

        .btn-box {
            text-align: center;
            margin-top: 40px;
        }

        .btn-row {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column; 
        }

        .btn-group {
            text-align: center;
            margin-top: 20px;
        }

        .btn-group .btn {
            width: 150px;
            height: 150px;
            margin: 10px;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .btn-primary {
            background-color: #0088cc;
            border: 2px solid #0088cc; 
            color: #fff; 
        }

        .btn-primary:hover {
            background-color: #005580;
            border: 2px solid #005580; 
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="logo.png" alt="Hospital Logo" class="logo">
        <h2>Doctor Appointment System</h2>
        <div class="row">
            <div class="col-md-6 welcome">
                <p>Welcome, <?php echo $_SESSION["hospitalName"]; ?></p>
            </div>
            <div class="col-md-6 user-info">
                <p>Logged In as <?php echo $_SESSION["username"]; ?></p>
            </div>
        </div>
        <div class="btn-box">
            <div class="btn-row">
            <div class="btn-group">
                <a href="view_doctors.php?username=<?php echo $_SESSION["username"]; ?>&hospitalName=<?php echo $_SESSION["hospitalName"]; ?>" class="btn btn-primary">View Doctors</a>
                <a href="add_doctors.php?username=<?php echo $_SESSION["username"]; ?>&hospitalName=<?php echo $_SESSION["hospitalName"]; ?>" class="btn btn-primary">Add Doctors</a>
                <a href="view_schedule.php?username=<?php echo $_SESSION["username"]; ?>&hospitalName=<?php echo $_SESSION["hospitalName"]; ?>" class="btn btn-primary">View Schedule</a>
            </div>
            </div>
            <div class="logout-btn">
                <form method="POST">
                    <button type="submit" name="logout" class="btn btn-danger">Logout</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>