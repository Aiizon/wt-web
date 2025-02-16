<?php

namespace App\Handler;

use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;
use Gotenberg\Exceptions\GotenbergApiErrored;
use Gotenberg\Exceptions\NoOutputFileInResponse;
use Gotenberg\Gotenberg;

class InvoiceGenerationHandler
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * @throws NoOutputFileInResponse
     * @throws GotenbergApiErrored
     */
    public function handle(Invoice $invoice): void
    {
        $gotenberg = Gotenberg::chromium('http://127.0.0.1:3000');

        $request = $gotenberg
            ->pdf()
            ->singlePage()
            ->url(getenv('APP_URL') . '/invoice/' . $invoice->getId() . '/generate')
        ;

        $file = Gotenberg::save($request, '/tmp/invoice_' . $invoice->getId() . '.pdf');

        $invoice->setContent(base64_encode(file_get_contents($file)));
        $invoice->setNeedsGeneration(false);

        $this->entityManager->persist($invoice);
        $this->entityManager->flush();

        unlink($file);
    }
}