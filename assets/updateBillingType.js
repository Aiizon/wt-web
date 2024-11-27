export default function updateBillingType(selectedBillingType, billingTypeSelect, unitPriceDisplay, totalPriceDisplay, rentalUnits, monthlyRentPrice, billingTypeInput) {
    selectedBillingType = billingTypeSelect.querySelector('option:checked');
    const months = selectedBillingType.value;
    const discount = selectedBillingType.getAttribute('data-discount');

    if (!Number.isInteger(parseInt(months))) {
        unitPriceDisplay.textContent = `${monthlyRentPrice}€`;
        return;
    }

    if (billingTypeInput !== null) {
        billingTypeInput.value = months;
    }

    unitPriceDisplay.textContent = `${Math.round((monthlyRentPrice * months) * (1 + discount / 100))}€`;
    totalPriceDisplay.textContent = `${Math.round((monthlyRentPrice * months) * (1 + discount / 100)) * rentalUnits.value}€`;
};