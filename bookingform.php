<?php
// ---------- DB CONNECTION ----------
$servername = "127.0.0.1:3308";
$username   = "root";
$password   = "";
$dbname     = "travelscapes";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ---------- GET PARAMETERS ----------
$hotelId = isset($_GET['hotel_id']) ? (int)$_GET['hotel_id'] : 0;
$cityId  = isset($_GET['city_id'])  ? (int)$_GET['city_id']  : 0;

if ($hotelId === 0 || $cityId === 0) {
    die("Invalid booking link. Hotel / City not found.");
}

// ---------- FETCH HOTEL ----------
$hotelSql = "SELECT * FROM hotels WHERE hotelid = $hotelId";
$hotelRes = $conn->query($hotelSql);
if ($hotelRes->num_rows === 0) {
    die("Hotel not found.");
}
$hotelRow = $hotelRes->fetch_assoc();
$hotelName = $hotelRow['hotel'];
$baseCostPerDay = (int)$hotelRow['cost'];   // cost per tourist per day (or just per tourist)

// ---------- FETCH CITY ----------
$citySql = "SELECT city FROM cities WHERE cityid = $cityId";
$cityRes = $conn->query($citySql);
$cityName = ($cityRes->num_rows > 0) ? $cityRes->fetch_assoc()['city'] : "Unknown";

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Form</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #78C7D4, #005273);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .wrapper {
            background: #ffffff;
            max-width: 900px;
            width: 100%;
            margin: 20px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.25);
            display: flex;
            overflow: hidden;
        }
        .details {
            flex: 1;
            background: #0c2833;
            color: #ffffff;
            padding: 25px 30px;
        }
        .details h2 {
            margin-top: 0;
        }
        .details p {
            margin: 8px 0;
        }
        .form-section {
            flex: 1;
            padding: 25px 30px;
        }
        .form-section h2 {
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 12px;
        }
        label {
            display: block;
            margin-bottom: 4px;
            font-size: 14px;
        }
        input[type="text"],
        input[type="number"],
        input[type="date"],
        input[type="tel"] {
            width: 100%;
            padding: 8px 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        .total-cost {
            margin-top: 10px;
            font-weight: bold;
        }
        .btn-submit {
            margin-top: 15px;
        }
        .btn-submit button {
            background-color: #6064b6;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
        }
        .btn-submit button:hover {
            background-color: #48508f;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <!-- LEFT: BOOKING DETAILS -->
    <div class="details">
        <h2>Booking Details</h2>
        <p><strong>City:</strong> <?php echo htmlspecialchars($cityName); ?></p>
        <p><strong>Hotel:</strong> <?php echo htmlspecialchars($hotelName); ?></p>
        <p><strong>Base Cost per Tourist:</strong> Rs. <?php echo $baseCostPerDay; ?></p>
        <p style="margin-top:15px;font-size:13px;opacity:0.8;">
            Total Cost = Base Cost × Number of Tourists
        </p>
    </div>

    <!-- RIGHT: FORM -->
    <div class="form-section">
        <h2>Booking Form</h2>
        <!-- send to payment page -->
        <form method="post" action="/Travelscapes/save_booking.php">

            <!-- keep hotel/city hidden if you want to use later -->
            <input type="hidden" name="hotel_id" value="<?php echo $hotelId; ?>">
            <input type="hidden" name="city_id" value="<?php echo $cityId; ?>">
            <input type="hidden" name="base_cost" id="baseCostHidden" value="<?php echo $baseCostPerDay; ?>">

            <div class="form-group">
                <label for="name">Name:</label>
                <input id="name" type="text" name="name" required>
            </div>

            <div class="form-group">
                <label for="username">Username:</label>
                <input id="username" type="text" name="username" required>
            </div>

            <div class="form-group">
                <label for="tourists">Number of Tourists:</label>
                <input id="tourists" type="number" name="tourists" min="1" value="1" required>
            </div>

            <div class="form-group">
                <label for="dob">Date of Birth:</label>
                <input id="dob" type="date" name="dob" required>
            </div>

            <div class="form-group">
                <label for="contact">Contact Number:</label>
                <input id="contact" type="tel" name="contact" required>
            </div>

            <p class="total-cost">
                Total Cost: Rs. <span id="totalCost"><?php echo $baseCostPerDay; ?></span>
            </p>

            <div class="btn-submit">
                <button type="submit">Proceed for Payment</button>
            </div>
        </form>
    </div>
</div>

<script>
// JS to calculate Total Cost = base cost * number of tourists
const baseCost   = <?php echo $baseCostPerDay; ?>;
const touristsEl = document.getElementById('tourists');
const totalEl    = document.getElementById('totalCost');

function updateCost() {
    const t = parseInt(touristsEl.value) || 0;
    totalEl.textContent = baseCost * t;
}
touristsEl.addEventListener('input', updateCost);
updateCost(); // initial call
</script>
</body>
</html>
