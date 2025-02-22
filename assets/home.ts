const billingTypeSelect: HTMLSelectElement = document.querySelector('select');
const offers: NodeListOf<HTMLElement>      = document.querySelectorAll('.offer');

const updateBillingType = () => {
    const selectedBillingType: HTMLInputElement = billingTypeSelect.querySelector('option:checked');
    const months:   number                      = parseInt(selectedBillingType.getAttribute('data-months'));
    const discount: number                      = parseInt(selectedBillingType.getAttribute('data-discount'));
    offers.forEach(offer => {
        const billingTypeInput:  HTMLInputElement = offer.querySelector('.billingType');
        const totalPriceDisplay: HTMLInputElement = offer.querySelector('.totalPriceDisplay');
        const monthlyRentPrice:  number           = parseInt(offer.querySelector('.monthlyPriceDisplay').getAttribute('data-rent'));

        billingTypeInput.value = selectedBillingType.value;

        if (!Number.isInteger(months)) {
            totalPriceDisplay.textContent = `${monthlyRentPrice}€`;
            return;
        }
        totalPriceDisplay.textContent = `${Math.round((monthlyRentPrice * months) * (1 + discount / 100))}€`;
    });
};

updateBillingType();

billingTypeSelect.addEventListener('change', e => {
    e.preventDefault();
    updateBillingType();
});