<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Intervention;
use App\Entity\Unit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Intervention>
 */
class InterventionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Intervention::class);
    }

    /**
     * @throws Exception
     */
    public function findInterventionsForCustomer(Customer $customer): array
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata(Intervention::class, 'i');
        // $rsm->addJoinedEntityFromClassMetadata(Unit::class, 'u', 'i', 'units', ['id' => 'unit_id']);
        
        $sql = '
            SELECT i.*
            FROM intervention i
            INNER JOIN unit_intervention ui ON ui.intervention_id = i.id
            INNER JOIN unit u ON ui.unit_id = u.id
            INNER JOIN rental_unit ru ON ru.rental_id = u.id
            INNER JOIN rental r ON r.id = ru.rental_id
            WHERE r.customer_id = :customerId
            AND (r.rental_end_date IS NULL OR r.rental_end_date < CURRENT_DATE())
        ';
        
        $conn = $this->getEntityManager()->getConnection();
        $query = $conn->prepare($sql);
        $query->bindValue('customerId', $customer->getId());
        dd($query->executeQuery()->fetchAllAssociative(), $customer->getId());
        
//        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
//        $query->setParameter('customerId', $customer->getId());
        
        return $query->getArrayResult();
    }
}
