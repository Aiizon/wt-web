<?php

namespace App\Controller;

use App\DTO\TokenCreationDTO;
use App\Form\CustomerUpdateType;
use App\Form\TokenCreationType;
use App\Form\UnitUsageType;
use App\Repository\CustomerRepository;
use App\Repository\InvoiceRepository;
use App\Repository\RentalRepository;
use App\Repository\TokenRepository;
use App\Repository\UnitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/me')]
class CustomerAreaController extends AbstractController
{
    private EntityManagerInterface      $entityManager;
    private CustomerRepository          $customerRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private InvoiceRepository           $invoiceRepository;
    private UnitRepository              $unitRepository;
    private RentalRepository            $rentalRepository;
    private TokenRepository             $tokenRepository;

    public function __construct
    (
        EntityManagerInterface      $entityManager,
        CustomerRepository          $customerRepository,
        UserPasswordHasherInterface $passwordHasher,
        InvoiceRepository           $invoiceRepository,
        UnitRepository              $unitRepository,
        RentalRepository            $rentalRepository,
        TokenRepository             $tokenRepository
    )
    {
        $this->entityManager      = $entityManager;
        $this->customerRepository = $customerRepository;
        $this->passwordHasher     = $passwordHasher;
        $this->invoiceRepository  = $invoiceRepository;
        $this->unitRepository     = $unitRepository;
        $this->rentalRepository   = $rentalRepository;
        $this->tokenRepository    = $tokenRepository;
    }

    #[Route('/', name: 'customer_area_home')]
    public function index(): Response
    {
        return $this->render('customer_area/index.html.twig');
    }

    #[Route(path: '/profile', name: 'customer_area_profile')]
    public function profile(): Response
    {
        return $this->render('customer_area/profile.html.twig');
    }

    #[Route('/profile/edit', name: 'customer_area_profile_edit')]
    public function profileEdit(Request $request): Response
    {
        $customer = $this->getUser();

        $form = $this->createForm(CustomerUpdateType::class, $customer, [
            'attr' => [
                'class' => 'd-flex flex-column justify-content-center align-items-center form'
            ]
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('plainPassword')->getData()) {
                $customer->setPassword(
                    $this->passwordHasher->hashPassword($customer, $form->get('plainPassword')->getData())
                );
            }
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre profil a été mis à jour avec succès');

            return $this->redirectToRoute('customer_area_profile');
        }

        return $this->render('customer_area/profile_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/tokens', name: 'customer_area_tokens')]
    public function manageTokens(): Response
    {
        $user   = $this->getUser();
        $tokens = $this->tokenRepository->findBy(['user' => $user]);

        return $this->render('customer_area/tokens.html.twig', [
            'tokens' => $tokens,
        ]);
    }

    /**
     * @throws RandomException
     */
    #[Route(path: '/token/create', name: 'customer_area_token_create')]
    public function createToken(Request $request): Response
    {
        $user = $this->getUser();
        $dto  = TokenCreationDTO::create('', $user);

        $form = $this->createForm(TokenCreationType::class, $dto, [
            'attr' => [
                'class' => 'd-flex flex-column justify-content-center align-items-center form'
            ]
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $token = $this->tokenRepository->generate($dto);

            $this->addFlash('success', 'Token généré avec succès : ' . $token->getValue());

            return $this->redirectToRoute('customer_area_tokens');
        }

        return $this->render('customer_area/token_create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/token/{id}/delete', name: 'customer_area_token_delete')]
    public function deleteToken(int $id): Response
    {
        $user = $this->getUser();
        $token = $this->tokenRepository->findOneBy([
            'id' => $id,
            'user' => $user
        ]);

        if (!$token) {
            throw $this->createNotFoundException('Token non trouvé');
        }

        $this->entityManager->remove($token);
        $this->entityManager->flush();

        $this->addFlash('success', 'Token supprimé avec succès');

        return $this->redirectToRoute('customer_area_tokens');
    }

    #[Route(path: '/profile/delete', name: 'customer_area_profile_delete')]
    public function profileDelete(): Response
    {
        $customer = $this->getUser();
        $this->customerRepository->anonymise($this->customerRepository->findOneBy(['email' => $customer->getUserIdentifier()]));

        $this->addFlash('success', 'Votre compte a été supprimé avec succès');

        return $this->redirectToRoute('logout');
    }

    #[Route('/rentals', name: 'customer_area_rentals')]
    public function rentals(): Response
    {
        $user     = $this->getUser();
        $customer = $this->customerRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        return $this->render('customer_area/rentals.html.twig', [
            'rentals' => $customer->getRentals(),
        ]);
    }

    #[Route('/rental/{id}/cancel', name: 'customer_area_cancel_rental', requirements: ['id' => '\d+'])]
    public function cancelRental(int $id): Response
    {
        $user     = $this->getUser();
        $customer = $this->customerRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        $rental = $this->rentalRepository->findOneBy([
            'id' => $id,
            'customer' => $customer
        ]);

        if (!$rental) {
            throw $this->createNotFoundException('Location non trouvée');
        }

        $rental->setDoRenew(false);
        $this->entityManager->flush();
        $this->addFlash('success', 'Location annulée avec succès');

        return $this->redirectToRoute('customer_area_rentals');
    }

    #[Route('/invoices', name: 'customer_area_invoices')]
    public function order(): Response
    {
        $user = $this->getUser();
        $customer = $this->customerRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        $invoices = $this->invoiceRepository->findByCustomerGroupedByMonth($customer);

        return $this->render('customer_area/invoices.html.twig', [
            'invoices' => $invoices,
        ]);
    }

    #[Route('/units', name: 'customer_area_units')]
    public function units(): Response
    {
        $user = $this->getUser();
        $customer = $this->customerRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        $rentals = $this->rentalRepository->findBy([
            'customer' => $customer,
            'rentalEndDate' => null
        ]);
        $units = [];

        foreach ($rentals as $rental) {
            $units = array_merge($units, $rental->getUnits()->toArray());
        }

        return $this->render('customer_area/units.html.twig', [
            'units' => $units,
        ]);
    }

    #[Route('/unit/{id}/usage', name: 'customer_area_update_unit_usage', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function updateUnitUsage(Request $request, int $id): Response
    {
        $user     = $this->getUser();
        $customer = $this->customerRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        $unit     = $this->unitRepository->find($id);
        $rentals  = $this->rentalRepository->findBy([
            'customer' => $customer,
            'rentalEndDate' => null
        ]);

        $units = [];
        foreach ($rentals as $rental) {
            $units = array_merge($units, $rental->getUnits()->toArray());
        }

        if (!$unit || !in_array($unit, $units)) {
            throw $this->createNotFoundException('Unité non trouvée');
        }

        $form = $this->createForm(UnitUsageType::class, $unit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Unité mise à jour avec succès.');
            return $this->redirectToRoute('customer_area_units');
        }

        return $this->render('customer_area/unit_usage.html.twig', [
            'form' => $form->createView(),
            'unit' => $unit
        ]);
    }
}