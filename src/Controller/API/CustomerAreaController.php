<?php

namespace App\Controller\API;

use App\Entity\Customer;
use App\Repository\RentalRepository;
use App\Repository\TokenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/me', name: 'api_customer_area_')]
class CustomerAreaController extends AbstractController
{
    private RentalRepository $rentalRepository;
    private TokenRepository  $tokenRepository;
    
    public function __construct
    (
        RentalRepository $rentalRepository,
        TokenRepository  $tokenRepository
    ) {
        $this->rentalRepository = $rentalRepository;
        $this->tokenRepository  = $tokenRepository;
    }
    
    #[Route('/unit/status', name: 'status', methods: ['GET'])]
    public function status(Request $request): JsonResponse
    {
        $checkResult = $this->checkToken($request);
        
        if ($checkResult instanceof JsonResponse) {
            return $checkResult;
        }
        
        $units = $this->rentalRepository->findActiveUnitsForCustomer($checkResult);
        
        return $this->json(['units' => $units], Response::HTTP_OK, [], ['groups' => 'unit:status']);
    }
    
    private function checkToken(Request $request): JsonResponse|Customer
    {
        $tokenHeader = $request->headers->get('Authorization');
        if (null === $tokenHeader) {
            return new JsonResponse(['error' => 'Token is required'], Response::HTTP_BAD_REQUEST);
        }
        
        $token = $this->tokenRepository->findOneBy(['value' => $tokenHeader]);
        if (null === $token) {
            return new JsonResponse(['error' => 'Invalid token'], Response::HTTP_UNAUTHORIZED);
        }
        
        $customer = $token->getUser();
        if (!$customer instanceof Customer) {
            return new JsonResponse(['error' => 'Invalid token'], Response::HTTP_UNAUTHORIZED);
        }
        
        return $customer;
    }
}
