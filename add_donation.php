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
    <link rel="stylesheet" href="adddonate.css">
    <style>
        .containerr{
            overflow: auto;
        }
        .donation-container {
            justify-content: center;
            gap: 20px;
            padding: 20px;
            overflow: auto;
        }

        .donation-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 10px;
            /* transition: transform 0.2s; */
        }

        .card-header {
            font-size: 18px;
            font-weight: bold;
            color: #fff;
            margin-bottom: 10px;
        }

        .card-detail {
            font-size: 18px;
            color: #555;
            margin-bottom: 8px;
        }

        .card-detail span {
            font-weight: bold;
            color: #333;
        }

        .card-remarks {
            font-style: italic;
            color: #777;
            margin-top: 10px;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        .imp {
            color: red;
            /* font-weight: bold; */
            font-size: 24px;
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
                    <span class="icon material-icons active">volunteer_activism</span>
                    <span class="text active">Donations</span>
                    <div class="submenu">
                        <a href="add_donation.php" class="active2">Add Donation.</a>
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
    <div class="containerr">
        <div class="container">
            <h2>Record Donation</h2>
            <form id="donationForm">
                <div class="form-group">
                    <label for="donorName">Donor Name<span class="imp"> **</span></label>
                    <input type="text" id="donorName" name="donorName" placeholder=" " required>
                </div>
                <div class="form-group">
                    <label for="mobileNumber">Mobile Number<span class="imp"> **</span></label>
                    <input type="tel" id="mobileNumber" name="mobileNumber" placeholder=" " required>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" placeholder=" " required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder=" ">
                </div>
                <div class="form-group">
                    <label for="donationAmount">Donation Amount (₹):<span class="imp"> **</span></label>
                    <input type="number" id="donationAmount" name="donationAmount" placeholder=" " required>
                </div>
                <div class="form-group">
                    <label for="paymentMethod">Payment Method<span class="imp"> **</span></label>
                    <select id="paymentMethod" name="paymentMethod" required>
                        <option value="" disabled selected>Select Payment Method</option>
                        <option value="Online">Online</option>
                        <option value="Cash">Cash</option>
                        <option value="Check">Check</option>
                        <option value="Wallet">Wallet</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="transactionReference">Transaction Reference ID</label>
                    <input type="text" id="transactionReference" name="transactionReference" placeholder=" ">
                </div>
                <div class="form-group">
                    <label for="donationDate">Date of Donation<span class="imp"> **</span></label>
                    <input type="date" id="donationDate" name="donationDate" required>
                </div>
                <div class="form-group">
                    <label for="remarks">Remarks</label>
                    <textarea id="remarks" name="remarks" placeholder=" "></textarea>
                </div>
                <button type="button" id="submitDonation">Submit Donation</button>
            </form>
            <p id="responseMessage" style="text-align: center; margin-top: 15px; font-size:24px;"></p>
        </div>

        <div class="container container2 donation-container" id="donationContainer">
            <br>
            <h2>View Latest Donations Added</h2>
            <br>

        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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

        $(document).ready(function() {
            $('#submitDonation').on('click', function() {
                const formData = {
                    donorName: $('#donorName').val().trim(),
                    mobileNumber: $('#mobileNumber').val().trim(),
                    donationAmount: $('#donationAmount').val().trim(),
                    paymentMethod: $('#paymentMethod').val(),
                    donationDate: $('#donationDate').val().trim(),
                    email: $('#email').val().trim(),
                    address: $('#address').val().trim(),
                    transactionReference: $('#transactionReference').val().trim(),
                    remarks: $('#remarks').val().trim(),
                };

                // Frontend validation: Check required fields
                if (!formData.donorName || !formData.mobileNumber || !formData.donationAmount || !formData.paymentMethod || !formData.donationDate) {
                    $('#responseMessage').text('Please fill in all required fields (Name, Mobile, Amount, Payment Method, and Date).').css('color', 'red');
                    return;
                }

                // Check for valid mobile number format (optional validation)
                const mobileRegex = /^[0-9]{10}$/;
                if (formData.mobileNumber && !mobileRegex.test(formData.mobileNumber)) {
                    $('#responseMessage').text('Please enter a valid 10-digit mobile number.').css('color', 'red');
                    return;
                }

                // Check for valid donation amount (optional validation)
                if (formData.donationAmount && isNaN(formData.donationAmount)) {
                    $('#responseMessage').text('Please enter a valid donation amount.').css('color', 'red');
                    return;
                }

                $.ajax({
                    url: 'save_donation.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        console.log(response); // Log the response for debugging
                        if (response.success) {
                            $('#responseMessage').text(response.message).css('color', 'green');
                            $('#donationForm')[0].reset();
                        } else {
                            $('#responseMessage').text(response.message).css('color', 'red');
                        }
                        setTimeout(function() {
                            location.reload();
                        }, 3000);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error); // Log the error details
                        $('#responseMessage').text('An error occurred while saving the donation.').css('color', 'red');
                    },
                });
            });
        });

        $(document).ready(function() {
            $.ajax({
                url: 'fetch_donations.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const donations = response.data;
                        const container = $('#donationContainer');

                        donations.forEach(donation => {
                            const card = `
                        <div class="donation-card">
                            <div class="card-header">${donation.donor_name}</div>
                            <div class="card-detail"><span>Mobile:</span> ${donation.mobile_number}</div>
                            <div class="card-detail"><span>Email:</span> ${donation.email || 'N/A'}</div>
                            <div class="card-detail"><span>Address:</span> ${donation.address || 'N/A'}</div>
                            <div class="card-detail"><span>Amount:</span> ₹${donation.donation_amount}</div>
                            <div class="card-detail"><span>Payment Method:</span> ${donation.payment_method}</div>
                            <div class="card-detail"><span>Transaction Reference:</span> ${donation.transaction_reference || 'N/A'}</div>
                            <div class="card-detail"><span>Date:</span> ${donation.donation_date}</div>
                            <div class="card-detail"><span>Status:</span> ${donation.status}</div>
                            <div class="card-detail"><span>Purpose:</span> ${donation.purpose}</div>
                            <div class="card-detail"><span>Anonymous:</span> ${donation.isanonymous ? 'Yes' : 'No'}</div>
                            <div class="card-detail"><span>Remarks:</span> ${donation.remarks || 'None'}</div>
                            <div class="card-detail"><span>Acknowledgment Sent:</span> ${donation.acknowledgment_sent == 1 ? 'Yes' : 'No'}</div>
                            <div class="card-detail"><span>Campaign:</span> ${donation.campaign_id || 'None'}</div>
                        </div>
                    `;
                            container.append(card);
                        });
                    } else {
                        console.error(response.message);
                    }
                },
                error: function() {
                    console.error('Failed to fetch donation records.');
                }
            });
        });
    </script>
</body>

</html>