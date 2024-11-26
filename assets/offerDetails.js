import getUnitAvailability from "./getUnitAvailability";

const billingTypeSelect = document.querySelector('#billing_type_billingType');
const billingTypeParameter = document.documentURI.split('?')[1]?.replace('billing=', '');
let selectedBillingType;
if (billingTypeParameter) {
    selectedBillingType = billingTypeSelect.querySelector(`option[value="${billingTypeParameter}"]`);
    selectedBillingType.selected = 'selected';
} else {
    selectedBillingType = billingTypeSelect.querySelector('option:checked');
}

const totalPriceDisplay = document.querySelector('.totalPriceDisplay');
const monthlyRentPrice = parseInt(document.querySelector('.monthlyPriceDisplay').getAttribute('data-rent'));
const billingTypeInput = document.querySelector('.billingType');
const rentQuantityInput = document.querySelector('.rentQuantity');

const updateBillingType = () => {
    selectedBillingType = billingTypeSelect.querySelector('option:checked');
    const months = selectedBillingType.value;
    const discount = selectedBillingType.getAttribute('data-discount');

    if (!Number.isInteger(parseInt(months))) {
        totalPriceDisplay.textContent = `${monthlyRentPrice}€`;
        return;
    }

    billingTypeInput.value = months;

    totalPriceDisplay.textContent = `${Math.round((monthlyRentPrice * months) * (1 + discount / 100))}€`;
};

updateBillingType();

billingTypeSelect.addEventListener('change', e => {
    e.preventDefault();
    updateBillingType();
});


const availableUnits = getUnitAvailability();
const offerUnitAmount = parseInt(document.querySelector('#unitAmount').getAttribute('data-unit-amount'));
const availableDisplay = document.querySelector('#availableDisplay');
const rentalUnits = document.querySelector('#rent_quantity_quantity');

const updateAvailability = () => {
    const selectedUnitAmount = parseInt(rentalUnits.value) * offerUnitAmount;
    const quantity = rentalUnits.value;

    if (selectedUnitAmount <= availableUnits) {
        availableDisplay.textContent = 'Disponible';
        availableDisplay.classList.replace('text-danger', 'text-success');
    } else {
        availableDisplay.textContent = 'Non disponible';
        availableDisplay.classList.replace('text-success', 'text-danger');
    }

    rentQuantityInput.value = quantity;
}

updateAvailability();

rentalUnits.addEventListener('change', e => {
    e.preventDefault();
    updateAvailability();
});