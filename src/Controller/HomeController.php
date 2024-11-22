<?php

namespace App\Controller;

use App\Form\BillingTypeType;
use App\Repository\BillingTypeRepository;
use App\Repository\OfferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    private OfferRepository $offerRepository;
    private BillingTypeRepository $billingTypeRepository;

    public function __construct
    (
        OfferRepository       $offerRepository,
        BillingTypeRepository $billingTypeRepository
    )
    {
        $this->offerRepository       = $offerRepository;
        $this->billingTypeRepository = $billingTypeRepository;
    }


    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $offers          = $this->offerRepository->findAll();
        $billingTypes    = $this->billingTypeRepository->findAll();
        $billingTypeForm = $this->createForm(BillingTypeType::class);

        return $this->render('home/index.html.twig', [
            'offers'          => $offers,
            'billingTypes'    => $billingTypes,
            'billingTypeForm' => $billingTypeForm,
        ]);
    }
}