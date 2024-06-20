<?php
session_start();
include("db_connection.php");

$username = $password = "";
$error = "";


if (isset($_SESSION["username"])) {
    header("Location: patient_dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = $_POST["username"];
    $password = $_POST["password"];



    
    $query = "SELECT * FROM patient WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        
        $_SESSION["username"] = $username;
        header("Location: patient_dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password. Please try again.";
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
    <title>Patient Login</title>

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
            max-width: 400px;
            margin: 0 auto;
            margin-top: 20px;
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

        .btn-login {
            background-color: #0088cc;
            border: none;
            color: #fff;
        }

        .btn-login:hover {
            background-color: #005580;
        }

        .error-message {
            color: red;
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
            <h2 class="mt-3">Patient Login</h2>
        </div>
        <?php if (!empty($error)): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="btn-box">
                <a href="index.html" class="btn btn-back">Back to Home</a>
                <button type="submit" class="btn btn-login">Login</button>
            </div>
        </form>
    </div>
</body>
</html>
