<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "fire_alarm";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $temperature = $_POST['temperature'];
    $humidity = $_POST['humidity'];
    $flame = $_POST['flame'];
    $smoke = $_POST['smoke'];
    $status = $_POST['status'];

    $sql = "INSERT INTO sensor_data (temperature, humidity, flame, smoke, status) VALUES ('$temperature', '$humidity', '$flame', '$smoke', '$status')";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Data received"]);
    } else {
        echo json_encode(["error" => "Failed to save data"]);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $sql = "SELECT * FROM sensor_data ORDER BY timestamp DESC LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "No data found"]);
    }
}

$conn->close();
?>
