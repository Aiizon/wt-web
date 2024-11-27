const billingTypeSelect = document.querySelector('select');
const offers = document.querySelectorAll('.offer');

const updateBillingType = () => {
    const selectedBillingType = billingTypeSelect.querySelector('option:checked');
    const months = selectedBillingType.innerText;
    const discount = selectedBillingType.getAttribute('data-discount');
    offers.forEach(offer => {
        const billingTypeInput = offer.querySelector('.billingType');
        const totalPriceDisplay = offer.querySelector('.totalPriceDisplay');
        const monthlyRentPrice = parseInt(offer.querySelector('.monthlyPriceDisplay').getAttribute('data-rent'));

        billingTypeInput.value = selectedBillingType.value;

        if (!Number.isInteger(parseInt(months))) {
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