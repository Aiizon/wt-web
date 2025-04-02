<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Rental;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rental>
 */
class RentalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rental::class);
    }

    public function findActiveUnitsByCustomer(Customer $customer): array
    {
        $rentals = $this->createQueryBuilder('r')
            ->andWhere('r.customer = :customer')
            ->andWhere('r.rentalEndDate IS NULL OR r.rentalEndDate > CURRENT_DATE()')
            ->setParameter('customer', $customer)
            ->getQuery()
            ->getResult()
        ;

        $units = [];

        foreach ($rentals as $rental) {
            $units = array_merge($units, $rental->getUnits()->toArray());
        }

        $grouped = [];

        foreach ($units as $unit) {
            $bay = $unit->getBay();

            if (!isset($grouped[$bay->getName()])) {
                $grouped[$bay->getName()] = [
                    'units' => [],
                ];
            }
            
            $grouped[$bay->getName()]['units'][] = $unit;
        }
        
        return $grouped;
    }
}
