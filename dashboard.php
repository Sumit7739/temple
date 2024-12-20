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
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="style_main.css">
    <!-- <link rel="stylesheet" href="addexpense.css"> -->
    <style>
        .containerr {
            height: 95vh;
        }

        .wrapperr {
            display: flex;
            justify-content: space-around;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .donation-container,
        .expenses-container,
        .total-container,
        .remaining-container {
            background: #ffffff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .donation-container:hover,
        .expenses-container:hover,
        .total-container:hover,
        .remaining-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .donation-container h2,
        .expenses-container h2,
        .total-container h2,
        .remaining-container h2 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333333;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Amount styling */
        #donation-amount,
        #expenses-amount,
        #total-donations-count,
        #remaining-amount {
            font-size: 28px;
            font-weight: bold;
            margin-top: 10px;
        }

        #donation-amount {
            color: #4caf50;
            /* Green for donations */
        }

        #expenses-amount {
            color: #e53935;
            /* Red for expenses */
        }

        #remaining-amount {
            color: #1e88e5;
            /* Blue for remaining amount */
        }

        #total-donations-count {
            color: #ff5100;
        }



        #menu button {
            margin: 10px;
            padding: 8px 10px;
            background-color: #fff;
            border: 1px solid #ddd;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 24px;
            cursor: pointer;
        }

        #expensesChart {
            position: absolute;
            top: 21%;
            max-width: 47%;
            max-height: 700px;
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .chart {
            position: absolute;
            top: 21%;
            right: 30px;
            width: 48%;
            height: 700px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div class="wrapper">
            <ul>
                <li class="has-submenu">
                    <span class="icon material-icons active">home</span>
                    <span class="text active">Dashboard</span>
                    <div class="submenu">
                        <a href="dashboard.php">Home</a>
                        <a href="ledger.php">Ledger</a>
                    </div>
                </li>

                <li class="has-submenu">
                    <span class="icon material-icons">volunteer_activism</span>
                    <span class="text">Donations</span>
                    <div class="submenu">
                        <a href="add_donation.php">Add Donation.</a>
                        <a href="view_donations.php">View Donations</a>
                    </div>
                </li>


                <li class="has-submenu">
                    <span class="icon material-icons">payments</span>
                    <span class="text">Expense</span>
                    <div class="submenu">
                        <a href="add_expenses.php">Add Expenses</a>
                        <a href="view_expenses.php">View Expenses</a>

                    </div>
                </li>

                <li class="has-submenu">
                    <span class="icon material-icons">campaign</span>
                    <span class="text">Campaign</span>
                    <div class="submenu">
                        <a href="add_campaign.php">Add Campaign</a>
                        <a href="view_campaigns.php">View Campaigns</a>

                    </div>
                </li>


                <li class="has-submenu">
                    <span class="icon material-icons">bar_chart</span>
                    <span class="text">Reports</span>
                    <div class="submenu">
                        <a href="donations_report.php">Donations Report</a>
                        <a href="expenses_report.php">Expenses Report</a>
                    </div>
                </li>


                <li class="has-submenu">
                    <span class="icon material-icons">table_chart</span>
                    <span class="text">Tables</span>
                    <div class="submenu">
                        <a href="donations_table.php">Donation Table</a>
                        <a href="expenses_table.php">Expenses Table</a>
                    </div>
                </li>

                <li class="has-submenu">
                    <span class="icon material-icons">settings</span>
                    <span class="text">Settings</span>
                    <div class="submenu">
                        <a href="users.php">See Users</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </li>


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
        <div class="wrapperr">
            <div class="donation-section">
                <div class="total-container">
                    <h2>No of Donations</h2>
                    <p id="total-donations-count">Loading...</p>
                </div>
            </div>
            <div id="donation-section">
                <div class="donation-container">
                    <h2>Total Donations</h2>
                    <p id="donation-amount">Loading...</p>
                </div>
            </div>
            <div id="expenses-section">
                <div class="expenses-container">
                    <h2>Total Expenses</h2>
                    <p id="expenses-amount">Loading...</p>
                </div>
            </div>
            <div id="remaining-section">
                <div class="remaining-container">
                    <h2>Remaining Amount</h2>
                    <p id="remaining-amount">Loading...</p>
                </div>
            </div>
        </div>
        <div class="chart">
            <h2 style="text-align: center;">Donation Charts</h2>
            <br>
            <div id="menu">
                <button onclick="showChart('trendChartContainer')">Show Trends</button>
                <button onclick="showChart('topDonorsChartContainer')">Show Top Donors</button>
                <button onclick="showChart('rangesChartContainer')">Show Donation Ranges</button>
                <button onclick="showChart('paymentMethodChartContainer')">Show Payment Methods</button>
                <button onclick="showChart('averageChartContainer')">Show Average Donations</button>
            </div>
            <br>
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
        <div class="chart2">
            <canvas id="expensesChart"></canvas>
        </div>

        <footer>
            <p>Created by <a href="submitupdates.html">Sumit</a> </p>
            <p class="footer-text">© 2024 . All rights reserved.</p>
        </footer>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="exrep.js"></script>
    <script src="dnrep.js"></script>
    <script>
        $(document).ready(function() {
            let donations = 0;
            let expenses = 0;
            let totalDonationsCount = 0;

            // Fetch Donations
            $.ajax({
                url: 'fetch_donation.php', // Backend script for donations
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        donations = parseFloat(response.total_donations || 0);
                        totalDonationsCount = parseInt(response.total_count || 0);
                        $('#donation-amount').text(`₹${donations.toFixed(2)}`);
                        $('#total-donations-count').text(totalDonationsCount); // Update total donations count
                        calculateRemaining(); // Calculate remaining amount after fetching donations
                    } else {
                        $('#donation-amount').text('Error fetching data');
                        console.error(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    $('#donation-amount').text('Unable to load data');
                    console.error(`AJAX Error: ${status}, ${error}`);
                }
            });

            // Fetch Expenses
            $.ajax({
                url: 'fetch_expenses.php', // Backend script for expenses
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        expenses = parseFloat(response.total_expenses || 0);
                        $('#expenses-amount').text(`₹${expenses.toFixed(2)}`);
                        calculateRemaining(); // Calculate remaining amount after fetching expenses
                    } else {
                        $('#expenses-amount').text('Error fetching data');
                        console.error(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    $('#expenses-amount').text('Unable to load data');
                    console.error(`AJAX Error: ${status}, ${error}`);
                }
            });

            // Function to Calculate and Display Remaining Amount
            function calculateRemaining() {
                const remaining = donations - expenses;
                $('#remaining-amount').text(`₹${remaining.toFixed(2)}`);
            }
        });

        document.querySelectorAll('.has-submenu').forEach(item => {
            item.addEventListener('click', () => {
                const submenu = item.querySelector('.submenu');
                if (submenu.style.display === "block") {
                    submenu.style.display = "none";
                } else {
                    submenu.style.display = "block";
                }
            });
        });
    </script>
</body>

</html>