<?php

namespace App\Controller;

use App\Repository\OfferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OfferController extends AbstractController
{
    private OfferRepository $offerRepository;

    public function __construct(OfferRepository $offerRepository)
    {
        $this->offerRepository = $offerRepository;
    }

    #[Route('/offer/{id}', name: 'offer', requirements: ['id' => '\d+'])]
    public function index(int $id, ?int $months): Response
    {
        $offer = $this->offerRepository->find($id);

        if (!$offer) {
            throw $this->createNotFoundException('L\'offre n\'existe pas');
        }

        return $this->render('offer/index.html.twig', [
            'controller_name' => 'OfferController',
        ]);
    }
}