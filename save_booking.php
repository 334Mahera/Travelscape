<?php
$conn = new mysqli("127.0.0.1:3308", "root", "", "travelscapes");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$hotelid   = $_POST['hotel_id'];
$cityid    = $_POST['city_id'];
$name      = $_POST['name'];
$username  = $_POST['username'];
$tourists  = $_POST['tourists'];
$dob       = $_POST['dob'];
$contact   = $_POST['contact'];
$baseCost  = $_POST['base_cost'];

$totalCost = $baseCost * $tourists;

$sql = "INSERT INTO bookings (hotelid, cityid, name, username, tourists, dob, contact, total_cost)
        VALUES ('$hotelid', '$cityid', '$name', '$username', '$tourists', '$dob', '$contact', '$totalCost')";

if ($conn->query($sql) === TRUE) {
    header("Location: Payment Interface/payment.html");
    exit();
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
