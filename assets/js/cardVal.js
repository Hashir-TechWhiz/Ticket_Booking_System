// assets/js/cardVal.js
function formatCardNumber(input) {
    let value = input.value.replace(/\D/g, '');
    value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
    input.value = value.substring(0, 19);
    document.getElementById('cardError').classList.add('hidden');
}

function formatExpiryDate(input) {
    let value = input.value.replace(/\D/g, '');
    if (value.length > 2) {
        value = value.substring(0, 2) + '/' + value.substring(2, 4);
    }
    input.value = value.substring(0, 5);
    document.getElementById('expiryError').classList.add('hidden');
}

function validatePaymentForm() {
    let isValid = true;

    // Card Number Validation
    const cardNumber = document.getElementById('cardNumber').value.replace(/\D/g, '');
    if (cardNumber.length !== 16) {
        document.getElementById('cardError').classList.remove('hidden');
        isValid = false;
    }

    // Expiry Date Validation
    const expiryDate = document.getElementById('expiryDate').value;
    const [month, year] = expiryDate.split('/');
    const currentYear = new Date().getFullYear() % 100;
    const currentMonth = new Date().getMonth() + 1;
    
    if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiryDate) || 
        year < currentYear || 
        (year == currentYear && month < currentMonth)) {
        document.getElementById('expiryError').classList.remove('hidden');
        isValid = false;
    }

    // CVV Validation
    const cvv = document.getElementById('cvv').value;
    if (cvv.length !== 3 || !/^\d+$/.test(cvv)) {
        document.getElementById('cvvError').classList.remove('hidden');
        isValid = false;
    }

    return isValid;
}