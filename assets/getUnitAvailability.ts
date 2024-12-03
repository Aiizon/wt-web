export type unitAvailability = {
    availableUnits: number;
}

export default async function getUnitAvailability(): Promise<number> {
    try {
        const response: Response = await fetch('/api/units/available');
        if (!response.ok) {
            throw new Error('Network response was not OK.');
        }
        const data: unitAvailability = await response.json();
        return data.availableUnits;
    } catch (error) {
        console.error('There has been a problem with the fetch operation: ', error);
    }
}