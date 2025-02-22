<?php

namespace App\Handler;

use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Gotenberg\Exceptions\GotenbergApiErrored;
use Gotenberg\Exceptions\NoOutputFileInResponse;
use Gotenberg\Gotenberg;
use Gotenberg\Stream;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class InvoiceGenerationHandler
{
    private EntityManagerInterface $entityManager;
    private Environment            $twig;
    private string                 $appUrl;

    public function __construct
    (
        EntityManagerInterface $entityManager,
        Environment            $twig,
        string                 $appUrl = 'https://localhost:8000'
    ) {
        $this->entityManager = $entityManager;
        $this->twig          = $twig;
        $this->appUrl        = $appUrl;
    }

    /**
     * @throws NoOutputFileInResponse
     * @throws GotenbergApiErrored
     * @throws Exception
     */
    public function handle(Invoice $invoice): void
    {
        $gotenberg = Gotenberg::chromium('http://127.0.0.1:3000');

        try {
            $html = $this->twig->render('invoice/_invoice.html.twig', [
                'invoice' => $invoice,
            ]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $request = $gotenberg
            ->pdf()
            ->paperSize(21, 29.7)
            ->margins(1, 1, 1, 1)
            ->preferCssPageSize(true)
            ->waitForExpression('document.readyState === "complete"')
            ->html(Stream::string('invoice.html', $html))
        ;

        $location = '/tmp/invoice_' . $invoice->getId();
        if (!is_dir($location)) {
            mkdir($location);
        }
        $file = Gotenberg::save($request, $location);

        $invoice->setContent(base64_encode(file_get_contents($location . '/' . $file)));
        $invoice->setNeedsGeneration(false);

        $this->entityManager->persist($invoice);
        $this->entityManager->flush();

        unlink($location . '/' . $file);
    }
}