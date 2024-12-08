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
    <!-- <link rel="stylesheet" href="addexpense.css"> -->
    <style>
        .search-wrapper {
            margin: 20px;
            font-family: Arial, sans-serif;
        }

        /* Main Container Styling */
        .search-container,
        #sort-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            margin-bottom: 20px;
        }

        /* Header Styling */
        h2 {
            font-size: 1.8rem;
            color: #222;
            margin-bottom: 10px;
            text-align: center;
        }

        /* Input and Select Styling */
        input[type="text"],
        input[type="date"],
        select {
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 15px;
            width: 300px;
            transition: border-color 0.3s ease;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        select {
            width: auto;
        }

        input[type="date"] {
            width: 200px;
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        select:focus {
            border-color: #007bff;
            outline: none;
        }

        /* Button Styling */
        button {
            padding: 10px 20px;
            font-size: 1rem;
            color: #000;
            background-color: #fff;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #fbfbfb;
        }

        button:focus {
            transform: scale(0.98);
            border: 1px solid #DE5F4C;
        }

        .print-button {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            border: none;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .print-button .material-icons {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        /* Label Styling */
        label {
            font-weight: bold;
            margin-right: 5px;
        }

        /* Approx Message */
        #approx-message {
            font-size: 0.9rem;
            font-style: italic;
        }

        /* Sort Container Specific */
        #sort-container {
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }


        .expenses-list-container {
            margin-top: 30px;
            max-width: 100%;
            max-height: 96vh;
            overflow-x: auto;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
            border-radius: 10px;
        }

        #expenses-search-table {
            width: 100%;
            border-collapse: collapse;
            /* margin-top: 10px; */
            background-color: #fff;
            border-radius: 10px;


        }

        #expenses-search-table th,
        #expenses-search-table td {
            border: 1px solid #ddd;
            /* border-radius: 10px; */
            padding: 10px;
            text-align: center;
        }

        #expenses-search-table th {
            background-color: #000;
            color: #fff;
            text-align: center;
        }

        #expenses-search-table tbody tr:hover {
            background-color: #f9f9f9;
        }
    </style>
</head>

