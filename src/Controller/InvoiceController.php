<?php

namespace App\Controller;

use App\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InvoiceController extends AbstractController
{
    private InvoiceRepository $invoiceRepository;

    public function __construct(InvoiceRepository $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    #[Route('/invoice/{id}', name: 'invoice_show', requirements: ['id' => '\d+'])]
    public function show(int $id): Response
    {
        $invoice = $this->invoiceRepository->find($id);

        if ($invoice === null) {
            throw $this->createNotFoundException('Invoice not found');
        }

        if ($this->getUser() !== $invoice->getRental()->getCustomer()) {
            throw $this->createAccessDeniedException('You are not allowed to access this invoice');
        }

        if ($invoice->getContent() === null || $invoice->isNeedsGeneration()) {
            throw $this->createNotFoundException('Invoice not generated yet');
        }

        $content = base64_decode(stream_get_contents($invoice->getContent()));

        $response = new Response($content);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="invoice_' . $id . '.pdf"');

        return $response;
    }

    #[Route('/invoice/{id}/generate', name: 'invoice_generate', requirements: ['id' => '\d+'])]
    public function index(int $id): Response
    {
        $invoice = $this->invoiceRepository->find($id);

        if ($invoice === null) {
            throw $this->createNotFoundException('Invoice not found');
        }

        return $this->render('invoice/_invoice.html.twig', [
            'invoice' => $invoice,
        ]);
    }
}
