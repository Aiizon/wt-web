<?php

namespace App\Controller\API;

use App\Repository\DiscountRepository;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use function Deployer\parse;

#[Route('/api')]
class DiscountController extends AbstractController
{
    private DiscountRepository $discountRepository;

    public function __construct(DiscountRepository $discountRepository)
    {
        $this->discountRepository = $discountRepository;
    }

    #[Route('/discount/valid', name: 'api_discount_valid', methods: ['POST'])]
    public function isValid(Request $request): JsonResponse
    {
        $code = json_decode($request->getContent())->code ?? null;

        if ($code === null) {
            throw new InvalidArgumentException('Un code est requis dans le corps de la requête.');
        }

        $discountCode = $this->discountRepository->findOneBy(['code' => $code, 'isActive' => true]);
        if ($discountCode === null) {
            return $this->json(['isValid' => false]);
        }

        return $this->json([
            'isValid' => true,
            'amount' => $discountCode->getAmount(),
            'isPercentage' => $discountCode->isPercentage()
        ]);
    }
}