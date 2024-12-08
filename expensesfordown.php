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

    <a href="view_expenses.php">
        <button class="download-button back">
            <span class="material-icons">arrow_back</span>
            <span class="text">
                Back
            </span>
        </button>
    </a>

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

            /**
             * Function to render table rows dynamically.
             * @param {Array} data - Array of expense objects.
             */
            function renderTable(data) {
                const tbody = document.querySelector('#expenses-table tbody');
                tbody.innerHTML = ''; // Clear existing rows

                // Iterate through data and append rows to the table
                data.forEach((expense) => {
                    const row = document.createElement('tr');

                    // Create table cells
                    const idCell = document.createElement('td');
                    idCell.textContent = expense.expense_id;

                    const categoryCell = document.createElement('td');
                    categoryCell.textContent = expense.category;

                    const amountCell = document.createElement('td');
                    amountCell.textContent = `${expense.amount} rs`;

                    const descriptionCell = document.createElement('td');
                    descriptionCell.textContent = expense.description;

                    const dateCell = document.createElement('td');
                    dateCell.textContent = expense.expense_date;

                    // Append cells to the row
                    row.appendChild(idCell);
                    row.appendChild(categoryCell);
                    row.appendChild(amountCell);
                    row.appendChild(descriptionCell);
                    row.appendChild(dateCell);

                    // Append row to the table body
                    tbody.appendChild(row);
                });
            }
        });

        document.getElementById('download-pdf').addEventListener('click', function() {
            const {
                jsPDF
            } = window.jspdf;

            // Create a new PDF instance
            const pdf = new jsPDF();

            // Get the table element
            const table = document.getElementById('expenses-table');

            // Use autoTable to add the table to the PDF
            pdf.autoTable({
                html: table,
                startY: 20, // Start position below the title
                styles: {
                    font: 'helvetica',
                    fontSize: 10,
                    cellPadding: 3,
                },
                headStyles: {
                    fillColor: [41, 128, 185], // Table header color
                    textColor: 255,
                },
            });

            // Add a title to the PDF
            pdf.text('Latest Expenses Report', 14, 15);

            // Save the PDF
            pdf.save('expenses_report.pdf');
        });
    </script>
</body>

</html>