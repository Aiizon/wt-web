import getUnitAvailability from "./getUnitAvailability";

export default async function updateAvailability(rentalUnits, offerUnitAmount, rentQuantityInput, availableDisplay, submitButton) {
    const availableUnits = await getUnitAvailability();
    const selectedUnitAmount = parseInt(rentalUnits.value) * offerUnitAmount;
    const quantity = rentalUnits.value;

    if (selectedUnitAmount <= availableUnits) {
        availableDisplay.textContent = 'Disponible';
        availableDisplay.classList.replace('text-danger', 'text-success');
    } else {
        availableDisplay.textContent = 'Non disponible';
        availableDisplay.classList.replace('text-success', 'text-danger');
    }

    if (rentQuantityInput !== null) {
        rentQuantityInput.value = quantity;
    }

    submitButton.disabled = selectedUnitAmount > availableUnits;
}