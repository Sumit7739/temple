// Function to toggle chart visibility
function showChart(id) {
    document.querySelectorAll('.chart-container').forEach(div => {
        div.style.display = div.id === id ? 'block' : 'none';
    });
}

// Fetch and render all charts
const fetchDonationData = async () => {
    try {
        const response = await fetch('fetch_donationschart.php');
        const data = await response.json();

        // Log the data to the console to inspect its structure
        console.log('Fetched Data:', data);

        // Populate individual charts
        renderTrendChart(data.trends);
        renderTopDonorsChart(data.topDonors);
        renderRangesChart(data.ranges);
        renderPaymentMethodChart(data.paymentMethods);
        renderAverageChart(data.average);
    } catch (error) {
        console.error('Error fetching donation data:', error);
    }
};

const renderTrendChart = (data) => {
    // Extract unique dates from the data
    const labels = [...new Set(data.map(item => item.donation_date))];

    // Group donations by each date
    const donationsOnEachDay = labels.map(date => data.filter(item => item.donation_date === date));

    // Map donations to amounts without aggregating them (show each donation as it is)
    const amounts = donationsOnEachDay.map(dayDonations => {
        return dayDonations.map(donation => Number(donation.donation_amount) || 0);
    });

    // Flatten the amounts array for display in the chart (show individual donations)
    const flatAmounts = amounts.flat();

    // Flatten the labels for corresponding donations (use the same label for each donation on the same date)
    const flatLabels = donationsOnEachDay.flatMap((_, index) => {
        return new Array(donationsOnEachDay[index].length).fill(labels[index]);
    });

    // Render the chart with individual donation amounts
    new Chart(document.getElementById('trendChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: flatLabels,
            datasets: [{
                label: 'Donations Over Time',
                data: flatAmounts,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.4,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Donation Trends',
                    font: {
                        size: 24,  // Title font size
                        family: 'Arial', // Title font family
                        weight: 'bold', // Title font weight
                        color: '#333' // Title color
                    },
                    padding: {
                        top: 10,
                        bottom: 10
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return `₹${context.raw.toFixed(2)}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Date',
                        font: {
                            size: 20,  // X-axis title font size
                            family: 'Arial', // X-axis title font family
                            weight: 'bold', // X-axis title font weight
                            color: '#333' // X-axis title color
                        },
                    },
                    ticks: {
                        font: {
                            size: 16, // X-axis ticks font size
                            family: 'Arial', // X-axis ticks font family
                            color: '#666' // X-axis ticks color
                        },
                        autoSkip: true,
                        maxRotation: 0,
                        minRotation: 0
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Donation Amount (₹)',
                        font: {
                            size: 20,  // Y-axis title font size
                            family: 'Arial', // Y-axis title font family
                            weight: 'bold', // Y-axis title font weight
                            color: '#333' // Y-axis title color
                        }
                    },
                    ticks: {
                        font: {
                            size: 16, // Y-axis ticks font size
                            family: 'Arial', // Y-axis ticks font family
                            color: '#666' // Y-axis ticks color
                        }
                    }
                }
            }
        }
    });

    // Show summary for Trend Chart
    const totalDonations = flatAmounts.reduce((sum, amount) => sum + amount, 0);
    const averageDonation = totalDonations / flatAmounts.length;
    document.getElementById('trendSummary').innerHTML = `Total Donations: ₹${totalDonations.toFixed(2)}<br>Average Donation: ₹${averageDonation.toFixed(2)}`;
};


// Helper to aggregate data by week or month if needed
const aggregateDataByDate = (labels, donationsOnEachDay, amounts) => {
    if (labels.length > 30) {
        // Switch to weeks/months if there are more than 30 days
        const groupedData = groupDataByWeekOrMonth(labels, donationsOnEachDay, amounts);
        return groupedData;
    }

    return {
        labels,
        amounts
    };
};

// Group data by week or month (for Trend Chart)
const groupDataByWeekOrMonth = (labels, donationsOnEachDay, amounts) => {
    const grouped = {};
    const newLabels = [];
    const newAmounts = [];
    let currentPeriod = '';
    let periodSum = 0;

    labels.forEach((label, index) => {
        const period = getWeekOrMonth(label);
        if (currentPeriod !== period) {
            if (currentPeriod !== '') {
                newLabels.push(currentPeriod);
                newAmounts.push(periodSum);
            }
            currentPeriod = period;
            periodSum = amounts[index];
        } else {
            periodSum += amounts[index];
        }
    });

    if (currentPeriod !== '') {
        newLabels.push(currentPeriod);
        newAmounts.push(periodSum);
    }

    return {
        labels: newLabels,
        amounts: newAmounts
    };
};

// Helper to get week or month from date string (trend date format assumed to be 'YYYY-MM-DD')
const getWeekOrMonth = (date) => {
    const dateObj = new Date(date);
    const month = dateObj.toLocaleString('default', {
        month: 'short'
    });
    const week = `Week ${Math.floor(dateObj.getDate() / 7) + 1}`;
    return week || month;
};

// Top Donors Chart (Show total donation amount by donor name, top 10)
const renderTopDonorsChart = (data) => {
    const labels = data.slice(0, 10).map(item => item.name);
    const amounts = data.slice(0, 10).map(item => Number(item.donation_amount) || 0); // Ensure numbers are used

    new Chart(document.getElementById('topDonorsChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Top Donors',
                data: amounts,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Top Donors',
                    font: {
                        size: 24,  // Title font size
                        family: 'Arial', // Title font family
                        weight: 'bold', // Title font weight
                        color: '#333' // Title color
                    },
                    padding: {
                        top: 10,
                        bottom: 10
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Donation Amount (₹)',
                        font: {
                            size: 20,  // Y-axis title font size
                            family: 'Arial', // Y-axis title font family
                            weight: 'bold', // Y-axis title font weight
                            color: '#333' // Y-axis title color
                        },
                    },
                    ticks: {
                        font: {
                            size: 16, // Y-axis ticks font size
                            family: 'Arial', // Y-axis ticks font family
                            color: '#666' // Y-axis ticks color
                        }
                    }
                }
            }
        }
    });

    // Show summary for Top Donors Chart
    const totalDonations = amounts.reduce((sum, amount) => sum + amount, 0);
    document.getElementById('topDonorsSummary').innerHTML = `Total Donations from Top Donors: ₹${totalDonations.toFixed(2)}`;
};

// Donation Ranges Chart (Show ranges by amount)
const renderRangesChart = (ranges) => {
    const labels = Object.keys(ranges);
    const data = Object.values(ranges);

    new Chart(document.getElementById('rangesChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Donation Ranges',
                data: data,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Donation Ranges',
                    font: {
                        size: 20,  // X-axis title font size
                        family: 'Arial', // X-axis title font family
                        weight: 'bold', // X-axis title font weight
                        color: '#333' // X-axis title color
                    },
                    padding: {
                        top: 10,
                        bottom: 10
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Donation Range',
                        font: {
                            size: 20,  // Y-axis title font size
                            family: 'Arial', // Y-axis title font family
                            weight: 'bold', // Y-axis title font weight
                            color: '#333' // Y-axis title color
                        }
                    },
                    ticks: {
                        font: {
                            size: 16, // Y-axis ticks font size
                            family: 'Arial', // Y-axis ticks font family
                            color: '#666' // Y-axis ticks color
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Donations',
                        font: {
                            size: 20,  // Y-axis title font size
                            family: 'Arial', // Y-axis title font family
                            weight: 'bold', // Y-axis title font weight
                            color: '#333' // Y-axis title color
                        }
                    },
                    ticks: {
                        font: {
                            size: 16, // Y-axis ticks font size
                            family: 'Arial', // Y-axis ticks font family
                            color: '#666' // Y-axis ticks color
                        }
                    }
                }
            }
        }
    });

    // Show summary for Ranges Chart
    const totalDonations = data.reduce((sum, range) => sum + range, 0);
    document.getElementById('rangesSummary').innerHTML = `Total Donations across Ranges: ${totalDonations}`;
};

// Payment Method Chart (Show number of donations by method)
const renderPaymentMethodChart = (data) => {
    const labels = Object.keys(data);
    const values = Object.values(data);

    new Chart(document.getElementById('paymentMethodChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Payment Methods',
                data: values,
                backgroundColor: 'rgba(153, 102, 255, 0.6)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Donations by Payment Method'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Donations'
                    }
                }
            }
        }
    });

    // Show summary for Payment Method Chart
    const totalDonations = values.reduce((sum, value) => sum + value, 0);
    document.getElementById('paymentMethodSummary').innerHTML = `Total Donations across Payment Methods: ${totalDonations}`;
};

// Average Donation Chart
const renderAverageChart = (data) => {
    const labels = data.map(item => item.donation_date);
    const averages = data.map(item => Number(item.average) || 0); // Ensure numbers are used

    new Chart(document.getElementById('averageChart').getContext('2d'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Average Donation Over Time',
                data: averages,
                borderColor: 'rgba(255, 159, 64, 1)',
                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                tension: 0.4,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Average Donation Over Time'
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return `₹${context.raw.toFixed(2)}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Average Donation (₹)'
                    }
                }
            }
        }
    });

    // Show summary for Average Donation Chart
    const totalDonations = averages.reduce((sum, avg) => sum + avg, 0);
    const averageDonation = totalDonations / averages.length;
    document.getElementById('averageSummary').innerHTML = `Total Average Donations: ₹${totalDonations.toFixed(2)}<br>Average Donation per Day: ₹${averageDonation.toFixed(2)}`;
};

// Fetch data on page load
fetchDonationData();