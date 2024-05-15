<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "prison_db";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve total number of users
$query_users = "SELECT COUNT(*) AS total_users FROM users";
$result_users = $conn->query($query_users);
$row_users = $result_users->fetch_assoc();
$total_users = $row_users['total_users'];


// Retrieve total number of visitors
$query_visitors = "SELECT COUNT(*) AS total_visitors FROM visitors";
$result_visitors = $conn->query($query_visitors);
$row_visitors = $result_visitors->fetch_assoc();
$total_visitors = $row_visitors['total_visitors'];


// Retrieve total number of inmates
$query_total_inmates = "SELECT COUNT(*) AS total_inmates FROM inmates";
$result_total_inmates = $conn->query($query_total_inmates);
$row_total_inmates = $result_total_inmates->fetch_assoc();
$total_inmates = $row_total_inmates['total_inmates'];

// Retrieve total number of inmates for each crime
$query_crime_inmates = "SELECT crime, COUNT(*) AS total_inmates FROM inmates GROUP BY crime";
$result_crime_inmates = $conn->query($query_crime_inmates);
$crime_inmates = array();

// Store the results in an associative array
while ($row = $result_crime_inmates->fetch_assoc()) {
    $crime = $row['crime'];
    $total_inmates_by_crime = $row['total_inmates'];
    $crime_inmates[$crime] = $total_inmates_by_crime;
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Prison Management System - Jail Information</title>
    <link rel="stylesheet" type="text/css" href="jail.css">
</head>
<body>
    <header>
       <h1>Jail Information</h1>
    </header><br>
    <h2>General Information</h2><br>
    <table>
        <tr>
            <th>Total Users</th>
            <td><?php echo $total_users; ?></td>
        </tr>
        <tr>
            <th>Total Visitors</th>
            <td><?php echo $total_visitors; ?></td>
        </tr>
        <tr>
            <th>Total Inmates</th>
            <td><?php echo $total_inmates; ?></td>
        </tr>
    </table><br>
        <h2>Inmates by Crime</h2>
    <table>
        <tr>
            <th>Crime</th>
            <th>Total Inmates</th>
        </tr>
        <?php foreach ($crime_inmates as $crime => $total_inmates): ?>
        <tr>
            <td><?php echo $crime; ?></td>
            <td><?php echo $total_inmates; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <a href="index.php">Back to Dashboard</a>
</body>
</html>
