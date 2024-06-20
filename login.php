<?php

include("db_connection.php");


if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $username = $_POST["username"];
    $password = $_POST["password"];

  
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row["password"];

        
        if (password_verify($password, $hashedPassword)) {
           
            session_start();
            $_SESSION["hospitalName"] = $row["hospitalName"];
            $_SESSION["username"] = $username;
            header("Location: hospital_dashboard.php?hospitalName=".$row["hospitalName"]."&username=".$username);
            exit();
        } else {
           
            $loginError = "Invalid username or password. Please try again.";
        }
    } else {
    
        $loginError = "Invalid username or password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
   
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
            margin-top: 100px; 
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

        .btn-primary:hover {
            background-color: #005580;
        }

        .form-group label {
            color: #0088cc;
            font-weight: bold;
            display: block;
        }

        input[type="text"],
        input[type="password"] {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            width: 100%;
            margin-bottom: 10px;
            color: #333; 
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center">
            <img src="logo.png" alt="Hospital Logo" class="logo">
            <h2 style="white-space: nowrap;">Doctor&nbsp;Appointment&nbsp;System</h2>
        </div>
        <div class="card">
            <div class="card-header">
                Admin Login
            </div>
            <div class="card-body">
                <form method="POST">
                    <?php if (isset($loginError)) : ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $loginError; ?>
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>
                </form>
                New User? <a href="signup.php">Sign Up</a>
            </div>
        </div>
    </div>
</body>
</html>
