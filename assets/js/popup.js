document.addEventListener("DOMContentLoaded", function () {
    window.openPopup = function (busId) {
        document.getElementById("tripBusId").value = busId;
        let popup = document.getElementById("popupForm");

        popup.classList.remove("hidden"); // Show popup
        popup.classList.add("flex"); // Ensure flexbox works for centering

        // Set today's date as the minimum for "Date From"
        let today = new Date().toISOString().split("T")[0];
        document.getElementById("dateFrom").setAttribute("min", today);
        document.getElementById("dateTo").setAttribute("min", today);
    };

    window.closePopup = function () {
        let popup = document.getElementById("popupForm");
        popup.classList.add("hidden"); // Hide popup
        popup.classList.remove("flex");
    };

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

    document.getElementById("dateFrom").addEventListener("change", function () {
        document.getElementById("dateTo").setAttribute("min", this.value);
        updateDays();
    });

    document.getElementById("dateTo").addEventListener("change", updateDays);
});
