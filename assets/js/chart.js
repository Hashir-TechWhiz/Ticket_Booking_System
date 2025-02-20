document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById("bookingsChart").getContext("2d");

    // Ensure bookingData is available
    const bookings = [];
    for (let i = 1; i <= 12; i++) {
        bookings.push(bookingData[i] || 0); 
    }

    new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: [
                "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
            ],
            datasets: [{
                label: "Bookings",
                data: bookings,
                backgroundColor: [
                    "#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0",
                    "#9966FF", "#FF9F40", "#C9CBCF", "#FF6384",
                    "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF"
                ],
                hoverOffset: 4,
            }]
        },
        options: {
            responsive: true,
            aspectRatio: 2,
            plugins: {
                legend: {
                    position: "bottom"
                }
            }
        }
    });
});
