export type DiscountResponse = {
    isValid:      boolean;
    amount:       number|null;
    isPercentage: boolean|null;
};

export default async function checkDiscount(discount: string): Promise<DiscountResponse> {
    const response: Response = await fetch(
        '/api/discount/valid',
        {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ code: discount })
        }
    );
    return await response.json();
};