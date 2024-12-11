$(document).ready(function() {
    $.ajax({
        url: 'fetch_expenseschart.php', // Backend to fetch expenses data
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const categories = response.categories; // E.g., ["Rent", "Utilities", "Salaries"]
                const amounts = response.amounts; // E.g., [5000, 1200, 8000]

                // Create the chart
                const ctx = document.getElementById('expensesChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar', // Change to 'pie' or 'line' for different types
                    data: {
                        labels: categories,
                        datasets: [{
                            label: 'Expenses',
                            data: amounts,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        // Safely parse the raw value as a number
                                        const value = parseFloat(tooltipItem.raw) || 0;
                                        return `₹${value.toFixed(2)}`;
                                    }
                                }
                            }
                        }
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            } else {
                console.error(response.message);
                alert("Failed to load expenses data.");
            }
        },
        error: function(xhr, status, error) {
            console.error(`Error: ${error}`);
            alert("Failed to fetch expenses data.");
        }
    });
});