<?php

namespace App\Command;

use App\Handler\InvoiceGenerationHandler;
use App\Repository\InvoiceRepository;
use Exception;
use Gotenberg\Exceptions\GotenbergApiErrored;
use Gotenberg\Exceptions\NoOutputFileInResponse;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'invoice:generate',
    description: 'Generate invoices for rentals that are due for generation',
)]
class InvoiceGenerationCommand extends Command
{
    private InvoiceRepository        $invoiceRepository;
    private InvoiceGenerationHandler $handler;

    public function __construct
    (
        InvoiceRepository        $invoiceRepository,
        InvoiceGenerationHandler $invoiceGenerationHandler
    )
    {
        $this->invoiceRepository = $invoiceRepository;
        $this->handler           = $invoiceGenerationHandler;

        parent::__construct();
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $invoices = $this->invoiceRepository->findBy([
            'needsGeneration' => true
        ]);

        foreach ($invoices as $invoice) {
            try {
                $this->handler->handle($invoice);
                $output->writeln('Invoice generated for rental ' . $invoice->getRental()->getId());
            } catch (GotenbergApiErrored $e) {
                dd($e);
                $output->writeln('An API error occurred while generating the invoice : ' . $e->getMessage());
                return Command::FAILURE;
            } catch (NoOutputFileInResponse $e) {
                $output->writeln('A file error occurred while generating the invoice : ' . $e->getMessage());
                return Command::FAILURE;
            } catch (Exception $e) {
                $output->writeln('An unknown error occurred while generating the invoice : ' . $e->getMessage());
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }
}
