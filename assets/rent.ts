import updateBillingType from "./updateBillingType";
import updateAvailability from "./updateAvailability";

const billingTypeSelect: HTMLSelectElement = document.querySelector('#rent_billingType');
let selectedBillingType: HTMLOptionElement = billingTypeSelect.querySelector('option:checked');
const unitPriceDisplay: HTMLElement        = document.querySelector('.unitPriceDisplay');
const totalPriceDisplay: HTMLElement       = document.querySelector('.totalPriceDisplay');
const monthlyRentPrice: number             = parseInt(document.querySelector('.monthlyPriceDisplay').getAttribute('data-rent'));
const rentalUnits: HTMLInputElement        = document.querySelector('#rent_quantity');
const offerUnitAmount: number              = parseInt(document.querySelector('.unitAmount').getAttribute('data-unit-amount'));
const availableDisplay: HTMLElement        = document.querySelector('#availableDisplay');
const submitButton: HTMLButtonElement      = document.querySelector('button[type="submit"]');

const parameterRentalUnits: string = new URLSearchParams(window.location.search).get('quantity');
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