<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Temple Ledger</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }

        .container {
            width: 95%;
            margin: 20px auto;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            font-size: 1.8em;
        }

        .controls {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        select,
        button {
            padding: 8px 12px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .credit {
            color: green;
            font-weight: bold;
        }

        .debit {
            color: red;
            font-weight: bold;
        }

        .donor {
            color: #007bff;
            font-weight: bold;
        }

        td.balance,
        th:nth-child(6) {
            font-weight: bold;
            color: #6c757d;
            background-color: #f9f9fb;
        }

        th:nth-child(6) {
            background-color: #0056b3;
            color: white;
        }

        td.balance:hover {
            background-color: #e8f7ff;
            transition: background-color 0.3s ease;
        }

        td.balance {
            font-size: 1.1em;
        }

        td.balance.negative {
            color: red;
        }


        tfoot {
            font-weight: bold;
            background-color: #e2e6ea;
        }

        @media (max-width: 768px) {
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Temple Donation and Expense Ledger</h1>

        <div class="controls">
            <select id="monthDropdown">
                <option value="01">January</option>
                <option value="02">February</option>
                <option value="03">March</option>
                <option value="04">April</option>
                <option value="05">May</option>
                <option value="06">June</option>
                <option value="07">July</option>
                <option value="08">August</option>
                <option value="09">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
            </select>

            <button id="sortButton">Sort Ascending</button>
        </div>

        <table id="ledgerTable">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Details</th>
                    <th>Category</th>
                    <th>Credit (₹)</th>
                    <th>Debit (₹)</th>
                    <th>Balance (₹)</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <?php
                include("config.php");
                $total_credit = 0;
                $total_debit = 0;
                $running_balance = 0;


                $sql = "
        SELECT 
            donation_date AS date, 
            donor_name AS details, 
            donation_amount AS credit, 
            NULL AS debit, 
            'Donation' AS category
        FROM donations
        UNION
        SELECT 
            expense_date AS date, 
            description AS details, 
            NULL AS credit, 
            amount AS debit, 
            category
        FROM expenses
        ORDER BY date ASC
    ";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $credit = $row['credit'] ?? 0;
                        $debit = $row['debit'] ?? 0;


                        $running_balance += $credit - $debit;


                        $total_credit += $credit;
                        $total_debit += $debit;

                        echo "<tr data-date='" . htmlspecialchars($row['date']) . "'>";
                        echo "<td>" . htmlspecialchars($row['date']) . "</td>";

                        if ($row['category'] == 'Donation') {
                            echo "<td class='donor'>Donation from " . htmlspecialchars($row['details']) . "</td>";
                        } else {
                            echo "<td>" . htmlspecialchars($row['details']) . "</td>";
                        }

                        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                        echo "<td class='credit'>" . ($credit ? number_format($credit, 2) : '—') . "</td>";
                        echo "<td class='debit'>" . ($debit ? number_format($debit, 2) : '—') . "</td>";
                        $balanceClass = $running_balance < 0 ? 'balance negative' : 'balance';
                        echo "<td class='$balanceClass'>" . number_format($running_balance, 2) . "</td>";
                        // echo "<td class='balance'>" . number_format($running_balance, 2) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No records found</td></tr>";
                }

                $final_balance = $total_credit - $total_debit;
                $conn->close();
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">Total</td>
                    <td id="totalCredit"><?= number_format($total_credit, 2) ?></td>
                    <td id="totalDebit"><?= number_format($total_debit, 2) ?></td>
                    <td id="finalBalance"><?= number_format($final_balance, 2) ?></td>
                </tr>
                <tr>
                    <td colspan="5">Final Balance (₹)</td>
                    <td><?= number_format($final_balance, 2) ?></td>
                </tr>
            </tfoot>

        </table>
    </div>

    <script>

        document.addEventListener("DOMContentLoaded", function () {
            const currentMonth = new Date().getMonth() + 1;
            document.getElementById("monthDropdown").value = currentMonth.toString().padStart(2, '0');
        });


        document.getElementById("monthDropdown").addEventListener("change", function () {
            const selectedMonth = this.value;
            const rows = document.querySelectorAll("#tableBody tr");

            rows.forEach(row => {
                const date = row.getAttribute("data-date");
                if (date && date.includes(`-${selectedMonth}-`)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });


        let ascending = true;
        document.getElementById("sortButton").addEventListener("click", function () {
            const table = document.getElementById("ledgerTable");
            const rows = Array.from(table.querySelectorAll("tbody tr"));

            rows.sort((a, b) => {
                const dateA = new Date(a.getAttribute("data-date"));
                const dateB = new Date(b.getAttribute("data-date"));
                return ascending ? dateA - dateB : dateB - dateA;
            });

            const tableBody = document.getElementById("tableBody");
            tableBody.innerHTML = "";
            rows.forEach(row => tableBody.appendChild(row));

            ascending = !ascending;
            this.textContent = ascending ? "Sort Ascending" : "Sort Descending";
        });
    </script>
</body>

</html>