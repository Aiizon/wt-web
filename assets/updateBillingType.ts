export default function updateBillingType(
    selectedBillingType: HTMLOptionElement,
    billingTypeSelect:   HTMLSelectElement,
    unitPriceDisplay:    HTMLElement,
    totalPriceDisplay:   HTMLElement,
    rentalUnits:         HTMLInputElement,
    monthlyRentPrice:    number,
    billingTypeInput:    HTMLInputElement,
    discountDisplay:     HTMLElement
): void
{
    selectedBillingType = billingTypeSelect.querySelector('option:checked');
    const months:            number  = parseInt(selectedBillingType.getAttribute('data-months')) ?? 1;
    const rentDiscount:      number  = parseInt(selectedBillingType.getAttribute('data-discount'));

    if (!Number.isInteger(months)) {
        unitPriceDisplay.textContent = `${monthlyRentPrice} €`;
        return;
    }

    if (billingTypeInput !== null) {
        billingTypeInput.value = String(selectedBillingType.value);
    }

    if (discountDisplay === null) {
        unitPriceDisplay.textContent  = `${Math.round((monthlyRentPrice * months) * (1 - rentDiscount / 100))} €`;
        totalPriceDisplay.textContent = `${Math.round((monthlyRentPrice * months) * (1 - rentDiscount / 100)) * Number.parseInt(rentalUnits.value)} €`;
        return;
    }

    const codeDiscountValue: number  = parseInt(discountDisplay.getAttribute('data-amount'));
    const codePercentage:    boolean = discountDisplay.getAttribute('data-percentage') === '1';

    if (codeDiscountValue !== null && discountDisplay.getAttribute('data-percentage') !== null) {
        if (codePercentage) {
            unitPriceDisplay.textContent = `${Math.round((monthlyRentPrice * months) * (1 - (rentDiscount + codeDiscountValue) / 100))} €`;
            totalPriceDisplay.textContent = `${Math.round((monthlyRentPrice * months) * (1 - (rentDiscount + codeDiscountValue) / 100)) * Number.parseInt(rentalUnits.value)} €`;
        } else {
            unitPriceDisplay.textContent  = `${Math.round((monthlyRentPrice * months) * (1 - rentDiscount / 100)) - codeDiscountValue} €`;
            totalPriceDisplay.textContent = `${Math.round((monthlyRentPrice * months) * (1 - rentDiscount / 100)) - codeDiscountValue * Number.parseInt(rentalUnits.value)} €`;
        }
        return;
    }

    unitPriceDisplay.textContent  = `${Math.round((monthlyRentPrice * months) * (1 - rentDiscount / 100))} €`;
    totalPriceDisplay.textContent = `${Math.round((monthlyRentPrice * months) * (1 - rentDiscount / 100)) * Number.parseInt(rentalUnits.value)} €`;
};