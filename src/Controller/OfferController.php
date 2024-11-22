<?php

namespace App\Controller;

use App\Form\BillingTypeType;
use App\Form\RentQuantityType;
use App\Repository\BillingTypeRepository;
use App\Repository\OfferRepository;
use App\Repository\UnitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OfferController extends AbstractController
{
    private OfferRepository       $offerRepository;
    private BillingTypeRepository $billingTypeRepository;
    private UnitRepository        $unitRepository;

    public function __construct
    (
        OfferRepository       $offerRepository,
        BillingTypeRepository $billingTypeRepository,
        UnitRepository        $unitRepository
    )
    {
        $this->offerRepository       = $offerRepository;
        $this->billingTypeRepository = $billingTypeRepository;
        $this->unitRepository        = $unitRepository;
    }

    #[Route('/offer/{id}', name: 'offer', requirements: ['id' => '\d+'])]
    public function index(Request $request, int $id): Response
    {
        $offer = $this->offerRepository->find($id);
        if (!$offer) {
            throw $this->createNotFoundException('L\'offre n\'existe pas.');
        }
        if (!$offer->isActive()) {
            throw $this->createNotFoundException('L\'offre n\'est pas active.');
        }

        $billingId = $request->get('billing');
        if ($billingId) {
            $billingType = $this->billingTypeRepository->findOneBy(['id' => intval($billingId)]);
            if (!$billingType) {
                throw $this->createNotFoundException('Le type de facturation n\'existe pas.');
            }
        } else {
            $billingType = $this->billingTypeRepository->findOneBy(['months' => 1]);
        }

        $billingTypeForm  = $this->createForm(BillingTypeType::class);
        $rentQuantityForm = $this->createForm(RentQuantityType::class);

        return $this->render('offer/index.html.twig', [
            'offer'            => $offer,
            'billingType'      => $billingType,
            'billingTypeForm'  => $billingTypeForm,
            'rentQuantityForm' => $rentQuantityForm,
        ]);
    }

    #[Route('/api/units/available', name: 'api_units_available')]
    public function getAvailableUnits(): JsonResponse
    {
        $availableUnits = $this->unitRepository->getAvailableUnitCount();

        return $this->json(['availableUnits' => $availableUnits]);
    }
}