<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is not logged in
if (!isset($_SESSION['id'])) {
    // Redirect to the login page or any other appropriate page
    header('Location: login.php');
    exit();
}

include('config.php');

$userID = $_SESSION['id'];

// Fetch user's name
$sql = "SELECT name FROM users WHERE id = '$userID'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['name'];
} else {
    $name = "User";
}


// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donations Reports</title>
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css"> -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="style_main.css">
    <style>
        .chart-container {
            width: 95%;
            /* max-height: 96%; */
            margin: 10px auto;
            margin-top: 15px;
            display: none;
            background-color: #fff;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        #menu button {
            margin-left: 50px;
            padding: 15px 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 24px;
            cursor: pointer;
        }

        .summary {
            /* text-align: center; */
            /* margin-top: 20px; */
            /* margin-bottom: 20px; */
            font-size: 1.2em;
        }
        
    </style>
</head>

<body>
    <div class="sidebar">
        <div class="wrapper">
            <ul>
                <!-- Home Link -->
                <li>
                    <span class="icon material-icons">home</span>
                    <span class="text"><a href="dashboard.php">Home</a></span>
                </li>

                <!-- Inventory Section with Submenu -->
                <li class="has-submenu">
                    <span class="icon material-icons">volunteer_activism</span>
                    <span class="text">Donations</span>
                    <div class="submenu">
                        <a href="add_donation.php">Add Donation.</a>
                        <a href="view_donations.php">View Donations</a>
                    </div>
                </li>

                <!-- Sales Section with Submenu -->
                <li class="has-submenu">
                    <span class="icon material-icons">payments</span>
                    <span class="text">Expense</span>
                    <div class="submenu">
                        <a href="add_expenses.php">Add Expenses</a>
                        <a href="view_expenses.php">View Expenses</a>

                    </div>
                </li>

                <!-- Stock Section (Placeholder for future use) -->
                <li class="has-submenu">
                    <span class="icon material-icons">campaign</span>
                    <span class="text">Campaign</span>
                    <div class="submenu">
                        <a href="add_campaign.php">Add Campaign</a>
                        <a href="view_campaigns.php">View Campaigns</a>

                    </div>
                </li>

                <!-- Reports Link -->
                <li class="has-submenu">
                    <span class="icon material-icons active">bar_chart</span>
                    <span class="text active">Reports</span>
                    <div class="submenu">
                        <a href="donations_report.php" class="active2">Donations Report</a>
                        <a href="expenses_report.php">Expenses Report</a>
                    </div>
                </li>

                <!-- Tables Link -->
                <li class="has-submenu">
                    <span class="icon material-icons">table_chart</span>
                    <span class="text">Tables</span>
                    <div class="submenu">
                        <a href="donations_table.php">Donation Table</a>
                        <a href="expenses_table.php">Expenses Table</a>
                    </div>
                </li>

                <!-- Settings Section with Submenu -->
                <li class="has-submenu">
                    <span class="icon material-icons">settings</span>
                    <span class="text">Settings</span>
                    <div class="submenu">
                        <a href="users.php">See Users</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </li>

                <!-- Help Link -->
                <li class="has-submenu">
                    <span class="icon material-icons">info</span>
                    <span class="text">Extra</span>
                    <div class="submenu">
                        <a href="notifications.php">Notifications</a>
                        <a href="view_query.php">Query</a>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <div class="containerr">
        <div id="menu">
            <button onclick="showChart('trendChartContainer')">Show Trends</button>
            <button onclick="showChart('topDonorsChartContainer')">Show Top Donors</button>
            <button onclick="showChart('rangesChartContainer')">Show Donation Ranges</button>
            <button onclick="showChart('paymentMethodChartContainer')">Show Payment Methods</button>
            <button onclick="showChart('averageChartContainer')">Show Average Donations</button>
        </div>

        <div id="trendChartContainer" class="chart-container" style="display: block;">
            <canvas id="trendChart"></canvas>
            <div id="trendSummary" class="summary"></div>
        </div>

        <div id="topDonorsChartContainer" class="chart-container" style="display: none;">
            <canvas id="topDonorsChart"></canvas>
            <div id="topDonorsSummary" class="summary"></div>
        </div>

        <div id="rangesChartContainer" class="chart-container" style="display: none;">
            <canvas id="rangesChart"></canvas>
            <div id="rangesSummary" class="summary"></div>
        </div>

        <div id="paymentMethodChartContainer" class="chart-container" style="display: none;">
            <canvas id="paymentMethodChart"></canvas>
            <div id="paymentMethodSummary" class="summary"></div>
        </div>

        <div id="averageChartContainer" class="chart-container" style="display: none;">
            <canvas id="averageChart"></canvas>
            <div id="averageSummary" class="summary"></div>
        </div>
    </div>
    <script src="dnrep.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>

</html>