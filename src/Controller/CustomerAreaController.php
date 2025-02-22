<?php

namespace App\Controller;

use App\Form\CustomerUpdateType;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    public function __construct
    (
        EntityManagerInterface      $entityManager,
        CustomerRepository          $customerRepository,
        UserPasswordHasherInterface $passwordHasher
    )
    {
        $this->entityManager      = $entityManager;
        $this->customerRepository = $customerRepository;
        $this->passwordHasher     = $passwordHasher;
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

    #[Route(path: '/profile/delete', name: 'customer_area_profile_delete')]
    public function profileDelete(): Response
    {
        $customer = $this->getUser();
        $this->customerRepository->anonymise($this->customerRepository->findOneBy(['email' => $customer->getUserIdentifier()]));

        $this->addFlash('success', 'Votre compte a été supprimé avec succès');

        return $this->redirectToRoute('logout');
    }

    #[Route('/rentals', name: 'customer_area_rentals')]
    public function orders(): Response
    {
        $user     = $this->getUser();
        $customer = $this->customerRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        return $this->render('customer_area/rentals.html.twig', [
            'rentals' => $customer->getRentals(),
        ]);
    }

    #[Route('/invoices', name: 'customer_area_invoices')]
    public function order(): Response
    {
        $customer = $this->getUser();

        $invoices = $this->customerRepository->findOneBy(['email' => $customer->getUserIdentifier()])->getInvoices();

        return $this->render('customer_area/invoices.html.twig', [
            'invoices' => $invoices,
        ]);
    }
}