<body>
    <!-- Sidebar structure -->
    <div class="sidebar">
        <div class="wrapper">
            <ul>
                <!-- Home Link -->
                <li>
                    <a href="dashboard.php"><span class="icon material-icons">home</span></a>
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
                    <span class="icon material-icons active">payments</span>
                    <span class="text active">Expense</span>
                    <div class="submenu">
                        <a href="add_expenses.php">Add Expenses</a>
                        <a href="view_expenses.php" class="active2">View Expenses</a>

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
    <div class="containerr">
        <div class="search-wrapper">
            <h2>View Expenses</h2>
            <div class="search-container">
                <!-- Select Filter -->
                <select id="filter-type">
                    <!-- <option value="category">Category</option> -->
                    <option value="amount">Amount</option>
                </select>

                <!-- Search Input -->
                <input type="text" id="search-input" placeholder="Enter search term" />

                <!-- Search Button -->
                <button id="search-btn">Search</button>
                <button type="button" id="search-btn" onclick="location.reload();">Reset</button>
                <a href="expensesfordown.php" style="color:#000; text-decoration: none;">
                    <button class="print-button">Print / Download</button>
                </a>
                <p id="approx-message" style="color: #555; margin-top: 10px;"></p>

            </div>
            <div id="sort-container">
                <label for="sort-category">Sort by Category:</label>
                <select id="sort-category">
                    <option value="All">All</option>
                    <option value="Materials">Materials</option>
                    <option value="Labor">Labor</option>
                    <option value="Utilities">Utilities</option>
                    <option value="Miscellaneous">Miscellaneous</option>
                </select>

                <label for="sort-date">Sort by Date:</label>
                <select id="sort-date">
                    <option value="desc">Descending (Newest First)</option>
                    <option value="asc">Ascending (Oldest First)</option>
                </select>

                <!-- Date Picker for Filtering -->
                <label for="filter-date">Select Date:</label>
                <input type="date" id="filter-date" />
            </div>
            <!-- Search Results Table -->
            <div class="expenses-list-container">
                <table id="expenses-search-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Filtered results will appear here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let allExpenses = []; // Array to store all fetched expenses

            // Fetch all expenses once
            $.ajax({
                url: 'fetch_expenses_list.php', // Backend script to fetch all data
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        allExpenses = response.data; // Store data for filtering
                        renderTable(allExpenses); // Render the complete table initially
                    } else {
                        console.error(response.message);
                        alert('Failed to load data.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(`AJAX Error: ${status}, ${error}`);
                    alert('Unable to load data.');
                }
            });

            // Filter data on search button click
            $('#search-btn').click(function() {
                const filterType = $('#filter-type').val(); // Selected filter type
                const searchTerm = $('#search-input').val().toLowerCase(); // User input

                $('#approx-message').text(''); // Clear previous message

                const filteredData = allExpenses.filter(expense => {
                    if (filterType === 'amount') {
                        // Convert input to a number and check if it's valid
                        const userAmount = parseFloat(searchTerm);
                        if (isNaN(userAmount)) return false;

                        // Allow for a tolerance range of ±50
                        const tolerance = 100;
                        const isApproxMatch =
                            Math.abs(parseFloat(expense.amount) - userAmount) <= tolerance;

                        // If there's a match, display an approximate result message
                        if (isApproxMatch) {
                            $('#approx-message').text(
                                `Showing results within ±${tolerance} of ₹${userAmount}`
                            );
                        }

                        return isApproxMatch;
                    } else {
                        // For category and date, use a case-insensitive includes check
                        return expense[filterType]?.toLowerCase().includes(searchTerm);
                    }
                });

                // Render the filtered table
                renderTable(filteredData);

                // Show a message if no results found
                if (filteredData.length === 0) {
                    $('#approx-message').text('No results found.');
                }
            });

            // Sort by category
            $('#sort-category').change(function() {
                const selectedCategory = $(this).val();

                if (selectedCategory === 'All') {
                    location.reload(); // Reload the page to display all data
                } else {
                    // Filter data by the selected category
                    const filteredData = allExpenses.filter(
                        expense => expense.category === selectedCategory
                    );

                    // Render the filtered table
                    renderTable(filteredData);

                    // Show a message if no results found
                    if (filteredData.length === 0) {
                        $('#expenses-search-table tbody').html(
                            '<tr><td colspan="5">No results found for the selected category.</td></tr>'
                        );
                    }
                }
            });

            // Sort by date
            $('#sort-date').change(function() {
                const selectedCategory = $('#sort-category').val();
                let filteredData = allExpenses;

                if (selectedCategory !== 'All') {
                    filteredData = allExpenses.filter(
                        expense => expense.category === selectedCategory
                    );
                }

                // Get selected date sort order
                const selectedDateSort = $(this).val();
                filteredData = sortByDate(filteredData, selectedDateSort);

                // Render the table with sorted data
                renderTable(filteredData);
            });

            // Date filter change
            $('#filter-date').change(function() {
                const selectedDate = $(this).val();
                let filteredData = allExpenses;

                if (selectedDate) {
                    // Filter expenses by selected date
                    filteredData = allExpenses.filter(expense => {
                        const expenseDate = new Date(expense.expense_date).toISOString().split('T')[0];
                        return expenseDate === selectedDate;
                    });
                }

                // Render the filtered table
                renderTable(filteredData);
            });

            // Function to render the table
            function renderTable(data) {
                const tableBody = $('#expenses-search-table tbody');
                tableBody.empty(); // Clear existing rows

                if (data.length === 0) {
                    tableBody.append('<tr><td colspan="5">No results found</td></tr>');
                    return;
                }

                data.forEach(expense => {
                    const row = `
                <tr>
                    <td>${expense.expense_id}</td>
                    <td>${expense.category}</td>
                    <td>₹${parseFloat(expense.amount).toFixed(2)}</td>
                    <td>${expense.description}</td>
                    <td>${expense.expense_date}</td>
                </tr>
            `;
                    tableBody.append(row);
                });
            }

            // Function to sort by date (ascending or descending)
            function sortByDate(data, sortOrder) {
                return data.sort((a, b) => {
                    const dateA = new Date(a.expense_date);
                    const dateB = new Date(b.expense_date);

                    if (sortOrder === 'asc') {
                        return dateA - dateB; // Ascending order (Oldest First)
                    } else {
                        return dateB - dateA; // Descending order (Newest First)
                    }
                });
            }

            function printPage() {
                window.print();
            }
        });
    </script>
</body>

</html>