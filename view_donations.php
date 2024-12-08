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
    <!-- <link rel="stylesheet" href="adddonate.css"> -->
    <style>
        body {
            overflow: auto;
        }

        .container {
            padding: 10px;
            border-radius: 10px;
            width: 100%;
            height: 100%;
            margin: 5px;
        }

        .donation-container {
            display: flex;
            flex-wrap: wrap;
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
            min-width: 45%;
            font-size: 24px;
            height: auto;
            max-height: 650px;
            /* transition: transform 0.2s; */
        }

        .card-header {
            background-color: #53a2a9;
            display: flex;
            justify-content: space-between;
            font-size: 28px;
            font-weight: bold;
            color: #000;
            margin-bottom: 10px;
        }

        .card-header span {
            cursor: pointer;
            /* color: #333; */
            font-size: 34px;
            transition: color 0.3s ease;
        }

        #del:hover {
            color: red;
        }

        #edit:hover {
            color: #de5f4c;
        }


        .card-detail {
            display: flex;
            justify-content: space-between;
            /* font-size: 18px; */
            color: #555;
            margin-bottom: 8px;
        }

        .card-detail span {
            font-weight: bold;
            /* font-size: 24px; */
            color: #333;
        }

        .card-remarks {
            font-style: italic;
            /* font-size: 24px; */
            color: #777;
            margin-top: 10px;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        /* Search Box Styles */
        .search-box {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            padding: 5px;
        }

        .search-box a {
            background: #fff;
            padding: 10px 15px;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 24px;
            color: red;
            font-size: 22px;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        #searchCriteria {
            width: 200px;
            padding: 15px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 14px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        #searchCriteria:focus {
            outline: none;
            border: 1px solid #ddd;
            box-shadow: 0 0 15px rgba(0, 123, 255, 0.6);
        }

        #searchInput {
            flex: 1;
            max-width: 50%;
            padding: 15px 15px;
            font-size: 18px;
            border: 1px solid #ddd;
            border-radius: 24px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        #searchInput:focus {
            border: 1px solid #ddd;
            outline: none;
            box-shadow: 0 0 15px rgba(0, 123, 255, 0.6);
            /* Removes default outline for cleaner appearance */
        }

        /* Modal styles */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.6);
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 40%;
            border-radius: 10px;
            text-align: center;
            font-size: 30px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }

        #confirmDelete {
            font-size: 18px;
            font-weight: bold;
            color: #fff;
            padding: 15px 18px;
            background-color: #de5f4c;
            cursor: pointer;
            border: 1px solid #ddd;
            border-radius: 24px;
        }

        #cancelDelete {
            font-size: 18px;
            font-weight: bold;
            color: #fff;
            padding: 15px 18px;
            background-color: #53a2a9;
            cursor: pointer;
            border-radius: 24px;
            border: 1px solid #ddd;
        }

        #confirmDelete:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            transition: box-shadow 0.3s ease;
        }

        #cancelDelete:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            transition: box-shadow 0.3s ease;
        }

        .modal-content .note {
            margin-top: 30px;
            font-size: 18px;
            color: red;
        }

        .popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: auto;
        }

        .popup-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            background-color: rgba(234, 239, 239, 0.85);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.125);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
            padding: 20px;
            border-radius: 10px;
            width: 70%;
            height: auto;
        }

        .mainform {
            display: flex;
            flex-wrap: wrap;
        }

        .form1 {
            width: 45%;
            margin: 10px;
        }

        .form2 {
            width: 45%;
            margin: 10px;
            margin-left: 20px;
        }

        .popup-content form label {
            display: block;
            margin-top: 10px;
            font-size: 18px;
            font-weight: bold;
            color: #333
        }

        .popup-content form input,
        .popup-content form textarea {
            width: 100%;
            margin-top: 5px;
            padding: 8px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
            font-size: 18px;
            color: #333;
        }

        .popup-content form select {
            width: 100%;
            padding: 15px 18px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background-color: #f9f9fb;
            outline: none;
        }

        .popup-content form button {
            margin-top: 15px;
            padding: 15px 20px;
            font-size: 1em;
            border-radius: 24px;
            border: none;
            color: white;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .popup-content form button#saveChanges {
            background-color: #4CAF50;
        }


        .popup-content form button#cancelEdit {
            background-color: #f44336;
        }

        .popup-content form button:hover {
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.5);
            transition: box-shadow 0.3s ease;
        }

        .btn {
            background-color: #fff;
            font-size: 18px;
            font-weight: 600;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 24px;
            margin-top: 20px;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }

        .btn:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .print-button {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            border: none;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
            background-color: #fff;
        }

        .print-button .material-icons {
            margin-right: 10px;
            font-size: 1.2rem;
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
                        <a href="add_donation.php">Add Donation.</a>
                        <a href="view_donations.php" class="active2">View Donations</a>
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
        <div class="search-box">
            <!-- Dropdown for search criteria -->
            <select id="searchCriteria">
                <option value="donor_name">Name</option>
                <option value="mobile_number">Mobile</option>
                <option value="email">Email</option>
                <option value="donation_amount">Donation Amount</option>
                <option value="payment_method">Payment Method</option>
                <option value="transaction_reference">Transaction Reference</option>
                <option value="donation_date">Date</option>
                <option value="status">Status</option>
                <option value="purpose">Purpose</option>
                <option value="isanonymous">Anonymous</option>
                <option value="remarks">Remarks</option>
                <option value="acknowledgment_sent">Acknowledgment Sent</option>
                <option value="campaign_id">Campaign</option>
            </select>
            <!-- Input for search text -->
            <input type="text" id="searchInput" placeholder="Type to Search donations...">
            <a href="view_donations.php">clear</a>

            <a href="donationfordown.php" style="color:#000; text-decoration: none;">
                <button class="print-button">Print / Download</button>
            </a>
        </div>
        <br>
        <h2 style="text-align: center; font-size:28px;">View Latest Donations Added</h2>
        <br>
        <div class="container container2 donation-container" id="donationContainer">
        </div>
        <!-- Modal for Delete Confirmation -->
        <div id="deleteModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <p>Are you sure you want to delete this record?</p>
                <br>
                <button id="confirmDelete" class="btn btn-danger">Delete</button>
                <button id="cancelDelete" class="btn btn-secondary">Cancel</button>
                <br>
                <p class="note">Note: This cannot be undone.</p>
            </div>
        </div>
        <div id="editPopup" class="popup" style="display: none;">
            <div class="popup-content">
                <h2>Edit Donation</h2>
                <form id="editForm" class="mainform">
                    <div class="form1">
                        <input type="hidden" id="editId" name="id">
                        <label for="editDonorName">Donor Name:</label>
                        <input type="text" id="editDonorName" name="donorName" required>

                        <label for="editMobileNumber">Mobile Number:</label>
                        <input type="text" id="editMobileNumber" name="mobileNumber" required>

                        <label for="editEmail">Email:</label>
                        <input type="email" id="editEmail" name="email">

                        <label for="editAddress">Address:</label>
                        <input type="text" id="editAddress" name="address">

                        <label for="editDonationAmount">Amount:</label>
                        <input type="number" id="editDonationAmount" name="donationAmount" required>

                        <label for="editPaymentMethod">Payment Method:</label>
                        <select name="paymentMethod" id="editPaymentMethod" required>
                            <option value="Online">Online</option>
                            <option value="Cash">Cash</option>
                            <option value="Check">Check</option>
                            <option value="Wallet">Wallet</option>
                            <option value="Other">Other</option>
                        </select>

                        <label for="editTransactionReference">Transaction Reference:</label>
                        <input type="text" id="editTransactionReference" name="transactionReference">
                    </div>
                    <div class="form2">

                        <label for="editDonationDate">Date:</label>
                        <input type="date" id="editDonationDate" name="donationDate" disabled>

                        <label for="editStatus">Status:</label>
                        <select id="editStatus" name="status">
                            <option value="Successful">Successful</option>
                            <option value="Pending">Pending</option>
                            <option value="Failed">Failed</option>
                            <option value="Rejected">Rejected</option>
                        </select>

                        <label for="editPurpose">Purpose:</label>
                        <input id="editPurpose" name="purpose">

                        <label for="editIsAnonymous">Anonymous:</label>
                        <select name="isanonymous" id="editIsAnonymous">
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                        <label for="editAcknowledgementSent">Acknowledgment Sent: <span style="color: red; margin-left:10px; font-weight:200;">Updates Automatically</span></label>
                        <select name="editAcknowledgementSent" id="editAcknowledgementSent" disabled>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                        <label for="editCampaign">Campaign ID</label>
                        <input type="text" id="editCampaign" name="campaign_id" disabled>

                        <label for="editRemarks">Remarks:</label>
                        <textarea id="editRemarks" name="remarks"></textarea>

                        <button type="button" id="saveChanges">Save Changes</button>
                        <button type="button" id="cancelEdit">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            // Fetch donation records and populate donation cards
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
                            <div class="donation-card" data-id="${donation.id}">
                                <div class="card-header donor_name">${donation.donor_name}<span class="icon material-icons" id="del" data-id="${donation.id}">delete</span>
        <span class="icon material-icons" id="edit" data-id="${donation.id}">edit</span></div>
                                <div class="card-detail mobile_number"><span>Mobile:</span> ${donation.mobile_number}</div>
                                <div class="card-detail email"><span>Email:</span> ${donation.email || 'N/A'}</div>
                <div class="card-detail address"><span>Address:</span> ${donation.address || 'N/A'}</div>
                                <div class="card-detail donation_amount"><span>Amount:</span> ₹${donation.donation_amount}</div>
                                <div class="card-detail payment_method"><span>Payment Method:</span> ${donation.payment_method}</div>
                                <div class="card-detail transaction_reference"><span>Transaction Reference:</span> ${donation.transaction_reference || 'N/A'}</div>
                                <div class="card-detail donation_date"><span>Date:</span> ${donation.donation_date}</div>
                                <div class="card-detail status"><span>Status:</span> ${donation.status}</div>
                                <div class="card-detail purpose"><span>Purpose:</span> ${donation.purpose}</div>
                                <div class="card-detail isanonymous"><span>Anonymous:</span> ${donation.isanonymous}</div>
                                <div class="card-detail remarks"><span>Remarks:</span> ${donation.remarks || 'None'}</div>
                                <div class="card-detail acknowledgment_sent"><span>Acknowledgment Sent:</span> ${donation.acknowledgment_sent}</div>
                                <div class="card-detail campaign_id"><span>Campaign ID:</span> ${donation.campaign_id || 'None'}</div>
                                <button type="button" id="sendMail" class="btn btn-primary">Send Acknowledgement</button>

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

            // Search functionality
            $('#searchInput').on('input', function() {
                const filterText = $(this).val().toLowerCase();
                const searchCriteria = $('#searchCriteria').val(); // Get the selected search criteria

                // Filter donation cards
                $('.donation-card').each(function() {
                    const fieldValue = $(this).find(`.${searchCriteria}`).text().toLowerCase(); // Get the relevant field value
                    if (fieldValue.includes(filterText)) {
                        $(this).show(); // Show card if it matches
                    } else {
                        $(this).hide(); // Hide card if it doesn't match
                    }
                });
            });


            $(document).ready(function() {

                // Use event delegation for dynamically added elements
                $(document).on('click', '.icon#del', function() {
                    // Show the modal
                    $('#deleteModal').css('display', 'block');

                    // Retrieve the record ID (stored in the data-id attribute of the card header)
                    recordId = $(this).closest('.card-header').data('id');
                    console.log("Record ID to delete:", recordId);
                });

                // Close the modal on "Cancel" or "X"
                $('#cancelDelete, .close').on('click', function() {
                    $('#deleteModal').css('display', 'none');
                });
                // Event listener for delete icon
                $(document).on('click', '#del', function() {
                    const recordId = $(this).data('id'); // Retrieve the data-id
                    if (!recordId) {
                        console.error('Record ID is undefined');
                        return;
                    }
                    console.log('Record ID to delete:', recordId);

                    // Show confirmation popup
                    $('#deletePopup').show();

                    // Attach the record ID to the confirm button
                    $('#confirmDelete').data('id', recordId);
                });

                // Confirm delete logic
                $('#confirmDelete').on('click', function() {
                    const recordId = $(this).data('id'); // Retrieve the attached record ID
                    if (recordId) {
                        console.log('Deleting record ID:', recordId);

                        // Perform an AJAX call to delete the record
                        $.ajax({
                            url: 'delete_record.php',
                            type: 'POST',
                            data: {
                                id: recordId
                            },
                            success: function(response) {
                                console.log(response); // Handle success
                                $('#deletePopup').hide();
                                location.reload(); // Optionally refresh the page
                            },
                            error: function() {
                                console.error('Failed to delete the record');
                            }
                        });
                    }
                });

                // Close the popup
                $('#cancelDelete').on('click', function() {
                    $('#deletePopup').hide();
                });
            });
            $(document).ready(function() {
                // Open Edit Popup
                $(document).on('click', '#edit', function() {
                    const recordId = $(this).data('id');

                    if (!recordId) {
                        console.error('Record ID is undefined');
                        return;
                    }

                    // Fetch data for the specific record
                    $.ajax({
                        url: 'fetch_record.php', // Create this backend script
                        type: 'GET',
                        data: {
                            id: recordId
                        },
                        success: function(response) {
                            const data = JSON.parse(response);

                            if (data.success) {
                                // Populate form fields
                                $('#editId').val(data.record.id);
                                $('#editDonorName').val(data.record.donor_name);
                                $('#editMobileNumber').val(data.record.mobile_number);
                                $('#editEmail').val(data.record.email);
                                $('#editAddress').val(data.record.address);
                                $('#editDonationAmount').val(data.record.donation_amount);
                                $('#editPaymentMethod').val(data.record.payment_method);
                                $('#editTransactionReference').val(data.record.transaction_reference);
                                $('#editStatus').val(data.record.status);
                                $('#editPurpose').val(data.record.purpose);
                                $('#editIsAnonymous').val(data.record.isanonymous);
                                $('#editAcknowledgementSent').val(data.record.acknowledgment_sent);
                                $('#editCampaign').val(data.record.campaign_id);
                                $('#editDonationDate').val(data.record.donation_date);
                                $('#editRemarks').val(data.record.remarks || '');

                                // Show the popup
                                $('#editPopup').show();
                            } else {
                                alert(data.message);
                            }
                        },
                        error: function() {
                            console.error('Failed to fetch record data');
                        },
                    });
                });

                // Close the popup
                $('#cancelEdit').on('click', function() {
                    $('#editPopup').hide();
                });

                // Save Changes
                $('#saveChanges').on('click', function() {
                    const formData = $('#editForm').serialize();

                    $.ajax({
                        url: 'update_record.php', // Create this backend script
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            const data = JSON.parse(response);

                            if (data.success) {
                                alert('Record updated successfully');
                                $('#editPopup').hide();
                                location.reload(); // Optionally reload the page to reflect changes
                            } else {
                                alert(data.message);
                            }
                        },
                        error: function() {
                            console.error('Failed to update the record');
                        },
                    });
                });
            });

        });
    </script>
</body>

</html>