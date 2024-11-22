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

const updateBillingType = () => {
    selectedBillingType = billingTypeSelect.querySelector('option:checked');
    const months = selectedBillingType.innerText;
    const discount = selectedBillingType.getAttribute('data-discount');
    const totalPriceDisplay = document.querySelector('.totalPriceDisplay');
    const monthlyRentPrice = parseInt(document.querySelector('.monthlyPriceDisplay').getAttribute('data-rent'));

    if (!Number.isInteger(parseInt(months))) {
        totalPriceDisplay.textContent = `${monthlyRentPrice}€`;
        return;
    }
    totalPriceDisplay.textContent = `${Math.round((monthlyRentPrice * months) * (1 + discount / 100))}€`;
};

updateBillingType();

billingTypeSelect.addEventListener('change', e => {
    e.preventDefault();
    updateBillingType();
});


const availableUnits = getUnitAvailability();
const offerUnitAmount = parseInt(document.querySelector('#unitAmount').getAttribute('data-unit-amount'));
const rentalUnits = document.querySelector('#rent_quantity_quantity');
const availableDisplay = document.querySelector('#availableDisplay');

const updateAvailability = () => {
    const selectedUnitAmount = parseInt(rentalUnits.value) * offerUnitAmount;

    if (selectedUnitAmount <= availableUnits) {
        availableDisplay.textContent = 'Disponible';
        availableDisplay.classList.replace('text-danger', 'text-success');
    } else {
        availableDisplay.textContent = 'Non disponible';
        availableDisplay.classList.replace('text-success', 'text-danger');
    }
}

updateAvailability();

rentalUnits.addEventListener('change', e => {
    e.preventDefault();
    updateAvailability();
});