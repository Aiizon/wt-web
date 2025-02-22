<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Invoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
        foreach ($invoices as $invoice) {
            $month = $invoice->getDate()->format('Y-m');
            $grouped[$month][] = $invoice;
        }

        return $grouped;
    }
}
