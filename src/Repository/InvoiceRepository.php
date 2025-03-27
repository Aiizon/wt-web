<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Invoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use IntlDateFormatter;

/**
 * @extends ServiceEntityRepository<Invoice>
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    public function findByCustomerGroupedByMonth(Customer $customer): array
    {
        $invoices = $this->createQueryBuilder('i')
            ->innerJoin('i.rental', 'r')
            ->where('r.customer = :customer')
            ->andWhere('i.content IS NOT NULL')
            ->andWhere('i.needsGeneration = false')
            ->setParameter('customer', $customer)
            ->orderBy('i.date', 'DESC')
            ->getQuery()
            ->getResult();

        $grouped = [];
        
        $formatter = new IntlDateFormatter(
            'fr_FR',
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            null,
            null,
            'MMMM yyyy'
        );
        
        foreach ($invoices as $invoice) {
            $month = $invoice->getDate()->format('Y-m');
            $translatedMonth = $formatter->format($invoice->getDate());
            $translatedMonth = mb_convert_case($translatedMonth, MB_CASE_TITLE, 'UTF-8');

            if (!isset($grouped[$month])) {
                $grouped[$month] = [
                    'label' => $translatedMonth,
                    'invoices' => []
                ];
            }
            
            $grouped[$month]['invoices'][] = $invoice;
        }
        
        ksort($grouped);

        return $grouped;
    }
}
