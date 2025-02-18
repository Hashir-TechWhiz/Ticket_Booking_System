document.addEventListener("DOMContentLoaded", function () {
    let dateInput = document.querySelector("input[name='journey_date']");

    if (dateInput) {
        // Get today's date in YYYY-MM-DD format
        let today = new Date();
        let yyyy = today.getFullYear();
        let mm = String(today.getMonth() + 1).padStart(2, "0");
        let dd = String(today.getDate()).padStart(2, "0");
        let minDate = `${yyyy}-${mm}-${dd}`;

        // Set the min attribute to prevent past dates
        dateInput.setAttribute("min", minDate);

        // Prevent manual entry of invalid dates
        dateInput.addEventListener("input", function () {
            if (this.value < minDate) {
                this.value = minDate;
            }
        });
    }
});
