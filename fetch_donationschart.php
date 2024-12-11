<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

include('config.php');

// Check connection
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

// Fetch trends
// $trends = $conn->query("SELECT donation_date, SUM(donation_amount) as donation_amount FROM donations GROUP BY donation_date ORDER BY donation_date ASC")
$trends = $conn->query ("SELECT donation_date, donation_amount FROM donations ORDER BY donation_date ASC")->fetch_all(MYSQLI_ASSOC);

// Fetch top donors
$topDonors = $conn->query("SELECT donor_name as name, SUM(donation_amount) as donation_amount FROM donations GROUP BY donor_name ORDER BY donation_amount DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC);

// Fetch contribution ranges
$rangesQuery = "SELECT 
    CASE 
        WHEN donation_amount <= 100 THEN '0-100'
        WHEN donation_amount <= 500 THEN '101-500'
        WHEN donation_amount <= 1000 THEN '501-1000'
        WHEN donation_amount <= 2000 THEN '1001-2000'
        WHEN donation_amount <= 5000 THEN '2001-5000'
        WHEN donation_amount <= 10000 THEN '5001-10000'
        ELSE '10001+' 
    END AS `range`, 
    COUNT(*) AS count 
FROM donations 
GROUP BY `range`
ORDER BY 
    CASE 
        WHEN `range` = '0-100' THEN 1
        WHEN `range` = '101-500' THEN 2
        WHEN `range` = '501-1000' THEN 3
        WHEN `range` = '1001-2000' THEN 4
        WHEN `range` = '2001-5000' THEN 5
        WHEN `range` = '5001-10000' THEN 6
        ELSE 7
    END;
";

$ranges = [];
foreach ($conn->query($rangesQuery)->fetch_all(MYSQLI_ASSOC) as $row) {
    $ranges[$row['range']] = (int)$row['count'];
}


// Fetch payment methods
$paymentMethods = [];
foreach ($conn->query("SELECT payment_method, COUNT(*) as count FROM donations GROUP BY payment_method")->fetch_all(MYSQLI_ASSOC) as $row) {
    $paymentMethods[$row['payment_method']] = (int)$row['count'];
}

// Fetch average donation
$average = $conn->query("SELECT donation_date, AVG(donation_amount) as average FROM donations GROUP BY donation_date ORDER BY donation_date ASC")->fetch_all(MYSQLI_ASSOC);

// Combine all data
echo json_encode(['trends' => $trends, 'topDonors' => $topDonors, 'ranges' => $ranges, 'paymentMethods' => $paymentMethods, 'average' => $average]);

$conn->close();
