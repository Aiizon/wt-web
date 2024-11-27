<?php

namespace App\Controller;

use App\DTO\RentDTO;
use App\Entity\BillingType;
use App\Entity\Offer;
use App\Form\BillingTypeType;
use App\Form\RentQuantityType;
use App\Form\RentType;
use App\Repository\BillingTypeRepository;
use App\Repository\DiscountRepository;
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
        $this->assertOfferValid($offer);

        $billingType = $this->getBillingType(intval($request->get('billing')));

        $billingTypeForm  = $this->createForm(BillingTypeType::class);
        $rentQuantityForm = $this->createForm(RentQuantityType::class);

        return $this->render('offer/index.html.twig', [
            'offer'            => $offer,
            'billingType'      => $billingType,
            'billingTypeForm'  => $billingTypeForm,
            'rentQuantityForm' => $rentQuantityForm,
        ]);
    }

    #[Route('/offer/{id}/rent', name: 'rent', requirements: ['id' => '\d+'])]
    public function rent(Request $request, int $id): Response
    {
        $customer = $this->getUser();

        $offer = $this->offerRepository->find($id);
        $this->assertOfferValid($offer);

        $billingType = $this->getBillingType(intval($request->get('billing')));

        $quantity = intval($request->get('quantity'));
        if (!$quantity) {
            $quantity = 1;
        }

        $rentDto = RentDTO::create(
            $offer,
            $customer,
            $billingType,
            null,
            $quantity,
            false,
            $customer->getFirstName(),
            $customer->getLastName(),
            $customer->getAddress()
        );
        $rentForm = $this->createForm(RentType::class, $rentDto);

        return $this->render('offer/rent.html.twig', [
            'offer'       => $offer,
            'billingType' => $billingType,
            'quantity'    => $quantity,
            'form'        => $rentForm,
        ]);
    }

    #[Route('/api/units/available', name: 'api_units_available')]
    public function getAvailableUnits(): JsonResponse
    {
        $availableUnits = $this->unitRepository->getAvailableUnitCount();

        return $this->json(['availableUnits' => $availableUnits]);
    }

    private function assertOfferValid(Offer $offer): void
    {
        if (!$offer) {
            throw $this->createNotFoundException('L\'offre n\'existe pas.');
        }
        if (!$offer->isActive()) {
            throw $this->createNotFoundException('L\'offre n\'est pas active.');
        }
    }

    private function getBillingType(int $id): BillingType
    {
        if ($id) {
            $billingType = $this->billingTypeRepository->findOneBy(['id' => $id]);
            if (!$billingType) {
                throw $this->createNotFoundException('Le type de facturation n\'existe pas.');
            }
        } else {
            $billingType = $this->billingTypeRepository->findOneBy(['months' => 1]);
        }

        return $billingType;
    }
}