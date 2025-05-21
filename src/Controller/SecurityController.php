<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class SecurityController extends AbstractController
{
    private EmailVerifier               $emailVerifier;
    private AuthenticationUtils         $authenticationUtils;
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface      $entityManager;
    private TranslatorInterface         $translator;
    private CustomerRepository          $customerRepository;

    public function __construct
    (
        EmailVerifier               $emailVerifier,
        AuthenticationUtils         $authenticationUtils,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface      $entityManager,
        TranslatorInterface         $translator,
        CustomerRepository          $customerRepository,
    ) {
        $this->emailVerifier          = $emailVerifier;
        $this->authenticationUtils    = $authenticationUtils;
        $this->passwordHasher         = $userPasswordHasher;
        $this->entityManager          = $entityManager;
        $this->translator             = $translator;
        $this->customerRepository     = $customerRepository;
    }

    #[Route(path: '/login', name: 'login')]
    public function login(): Response
    {
        // get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'logout')]
    public function logout(): void
    {
        // This method is intercepted by the firewall, so it will never be executed
        // If you're reading this, you're cute ! <3
    }

    #[Route('/register', name: 'register')]
    public function register(Request $request): Response
    {
        $customer = new Customer();
        $form = $this->createForm(RegistrationFormType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $customer->setPassword($this->passwordHasher->hashPassword($customer, $plainPassword));

            $this->entityManager->persist($customer);
            $this->entityManager->flush();

            // generate a signed url and email it to the user
            //$this->emailVerifier->sendEmailConfirmation('verify_email', $customer,
            //    (new TemplatedEmail())
            //        ->from(new Address('noreply@worktogether.fr', 'Automate Work Together'))
            //        ->to((string) $customer->getEmail())
            //        ->subject('Veuillez confirmer votre e-mail')
            //        ->htmlTemplate('security/confirmation_email.html.twig')
            //);

            $this->addFlash('success', 'Votre compte a été créé avec succès. Veuillez vous connecter.');

            return $this->redirectToRoute('login');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'verify_email')]
    public function verifyUserEmail(Request $request): Response
    {
        $id = $request->query->get('id');

        if (null === $id) {
            return $this->redirectToRoute('register');
        }

        $customer = $this->customerRepository->find($id);

        if (null === $customer) {
            return $this->redirectToRoute('register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $customer);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $this->translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('register');
        }

        $this->addFlash('success', 'Votre e-mail a bien été vérifié.');

        return $this->redirectToRoute('home');
    }
}
