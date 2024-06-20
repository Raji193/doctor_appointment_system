<?php

include("db_connection.php");


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $hospitalName = $_POST['hospitalName'];
    $username = $_POST['username'];
    $hospitalAddress = $_POST['hospitalAddress'];
    $mobileNumber = $_POST['mobileNumber'];
    $mailID = $_POST['mailID'];
    $password = $_POST['password'];

    
    $checkQuery = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($checkQuery);

    if ($result->num_rows > 0) {
       
        echo "Username already exists. Please choose a different username.";
    } else {
    
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

       
        $sql = "INSERT INTO users (hospitalName, username, hospitalAddress, mobileNumber, mailID, password) 
                VALUES ('$hospitalName', '$username', '$hospitalAddress', '$mobileNumber', '$mailID', '$hashedPassword')";

        if ($conn->query($sql) === TRUE) {
      
            header("Location: hospital_dashboard.php?hospitalName=".$row["hospitalName"]."&username=".$username);
            exit();
        } else {

            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Signup</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <style>
        body {
            background: linear-gradient(to bottom, #0088cc, #003366) fixed;
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
        }

        .container {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            margin: 0 auto;
            margin-top: 20px;
        }

        .logo {
            width: 120px;
            height: 120px;
            margin-bottom: 20px;
        }

        h2 {
            color: #0088cc;
            margin-bottom: 20px;
            font-size: 28px;
        }

        .card-header {
            background-color: #0088cc;
            color: #fff;
            text-align: center;
            border: none;
            font-weight: bold; 
            font-size: 24px; 
        }

        .btn-primary {
            background-color: #0088cc;
            border: none;
            width: 100%;
            padding: 10px;
        }

        .btn-secondary {
            color: #0088cc; 
            border: none;
            width: 100%;
            padding: 10px;
            text-align: center;
            text-decoration: underline; 
            cursor: pointer; 
        }

        .btn-primary:hover {
            background-color: #005580;
        }

        .form-group label {
            color: #0088cc;
            font-weight: bold;
            display: block;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            width: 100%;
            margin-bottom: 10px;
            color: #333; 
        }
    </style>
   
    <link rel="icon" href="favicon.ico" type="image/x-icon">
</head>
<body>
    <div class="container">
        <div class="text-center">
            <img src="logo.png" alt="Hospital Logo" class="logo">
            <h2 style="white-space: nowrap;">Doctor&nbsp;Appointment&nbsp;System</h2>
        </div>
        <div class="card">
            <div class="card-header">
                Admin Signup
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label for="hospitalName">Hospital Name</label>
                        <input type="text" class="form-control" id="hospitalName" name="hospitalName" required>
                    </div>
                    <div class="form-group">
                        <label for="hospitalAddress">Hospital Address</label>
                        <input type="text" class="form-control" id="hospitalAddress" name="hospitalAddress" required>
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="mobileNumber">Mobile Number</label>
                        <input type="text" class="form-control" id="mobileNumber" name="mobileNumber" required>
                    </div>
                    <div class="form-group">
                        <label for="mailID">Mail ID</label>
                        <input type="email" class="form-control" id="mailID" name="mailID" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Signup</button>
                </form>
                Existing User ? <a href="login.php">Login</a>
            </div>
        </div>
    </div>
</body>
</html>
