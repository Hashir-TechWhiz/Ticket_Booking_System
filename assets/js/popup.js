document.addEventListener("DOMContentLoaded", function () {
    let bookedDates = [];

    window.openPopup = function (busId) {
        document.getElementById("tripBusId").value = busId;
        let popup = document.getElementById("popupForm");

        popup.classList.remove("hidden"); // Show popup
        popup.classList.add("flex"); // Ensure flex-box works for centering

        // Fetch booked dates for the selected bus
        fetch(`get_booked_dates.php?bus_id=${busId}`)
            .then(response => response.json())
            .then(data => {
                bookedDates = data;
                // Initialize flatpickr on the date inputs with the disabled dates and custom styling
                initFlatpickr();
            });
    };

    window.closePopup = function () {
        let popup = document.getElementById("popupForm");
        popup.classList.add("hidden"); // Hide popup
        popup.classList.remove("flex");

        // Destroy flatpickr instances if they exist to avoid duplication on next open
        if (window.fpDateFrom) {
            window.fpDateFrom.destroy();
        }
        if (window.fpDateTo) {
            window.fpDateTo.destroy();
        }
        // Clear date input values and days
        document.getElementById("dateFrom").value = "";
        document.getElementById("dateTo").value = "";
        document.getElementById("days").value = "";
    };

    function initFlatpickr() {
        // Destroy previous instances if any
        if (window.fpDateFrom) {
            window.fpDateFrom.destroy();
        }
        if (window.fpDateTo) {
            window.fpDateTo.destroy();
        }

        // Initialize flatpickr for Date From
        window.fpDateFrom = flatpickr("#dateFrom", {
            dateFormat: "Y-m-d",
            minDate: "today",
            disable: bookedDates,
            onChange: function (selectedDates, dateStr, instance) {
                // Update the minDate for Date To based on selected Date From
                if (dateStr) {
                    window.fpDateTo.set('minDate', dateStr);
                }
                updateDays();
            },
            onDayCreate: function (selectedDates, dateStr, instance, dayElem) {
                let date = dayElem.dateObj;
                let today = new Date();
                today.setHours(0, 0, 0, 0);
                let formattedDate = instance.formatDate(date, "Y-m-d");
                if (date < today) {
                    dayElem.classList.add('past-date');
                } else if (bookedDates.includes(formattedDate)) {
                    dayElem.classList.add('booked-date');
                }
            }
        });

        // Initialize flatpickr for Date To
        window.fpDateTo = flatpickr("#dateTo", {
            dateFormat: "Y-m-d",
            minDate: "today",
            disable: bookedDates,
            onChange: function (selectedDates, dateStr, instance) {
                updateDays();
            },
            onDayCreate: function (selectedDates, dateStr, instance, dayElem) {
                let date = dayElem.dateObj;
                let today = new Date();
                today.setHours(0, 0, 0, 0);
                let formattedDate = instance.formatDate(date, "Y-m-d");
                if (date < today) {
                    dayElem.classList.add('past-date');
                } else if (bookedDates.includes(formattedDate)) {
                    dayElem.classList.add('booked-date');
                }
            }
        });
    }

    function updateDays() {
        let dateFrom = document.getElementById("dateFrom").value;
        let dateTo = document.getElementById("dateTo").value;
        let daysInput = document.getElementById("days");

        if (dateFrom && dateTo) {
            let fromDate = new Date(dateFrom);
            let toDate = new Date(dateTo);

            if (toDate >= fromDate) {
                let timeDiff = toDate - fromDate;
                let days = timeDiff / (1000 * 3600 * 24) + 1; // Include the starting day
                daysInput.value = days;
            } else {
                alert("Error: 'Date To' cannot be before 'Date From'.");
                document.getElementById("dateTo").value = ""; // Clear invalid input
                daysInput.value = "";
            }
        }
    }
});
