<?php

namespace App\Handler;

use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Gotenberg\Exceptions\GotenbergApiErrored;
use Gotenberg\Exceptions\NoOutputFileInResponse;
use Gotenberg\Gotenberg;
use Gotenberg\Stream;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class InvoiceGenerationHandler
{
    private MailerInterface        $mailer;
    private EntityManagerInterface $entityManager;
    private Environment            $twig;
    private string                 $appUrl;

    public function __construct
    (
        MailerInterface        $mailer,
        EntityManagerInterface $entityManager,
        Environment            $twig,
        #[Autowire('%env(GOTENBERG_URL)%')]
        string                 $appUrl = 'https://localhost:8000'
    ) {
        $this->mailer        = $mailer;
        $this->entityManager = $entityManager;
        $this->twig          = $twig;
        $this->appUrl        = $appUrl;
    }

    /**
     * @throws NoOutputFileInResponse
     * @throws GotenbergApiErrored
     * @throws Exception
     * @throws TransportExceptionInterface
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

        $email = (new Email())
            ->from('automate@worktogether.com')
            ->to($invoice->getRental()->getCustomer()->getEmail())
            ->subject('Facture pour la location N°' . $invoice->getRental()->getId())
            ->html($this->twig->render('email/invoice.html.twig', [
                'invoice' => $invoice,
            ]))
            ->attachFromPath($location . '/' . $file, `invoice-${invoice->getNumber()}.pdf`)
        ;

        $this->mailer->send($email);

        unlink($location . '/' . $file);
    }
}