import updateBillingType from "./updateBillingType";
import updateAvailability from "./updateAvailability";

const billingTypeSelect: HTMLSelectElement = document.querySelector('#billing_type_billingType');
const unitPriceDisplay:  HTMLElement       = document.querySelector('.unitPriceDisplay');
const totalPriceDisplay: HTMLInputElement  = document.querySelector('.totalPriceDisplay');
const monthlyRentPrice:  number            = parseInt(document.querySelector('.monthlyPriceDisplay').getAttribute('data-rent'));
const billingTypeInput:  HTMLInputElement  = document.querySelector('.billingType');
const rentQuantityInput: HTMLInputElement  = document.querySelector('.rentQuantity');
const rentalUnits:       HTMLInputElement  = document.querySelector('#rent_quantity_quantity');
const offerUnitAmount:   number            = parseInt(document.querySelector('#unitAmount').getAttribute('data-unit-amount'));
const availableDisplay:  HTMLElement       = document.querySelector('#availableDisplay');
const submitButton:      HTMLButtonElement = document.querySelector('button[type="submit"]');

const billingTypeParameter: string = new URLSearchParams(window.location.search).get('billing');
let selectedBillingType: HTMLOptionElement;
if (billingTypeParameter) {
    selectedBillingType = billingTypeSelect.querySelector(`option[value="${billingTypeParameter}"]`);
    selectedBillingType.selected = true;
} else {
    selectedBillingType = billingTypeSelect.querySelector('option:checked');
}

updateBillingType(selectedBillingType, billingTypeSelect, unitPriceDisplay, totalPriceDisplay, rentalUnits, monthlyRentPrice, billingTypeInput, null);

billingTypeSelect.addEventListener('change', (e: Event) => {
    e.preventDefault();
    updateBillingType(selectedBillingType, billingTypeSelect, unitPriceDisplay, totalPriceDisplay, rentalUnits, monthlyRentPrice, billingTypeInput, null);
});

updateAvailability(rentalUnits, offerUnitAmount, rentQuantityInput, availableDisplay, submitButton);

rentalUnits.addEventListener('change', (e: Event) => {
    e.preventDefault();
    updateBillingType(selectedBillingType, billingTypeSelect, unitPriceDisplay, totalPriceDisplay, rentalUnits, monthlyRentPrice, billingTypeInput, null);
    updateAvailability(rentalUnits, offerUnitAmount, rentQuantityInput, availableDisplay, submitButton);
});