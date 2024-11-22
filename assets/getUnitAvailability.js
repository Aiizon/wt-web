export default async function getUnitAvailability() {
    const response = await fetch('/api/units/available');
    return await JSON.parse(response)[0];
}