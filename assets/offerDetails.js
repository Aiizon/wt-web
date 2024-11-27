import updateBillingType from "./updateBillingType";
import updateAvailability from "./updateAvailability";

const billingTypeSelect = document.querySelector('#billing_type_billingType');
const unitPriceDisplay = document.querySelector('.unitPriceDisplay');
const totalPriceDisplay = document.querySelector('.totalPriceDisplay');
const monthlyRentPrice = parseInt(document.querySelector('.monthlyPriceDisplay').getAttribute('data-rent'));
const billingTypeInput = document.querySelector('.billingType');
const rentQuantityInput = document.querySelector('.rentQuantity');
const rentalUnits = document.querySelector('#rent_quantity_quantity');
const offerUnitAmount = parseInt(document.querySelector('#unitAmount').getAttribute('data-unit-amount'));
const availableDisplay = document.querySelector('#availableDisplay');
const submitButton = document.querySelector('button[type="submit"]');

const billingTypeParameter = new URLSearchParams(window.location.search).get('billing');
let selectedBillingType;
if (billingTypeParameter) {
    selectedBillingType = billingTypeSelect.querySelector(`option[value="${billingTypeParameter}"]`);
    selectedBillingType.selected = 'selected';
} else {
    selectedBillingType = billingTypeSelect.querySelector('option:checked');
}

updateBillingType(selectedBillingType, billingTypeSelect, unitPriceDisplay, totalPriceDisplay, rentalUnits, monthlyRentPrice, billingTypeInput);

billingTypeSelect.addEventListener('change', e => {
    e.preventDefault();
    updateBillingType(selectedBillingType, billingTypeSelect, unitPriceDisplay, totalPriceDisplay, rentalUnits, monthlyRentPrice, billingTypeInput);
});

updateAvailability(rentalUnits, offerUnitAmount, rentQuantityInput, availableDisplay, submitButton);

rentalUnits.addEventListener('change', e => {
    e.preventDefault();
    updateBillingType(selectedBillingType, billingTypeSelect, unitPriceDisplay, totalPriceDisplay, rentalUnits, monthlyRentPrice, billingTypeInput);
    updateAvailability(rentalUnits, offerUnitAmount, rentQuantityInput, availableDisplay, submitButton);
});