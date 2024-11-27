import updateBillingType from "./updateBillingType";
import updateAvailability from "./updateAvailability";

const billingTypeSelect = document.querySelector('#rent_billingType');
let selectedBillingType = billingTypeSelect.querySelector('option:checked');
const unitPriceDisplay = document.querySelector('.unitPriceDisplay');
const totalPriceDisplay = document.querySelector('.totalPriceDisplay');
const monthlyRentPrice = parseInt(document.querySelector('.monthlyPriceDisplay').getAttribute('data-rent'));
const rentalUnits = document.querySelector('#rent_quantity');
const offerUnitAmount = parseInt(document.querySelector('.unitAmount').getAttribute('data-unit-amount'));
const availableDisplay = document.querySelector('#availableDisplay');
const submitButton = document.querySelector('button[type="submit"]');

const parameterRentalUnits = new URLSearchParams(window.location.search).get('quantity');
if (parameterRentalUnits) {
    rentalUnits.value = parameterRentalUnits;
}

updateBillingType(selectedBillingType, billingTypeSelect, unitPriceDisplay, totalPriceDisplay, rentalUnits, monthlyRentPrice, null);

billingTypeSelect.addEventListener('change', e => {
    e.preventDefault();
    updateBillingType(selectedBillingType, billingTypeSelect, unitPriceDisplay, totalPriceDisplay, rentalUnits, monthlyRentPrice, null);
});

updateAvailability(rentalUnits, offerUnitAmount, null, availableDisplay, submitButton);

rentalUnits.addEventListener('change', e => {
    e.preventDefault();
    updateBillingType(selectedBillingType, billingTypeSelect, unitPriceDisplay, totalPriceDisplay, rentalUnits, monthlyRentPrice, null);
    updateAvailability(rentalUnits, offerUnitAmount, null, availableDisplay, submitButton);
});