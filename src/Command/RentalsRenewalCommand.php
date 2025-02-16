<?php

namespace App\Command;

use App\Handler\InvoiceCreationHandler;
use App\Repository\RentalRepository;
use DateMalformedStringException;
use DateTimeImmutable;
use DateMalformedIntervalStringException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'rental:renew',
    description: 'Handle rentals that are due for renewal',
)]
class RentalsRenewalCommand extends Command
{
    private RentalRepository       $rentalRepository;
    private EntityManagerInterface $entityManager;
    private InvoiceCreationHandler $handler;

    public function __construct
    (
        RentalRepository       $rentalRepository,
        EntityManagerInterface $entityManager,
        InvoiceCreationHandler $invoiceCreationHandler
    )
    {
        $this->rentalRepository = $rentalRepository;
        $this->entityManager    = $entityManager;
        $this->handler          = $invoiceCreationHandler;

        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this
            ->addArgument(
                'date',
                InputArgument::OPTIONAL,
                'Date to consider',
                'now'
            );
    }

    /**
     * @throws DateMalformedIntervalStringException
     * @throws DateMalformedStringException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $date = new DateTimeImmutable($input->getArgument('date'));

        $rentals = $this->rentalRepository->findBy([
            'rentalEndDate' => null,
        ]);

        foreach ($rentals as $rental) {
            if ($rental->getNextDueDate()->format('Y-m-d') !== $date->format('Y-m-d')) {
                continue;
            }

            try {
                if (!$rental->isDoRenew()) {
                    $rental->setRentalEndDate($date);
                    $this->entityManager->flush();
                    $output->writeln("Ending rental {$rental->getId()}");
                    continue;
                }

                $invoice = $this->handler->handle($rental, $date);
                $output->writeln("Renewing rental {$rental->getId()} with invoice id {$invoice->getId()}");
                $this->entityManager->flush();
            } catch (Exception $e) {
                $output->writeln("Error renewing rental {$rental->getId()}: {$e->getMessage()}");
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }
}