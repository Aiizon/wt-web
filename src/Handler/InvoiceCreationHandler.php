<?php

namespace App\Handler;

use App\Entity\Invoice;
use App\Entity\Rental;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class InvoiceCreationHandler
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function handle(Rental $rental, DateTimeImmutable $date): Invoice
    {
        $invoice = new Invoice();
        $invoice->setTotalRentPrice($rental->getTotalRentPrice());
        $invoice->setBillingAddress($rental->getCustomer()->getAddress());
        $invoice->setRental($rental);
        $invoice->setDate($date);
        $invoice->setNeedsGeneration(true);

        $this->entityManager->persist($invoice);

        $rental->addInvoice($invoice);

        $this->entityManager->flush();

        return $invoice;
    }
}