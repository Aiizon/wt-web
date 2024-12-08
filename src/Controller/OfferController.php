<?php

namespace App\Controller;

use App\DTO\RentalDto;
use App\Entity\BillingType;
use App\Entity\Offer;
use App\Entity\Rental;
use App\Form\BillingTypeType;
use App\Form\RentQuantityType;
use App\Form\RentType;
use App\Handler\CustomerDataEditionHandler;
use App\Handler\RentalCreationHandler;
use App\Repository\BillingTypeRepository;
use App\Repository\DiscountRepository;
use App\Repository\OfferRepository;
use App\Repository\UnitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OfferController extends AbstractController
{
    private OfferRepository            $offerRepository;
    private BillingTypeRepository      $billingTypeRepository;
    private UnitRepository             $unitRepository;
    private CustomerDataEditionHandler $customerDataEditionHandler;
    private RentalCreationHandler      $rentalCreationHandler;

    public function __construct
    (
        OfferRepository            $offerRepository,
        BillingTypeRepository      $billingTypeRepository,
        UnitRepository             $unitRepository,
        CustomerDataEditionHandler $customerDataEditionHandler,
        RentalCreationHandler      $rentalCreationHandler
    )
    {
        $this->offerRepository            = $offerRepository;
        $this->billingTypeRepository      = $billingTypeRepository;
        $this->unitRepository             = $unitRepository;
        $this->customerDataEditionHandler = $customerDataEditionHandler;
        $this->rentalCreationHandler      = $rentalCreationHandler;
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

        $rentalDto = RentalDto::create(
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
        $rentForm = $this->createForm(RentType::class, $rentalDto);
        $rentForm->handleRequest($request);

        if ($rentForm->isSubmitted() && $rentForm->isValid()) {
            if (($rentalDto->offer->getMaxUnits() * $rentalDto->quantity) > $this->unitRepository->getAvailableUnitCount()) {
                $this->addFlash('danger', 'Il n\'y a pas assez d\'unités disponibles.');
                return $this->redirectToRoute('rent', ['id' => $id, 'billing' => $billingType->getId(), 'quantity' => $quantity]);
            }

            $this->customerDataEditionHandler->handle($rentalDto);
            $this->rentalCreationHandler->handle($rentalDto);

            $this->addFlash('success', 'La location a bien été enregistrée.');
            // @todo: redirect to 'my rentals' page
            return $this->redirectToRoute('offer', ['id' => $id]);
        }

        return $this->render('offer/rent.html.twig', [
            'offer'       => $offer,
            'billingType' => $billingType,
            'quantity'    => $quantity,
            'form'        => $rentForm,
        ]);
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