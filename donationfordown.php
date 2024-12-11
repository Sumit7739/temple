<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Table as PDF</title>
    <!-- Google Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        /* Button Styling */
        .download-button {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            background-color: #fff;
            color: #000;
            border: none;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 25px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-bottom: 20px;
        }

        .download-button:hover {
            background-color: #ddd;
        }

        .download-button .material-icons {
            margin-right: 10px;
            font-size: 1.5rem;
        }

        .back {
            position: absolute;
            top: 10px;
            right: 20px;
        }

        .back a {
            color: #000;
            text-decoration: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <button class="download-button" id="download-pdf">
        <span class="material-icons">file_download</span>
        Download as PDF
    </button>

    <a href="view_donations.php">
        <button class="download-button back">
            <span class="material-icons">arrow_back</span>
            <span class="text">
                Back
            </span>
        </button>
    </a>

    <table id="donations-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Donor Name</th>
                <th>Mobile</th>
                <th>Email</th>
                <th>Amount</th>
                <th>Payment Method</th>
                <th>Transaction Reference</th>
                <th>Date</th>
                <!-- <th>Remarks</th> -->
                <!-- <th>Status</th> -->
                <!-- <th>Purpose</th> -->
                <!-- <th>Anonymous</th> -->
                <!-- <th>Acknowledgement Sent</th> -->
                <!-- <th>Campaign ID</th> -->

            </tr>
        </thead>
        <tbody>
            <!-- Filtered results will appear here -->
        </tbody>
    </table>
    <!-- jsPDF Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <!-- jsPDF AutoTable Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let allDonations = []; // Array to store all fetched donations

            // Fetch all donations once
            $.ajax({
                url: 'fetch_donations.php', // Backend script to fetch all data
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        allDonations = response.data; // Store data for filtering
                        renderTable(allDonations); // Render the complete table initially
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

            /**
             * Function to render table rows dynamically.
             * @param {Array} data - Array of donation objects.
             */
            function renderTable(data) {
                const tbody = document.querySelector('#donations-table tbody');
                tbody.innerHTML = ''; // Clear existing rows

                // Iterate through data and append rows to the table
                data.forEach((donation) => {
                    const row = document.createElement('tr');

                    // Create table cells for each column
                    const idCell = document.createElement('td');
                    idCell.textContent = donation.id;

                    const donorNameCell = document.createElement('td');
                    donorNameCell.textContent = donation.donor_name;

                    const mobileCell = document.createElement('td');
                    mobileCell.textContent = donation.mobile_number;

                    const emailCell = document.createElement('td');
                    emailCell.textContent = donation.email;

                    const amountCell = document.createElement('td');
                    amountCell.textContent = `${donation.donation_amount} rs`;

                    const paymentMethodCell = document.createElement('td');
                    paymentMethodCell.textContent = donation.payment_method;

                    const transactionRefCell = document.createElement('td');
                    transactionRefCell.textContent = donation.transaction_reference;

                    const dateCell = document.createElement('td');
                    dateCell.textContent = donation.donation_date;

                    // const statusCell = document.createElement('td');
                    // statusCell.textContent = donation.status;

                    // const purposeCell = document.createElement('td');
                    // purposeCell.textContent = donation.purpose;

                    // const anonymousCell = document.createElement('td');
                    // anonymousCell.textContent = donation.Isanonymous ? 'Yes' : 'No';

                    // const remarksCell = document.createElement('td');
                    // remarksCell.textContent = donation.remarks;

                    // const ackSentCell = document.createElement('td');
                    // ackSentCell.textContent = donation.acknowledgement_sent ? 'Yes' : 'No';

                    // const campaignIdCell = document.createElement('td');
                    // campaignIdCell.textContent = donation.campaign_id;

                    // Append cells to the row
                    row.appendChild(idCell);
                    row.appendChild(donorNameCell);
                    row.appendChild(mobileCell);
                    row.appendChild(emailCell);
                    row.appendChild(amountCell);
                    row.appendChild(paymentMethodCell);
                    row.appendChild(transactionRefCell);
                    row.appendChild(dateCell);

                    // row.appendChild(statusCell);
                    // row.appendChild(purposeCell);
                    // row.appendChild(anonymousCell);
                    // row.appendChild(remarksCell);
                    // row.appendChild(ackSentCell);
                    // row.appendChild(campaignIdCell);

                    // Append row to the table body
                    tbody.appendChild(row);
                });
            }
        });


        document.getElementById('download-pdf').addEventListener('click', function() {
            const {
                jsPDF
            } = window.jspdf;

            // Create a new PDF instance in landscape mode
            const pdf = new jsPDF();

            // Add a title to the PDF
            pdf.setFont('helvetica', 'bold');
            pdf.setFontSize(12); // Smaller font size for the title
            pdf.text('Latest Donations Report', 14, 15);

            // Use autoTable to add the table to the PDF
            pdf.autoTable({
                html: '#donations-table', // Select the table by ID
                startY: 25, // Start position below the title
                styles: {
                    font: 'helvetica',
                    fontSize: 8, // Smaller font size for the table
                    cellPadding: 2, // Add minimal padding for better fit
                    lineHeight: 1.2, // Reduce line height
                },
                headStyles: {
                    fillColor: [41, 128, 185], // Table header background color
                    textColor: 255, // White text color for headers
                    fontSize: 9, // Small font size for headers
                    halign: 'center', // Center align headers
                },
                columnStyles: {
                    // Auto-shrink columns dynamically
                    0: {
                        cellWidth: 'auto',
                        halign: 'center'
                    }, // ID
                    1: {
                        cellWidth: 'auto',
                        halign: 'left'
                    }, // Donor Name
                    2: {
                        cellWidth: 'auto',
                        halign: 'center'
                    }, // Mobile
                    3: {
                        cellWidth: 'auto',
                        halign: 'left'
                    }, // Email
                    4: {
                        cellWidth: 'auto',
                        halign: 'right'
                    }, // Amount
                    5: {
                        cellWidth: 'auto',
                        halign: 'center'
                    }, // Payment Method
                    6: {
                        cellWidth: 'auto',
                        halign: 'left'
                    }, // Transaction Reference
                    7: {
                        cellWidth: 'auto',
                        halign: 'center'
                    }, // Date
                },
                bodyStyles: {
                    valign: 'middle', // Vertically align text in cells
                },
                alternateRowStyles: {
                    fillColor: [240, 240, 240], // Light gray alternate row background
                },
                theme: 'grid', // Use grid theme for table borders
                margin: {
                    top: 20,
                    left: 10,
                    right: 10
                }, // Adjust margins for more space
                tableWidth: 'wrap', // Dynamically wrap table to fit content
            });

            // Save the PDF
            pdf.save('Donations_Report.pdf');
        });
    </script>
</body>

</html>