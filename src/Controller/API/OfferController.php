<?php

namespace App\Controller\API;

use App\Repository\UnitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class OfferController extends AbstractController
{
    private UnitRepository $unitRepository;

    public function __construct(UnitRepository $unitRepository)
    {
        $this->unitRepository = $unitRepository;
    }

    #[Route('/units/available', name: 'api_units_available')]
    public function getAvailableUnits(): JsonResponse
    {
        $availableUnits = $this->unitRepository->getAvailableUnitCount();

        return $this->json(['availableUnits' => $availableUnits]);
    }
}