<?php

namespace App\Repository;

use App\Entity\Bay;
use App\Entity\Customer;
use App\Entity\Gravity;
use App\Entity\Intervention;
use App\Entity\Unit;
use App\Entity\User;
use Deployer\Documentation\ApiGen;
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
        $rsm->addJoinedEntityFromClassMetadata(Unit::class, 'u', 'i', 'units', ['id' => 'unit_id']);
        
        $sql = '
            SELECT i.*, u.id AS unit_id, u.name AS name, u.bay_id AS bay_id
            FROM intervention i
                INNER JOIN unit_intervention ui ON ui.intervention_id = i.id
                INNER JOIN unit u ON ui.unit_id = u.id
                INNER JOIN bay b ON b.id = u.bay_id
                INNER JOIN rental_unit ru ON ui.unit_id = ru.unit_id
                INNER JOIN rental r ON r.id = ru.rental_id
            WHERE r.customer_id = :customerId
                AND (r.rental_end_date IS NULL OR r.rental_end_date < CURRENT_DATE())
        ';
        
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('customerId', $customer->getId());
        
        return $query->getArrayResult();
    }

    public function findInterventionsForUnitAndCustomer(Customer $customer, Unit $unit): array
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata(Intervention::class, 'i');
        $rsm->addJoinedEntityFromClassMetadata(Gravity::class, 'g', 'i', 'gravity', ['id' => 'gravity_id']);
        $rsm->addJoinedEntityFromClassMetadata(User::class, 'us', 'i', 'user', [
            'id'        => 'user_id',
            'firstName' => 'user_first_name',
            'lastName'  => 'user_last_name'
        ]);
        
        $sql = '
            SELECT i.*, 
                   g.id AS gravity_id, g.name AS name, 
                   us.id AS user_id, us.first_name AS user_first_name, us.last_name AS user_last_name
            FROM intervention i
                 INNER JOIN unit_intervention ui ON ui.intervention_id = i.id
                 INNER JOIN unit u ON ui.unit_id = u.id
                 INNER JOIN rental_unit ru ON ui.unit_id = ru.unit_id
                 INNER JOIN rental r ON r.id = ru.rental_id
                 LEFT JOIN gravity g ON g.id = i.gravity_id
                 LEFT JOIN user us ON us.id = i.user_id
            WHERE r.customer_id = :customerId
                AND (r.rental_end_date IS NULL OR r.rental_end_date < CURRENT_DATE())
                AND u.id = :unitId
            ORDER BY i.start_date DESC
        ';

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('customerId', $customer->getId());
        $query->setParameter('unitId', $unit->getId());

        return $query->getArrayResult();
    }
}
