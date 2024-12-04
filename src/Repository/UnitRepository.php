<?php

namespace App\Repository;

use App\Entity\Unit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use PDO;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @extends ServiceEntityRepository<Unit>
 */
class UnitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Unit::class);
    }

    /**
     * Gets the number of available units.
     * @throws Exception
     */
    public function getAvailableUnitCount(): int
    {
        // Get the rented units with their id, and then check if the unit is not in the list of rented units.
        $query = '
            SELECT count(u.id) as availableUnits
            FROM unit u
            WHERE u.id NOT IN (
                SELECT ru.unit_id
                FROM rental r
                INNER JOIN rental_unit ru ON r.id = ru.rental_id
                WHERE r.rental_end_date is null
            )
        ';

        $stmt = $this->getEntityManager()->getConnection()->prepare($query);
        return $stmt->executeQuery()->fetchNumeric()[0];
    }

    public function getRentedUnits(): int
    {
        $conn = $this->getEntityManager()->getConnection();

        $conn->prepare('CALL GetRentedUnits(@count);')->executeStatement();
        return $conn->fetchNumeric('SELECT @count')[0];
    }

    public function getRentedUnitsByBayProc(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $conn->prepare('SELECT 1')->executeStatement();
        return $conn->fetchAllAssociative('CALL GetRentedUnitsByBay();');
    }

    /**
     * Gets an array of available units for rental.
     * @throws Exception
     */
    public function getAvailableUnitsForRental(int $quantity)
    {
        $query = '
        SELECT u.id
        FROM unit u
        WHERE u.bay_id IN (
            SELECT u.bay_id
            FROM unit u
            WHERE u.id NOT IN (
                SELECT ru.unit_id
                FROM rental r
                INNER JOIN rental_unit ru ON r.id = ru.rental_id
                WHERE r.rental_end_date is null
            )
            GROUP BY u.bay_id
            HAVING COUNT(u.id) >= :quantity
        )
        AND u.id NOT IN (
            SELECT ru.unit_id
            FROM rental r
            INNER JOIN rental_unit ru ON r.id = ru.rental_id
            WHERE r.rental_end_date is null
        )
        AND EXISTS (
            SELECT 1
            FROM unit u2
            WHERE u2.bay_id = u.bay_id
            AND u2.id = u.id + :quantity - 1
        )
        LIMIT ' . $quantity . ';';

        $stmt = $this->getEntityManager()->getConnection()->prepare($query);
        $stmt->bindValue('quantity', $quantity);
        $result = $stmt->executeQuery()->fetchAllAssociative();

        return array_map(fn($unit) => $unit['id'], $result);
    }

    //    /**
    //     * @return Unit[] Returns an array of Unit objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Unit
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
