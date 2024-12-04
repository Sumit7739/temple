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
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css"> -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="style_main.css">
</head>

<body>
    <div class="containerr">
        <!-- Sidebar structure -->
        <div class="sidebar">
            <div class="wrapper">
                <ul>
                    <!-- Home Link -->
                    <li>
                        <span class="icon material-icons active">home</span>
                        <span class="text active">Home</span>
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
                        <span class="icon material-icons">bar_chart</span>
                        <span class="text">Reports</span>
                        <div class="submenu">
                            <a href="donations_report.php">Donations Report</a>
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


        <div class="content">
            <h3>Welcome <?php echo $name; ?></h3>
            <!-- <h4>Role - <?php echo $role; ?> </h4> -->
        </div>

        <div class="dashboard">
            <!-- Inventory Card -->
            <!-- <div class="card">
                <div class="card-header">
                    <h4>Inventory</h4>
                </div>
                <div class="card-body">
                    <p><strong>Entries Today:</strong> <?php echo $inventoryToday; ?></p>
                    <p><strong>Total Entries This Week:</strong> <?php echo $inventoryThisWeek; ?></p>
                </div>
            </div> -->
            <!-- 2nd table -->
            <!-- <div class="card">
                <div class="card-header">
                    <h4>2nd Inventory Table</h4>
                </div>
                <div class="card-body">
                    <p><strong>Entries Today:</strong> <?php echo $moreinventoryToday; ?></p>
                    <p><strong>Total Entries This Week:</strong> <?php echo $moreinventoryThisWeek; ?></p>
                </div>
            </div> -->

            <!-- Sales Card -->
            <!-- <div class="card">
                <div class="card-header">
                    <h4>Sales</h4>
                </div>
                <div class="card-body">
                    <p><strong>Entries Today:</strong> <?php echo $salesToday; ?></p>
                    <p><strong>Total Entries This Week:</strong> <?php echo $salesThisWeek; ?></p>
                </div>
            </div> -->
            <!-- 2nd sales table -->
            <!-- <div class="card">
                <div class="card-header">
                    <h4>2nd Sales Table</h4>
                </div>
                <div class="card-body">
                    <p><strong>Entries Today:</strong> <?php echo $moresalesToday; ?></p>
                    <p><strong>Total Entries This Week:</strong> <?php echo $moresalesThisWeek; ?></p>
                </div>
            </div> -->
        </div>

        <h2>Quick Links Section</h2>
        <br>
        <div class="quick-links">
            <div class="link-item">
                <!-- <h4>Inventory</h4> -->
                <!-- <a href="add_inventory.php">Add Inventory</a>
                <a href="view_inventory.php">View Inventory</a> -->
            </div>
            <div class="link-item">
                <!-- <h4>Sales</h4> -->
                <!-- <a href="sales.php">Add Sales</a>
                <a href="view_sales.php">View Sales</a> -->
            </div>
        </div>

        <!-- <footer>
            <p>Created by <a href="submitupdates.html">Sumit</a> </p>
            <p class="footer-text">© 2024 . All rights reserved.</p>
        </footer> -->


        <script src="manage_system.js"></script>
        <script>
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