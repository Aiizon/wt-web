export default async function getUnitAvailability() {
    try {
        const response = await fetch('/api/units/available');
        if (!response.ok) {
            throw new Error('Network response was not OK.');
        }
        const data = await response.json();
        return data.availableUnits;
    } catch (error) {
        console.error('There has been a problem with the fetch operation: ', error);
    }
}