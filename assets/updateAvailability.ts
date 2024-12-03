import getUnitAvailability from "./getUnitAvailability";

export default async function updateAvailability(
    rentalUnits:       HTMLInputElement,
    offerUnitAmount:   number,
    rentQuantityInput: HTMLInputElement,
    availableDisplay:  HTMLElement,
    submitButton:      HTMLButtonElement
): Promise<void>
{
    const availableUnits: number     = await getUnitAvailability();
    const selectedUnitAmount: number = parseInt(rentalUnits.value) * offerUnitAmount;
    const quantity: string           = rentalUnits.value;

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