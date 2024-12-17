<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/me')]
class CustomerAreaController extends AbstractController
{
    #[Route('/', name: 'customer_area_home')]
    public function index(): Response
    {
        return $this->render('customer_area/index.html.twig');
    }

    #[Route('/profile', name: 'customer_area_profile')]
    public function profile(): Response
    {
        return $this->render('customer_area/profile.html.twig');
    }

    #[Route('/orders', name: 'customer_area_orders')]
    public function orders(): Response
    {
        return $this->render('customer_area/orders.html.twig');
    }

    #[Route('/invoices', name: 'customer_area_invoices')]
    public function order(): Response
    {
        return $this->render('customer_area/invoices.html.twig');
    }
}