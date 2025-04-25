<?php

namespace App\Controller;

use App\Document\Feedback;
use App\DTO\RentalDto;
use App\Entity\BillingType;
use App\Entity\Offer;
use App\Form\BillingTypeType;
use App\Form\FeedbackType;
use App\Form\RentQuantityType;
use App\Form\RentType;
use App\Handler\CustomerDataEditionHandler;
use App\Handler\RentalCreationHandler;
use App\Repository\BillingTypeRepository;
use App\Repository\CustomerRepository;
use App\Repository\OfferRepository;
use App\Repository\UnitRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\LockException;
use Doctrine\ODM\MongoDB\Mapping\MappingException;
use Doctrine\ODM\MongoDB\MongoDBException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

class OfferController extends AbstractController
{
    private OfferRepository            $offerRepository;
    private BillingTypeRepository      $billingTypeRepository;
    private UnitRepository             $unitRepository;
    private CustomerRepository         $customerRepository;
    private CustomerDataEditionHandler $customerDataEditionHandler;
    private RentalCreationHandler      $rentalCreationHandler;
    private DocumentManager            $documentManager;

    public function __construct
    (
        OfferRepository            $offerRepository,
        BillingTypeRepository      $billingTypeRepository,
        UnitRepository             $unitRepository,
        CustomerRepository         $customerRepository,
        CustomerDataEditionHandler $customerDataEditionHandler,
        RentalCreationHandler      $rentalCreationHandler,
        DocumentManager            $documentManager
    )
    {
        $this->offerRepository            = $offerRepository;
        $this->billingTypeRepository      = $billingTypeRepository;
        $this->unitRepository             = $unitRepository;
        $this->customerRepository         = $customerRepository;
        $this->customerDataEditionHandler = $customerDataEditionHandler;
        $this->rentalCreationHandler      = $rentalCreationHandler;
        $this->documentManager            = $documentManager;
    }

    /**
     * @throws MongoDBException
     */
    #[Route('/offer/{id}', name: 'offer', requirements: ['id' => '\d+'])]
    public function index(Request $request, int $id): Response
    {
        $offer = $this->offerRepository->find($id);
        $this->assertOfferValid($offer);

        $billingType = $this->getBillingType(intval($request->get('billing')));

        $billingTypeForm  = $this->createForm(BillingTypeType::class);
        $rentQuantityForm = $this->createForm(RentQuantityType::class);

        if ($this->isGranted('ROLE_ADMIN')) {
            $feedbacks = [];// $this->documentManager->getRepository(Feedback::class)->findBy(['offerId' => $offer->getId()]);
        } else {
            $feedbacks = [];// $this->documentManager->getRepository(Feedback::class)->findBy(['offerId' => $offer->getId(), 'isVisible' => true]);
        }

        if ($this->getUser()) {
            $newFeedback = new Feedback();

            $newFeedback->setCustomerIdentifier($this->getUser()->getUserIdentifier());
            $newFeedback->offerId = $offer->getId();

            $feedbackForm = $this->createForm(FeedbackType::class, $newFeedback, [
                'attr' => [
                    'class' => 'd-flex flex-row justify-content-evenly align-items-center',
                ],
            ]);

            $feedbackForm->handleRequest($request);

            if ($feedbackForm->isSubmitted() && $feedbackForm->isValid()) {
                $this->documentManager->persist($newFeedback);
                $this->documentManager->flush();
                $this->addFlash('success', 'Le commentaire a bien été enregistré.');
                return $this->redirectToRoute('offer', ['id' => $id]);
            }
        }

        return $this->render('offer/index.html.twig', [
            'offer'            => $offer,
            'billingType'      => $billingType,
            'billingTypeForm'  => $billingTypeForm,
            'rentQuantityForm' => $rentQuantityForm,
            'feedbacks'        => $feedbacks,
            'feedbackForm'     => $feedbackForm ?? null,
        ]);
    }

    /**
     * @throws MappingException
     * @throws MongoDBException
     * @throws Throwable
     */
    #[Route('/offer/{offerId}/feedback/{feedbackId}/toggle', name: 'toggle_feedback', requirements: ['id' => '\d+', 'feedbackId' => '^[0-9a-fA-F]{24}$'])]
    public function toggleFeedback(string $feedbackId): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $feedback = $this->documentManager->getRepository(Feedback::class)->find($feedbackId);
        if (!$feedback) {
            throw $this->createNotFoundException('Le commentaire n\'existe pas.');
        }

        $feedback->isVisible = !$feedback->isVisible;
        $this->documentManager->flush();

        $this->addFlash('success', 'Le commentaire a bien été caché.');
        return $this->redirectToRoute('offer', ['id' => $feedback->offerId]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws \Doctrine\DBAL\Exception
     */
    #[Route('/offer/{id}/rent', name: 'rent', requirements: ['id' => '\d+'])]
    public function rent(Request $request, int $id): Response
    {
        $user = $this->getUser();
        $customer = $this->customerRepository->findOneBy(['userIdentifier' => $user->getUserIdentifier()]);

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
            try {
                $this->rentalCreationHandler->handle($rentalDto);
            } catch (Exception $e) {
                $this->addFlash('danger', 'Erreur lors de la sauvegarde de la commande.');
            }

            $this->addFlash('success', 'La location a bien été enregistrée.');
            return $this->redirectToRoute('customer_area_rentals', ['id' => $id]);
        }

        return $this->render('offer/rent.html.twig', [
            'offer'       => $offer,
            'billingType' => $billingType,
            'quantity'    => $quantity,
            'form'        => $rentForm,
        ]);
    }

    private function assertOfferValid(?Offer $offer): void
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