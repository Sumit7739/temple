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
    <link rel="stylesheet" href="addexpense.css">
    <style>
        .wrapper{
            margin-bottom: 20px;
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
                    <span class="icon material-icons active">payments</span>
                    <span class="text active">Expense</span>
                    <div class="submenu">
                        <a href="add_expenses.php" class="active2">Add Expenses</a>
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
    <div class="containerr">
        <div class="wrapper">
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

        <div class="container">
            <h1>Add Expense</h1>
            <form action="process_expense.php" method="POST" enctype="multipart/form-data">
                <!-- Expense Category -->
                <label for="category">Expense Category</label>
                <select id="category" name="category" required>
                    <option value="Materials">Materials</option>
                    <option value="Labor">Labor</option>
                    <option value="Utilities">Utilities</option>
                    <option value="Miscellaneous">Miscellaneous</option>
                </select>

                <!-- Amount Spent -->
                <label for="amount">Amount Spent</label>
                <input type="number" id="amount" name="amount" placeholder="Enter amount" required min="0" step="0.01">

                <!-- Description -->
                <label for="description">Description/Remarks</label>
                <textarea id="description" name="description" placeholder="Enter details" rows="4"></textarea>

                <label for="date">Expense Date:</label>
                <input type="date" id="date" name="date" required>


                <!-- Attachment Upload -->
                <label for="attachment">Attachment (Optional)</label>
                <input type="file" id="attachment" name="attachment" accept=".jpg, .jpeg, .png, .pdf">
                <div id="preview">
                    <!-- Preview will be displayed here -->
                </div>

                <!-- Submit Button -->
                <button type="submit">Submit</button>
            </form>
        </div>
        <div id="expenses-section">
            <div class="expenses-list-container">
                <h2>Expenses List</h2>
                <table id="expenses-table">
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
                        <!-- Rows will be dynamically appended here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let donations = 0;
            let expenses = 0;

            // Fetch Donations
            $.ajax({
                url: 'fetch_donation.php', // Backend script for donations
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        donations = parseFloat(response.total_donations || 0);
                        $('#donation-amount').text(`₹${donations.toFixed(2)}`);
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
        $(document).ready(function() {
            // Fetch expenses data
            $.ajax({
                url: 'fetch_expenses_list.php', // Backend script for fetching expenses list
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Clear the table body
                        const tableBody = $('#expenses-table tbody');
                        tableBody.empty();

                        // Append each expense as a row in the table
                        response.data.forEach(expense => {
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
                    } else {
                        console.error(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(`AJAX Error: ${status}, ${error}`);
                }
            });
        });
        // JavaScript for displaying selected attachment preview
        const attachmentInput = document.getElementById('attachment');
        const previewDiv = document.getElementById('preview');

        attachmentInput.addEventListener('change', function() {
            previewDiv.innerHTML = ''; // Clear existing preview

            const file = this.files[0];
            if (file) {
                const fileType = file.type;
                const validImageTypes = ['image/jpeg', 'image/png', 'image/jpg'];

                // Check if the file is an image
                if (validImageTypes.includes(fileType)) {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.alt = 'Attachment Preview';
                    img.style.maxWidth = '50%';
                    img.style.marginTop = '10px';
                    previewDiv.appendChild(img);
                } else if (fileType === 'application/pdf') {
                    const pdfPreview = document.createElement('p');
                    pdfPreview.textContent = `Selected File: ${file.name}`;
                    pdfPreview.style.marginTop = '10px';
                    previewDiv.appendChild(pdfPreview);
                } else {
                    const errorText = document.createElement('p');
                    errorText.textContent = 'Unsupported file type.';
                    errorText.style.color = 'red';
                    previewDiv.appendChild(errorText);
                }
            }
        });
    </script>
</body>

</html>