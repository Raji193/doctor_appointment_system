<?php
session_start();
include("db_connection.php");


if (!isset($_SESSION["username"])) {
    echo "You are not logged in.";
    exit();
}


if (isset($_GET["id"])) {
    $doctorID = $_GET["id"];

    
    $stmt = $conn->prepare("DELETE FROM doctor WHERE ID = ?");
    $stmt->bind_param("i", $doctorID);

    if ($stmt->execute()) {
        header("Location: view_doctors.php");
        exit();
    } else {
        echo "Error deleting doctor: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
