export default function updateBillingType(
    selectedBillingType: HTMLOptionElement,
    billingTypeSelect:   HTMLSelectElement,
    unitPriceDisplay:    HTMLElement,
    totalPriceDisplay:   HTMLElement,
    rentalUnits:         HTMLInputElement,
    monthlyRentPrice:    number,
    billingTypeInput:    HTMLInputElement
): void
{
    selectedBillingType = billingTypeSelect.querySelector('option:checked');
    const months:   number = parseInt(selectedBillingType.getAttribute('data-months'));
    const discount: number = parseInt(selectedBillingType.getAttribute('data-discount'));

    if (!Number.isInteger(months)) {
        unitPriceDisplay.textContent = `${monthlyRentPrice} €`;
        return;
    }

    if (billingTypeInput !== null) {
        billingTypeInput.value = String(selectedBillingType.value);
    }

    unitPriceDisplay.textContent  = `${Math.round((monthlyRentPrice * months) * (1 + discount / 100))} €`;
    totalPriceDisplay.textContent = `${Math.round((monthlyRentPrice * months) * (1 + discount / 100)) * Number.parseInt(rentalUnits.value)} €`;
};