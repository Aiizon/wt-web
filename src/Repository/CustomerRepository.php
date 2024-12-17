<?php

namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Customer>
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    /**
     * @throws Exception
     */
    public function anonymise(Customer $customer): void
    {
        $uniqid = uniqid();

        $customer->setEmail('anonyme' . $uniqid . '@exemple.com');
        $customer->setFirstName('Anonyme');
        $customer->setLastName('Anonyme');
        $customer->setAddress('Anonyme');
        $customer->setVerified(false);
        $customer->setRoles([]);

        $this->getEntityManager()->flush();

        $query = 'update user set password = \'\' where email = :email';
        $stmt = $this->getEntityManager()->getConnection()->prepare($query);
        $stmt->bindValue('email', 'anonyme' . $uniqid . '@exemple.com');
        $stmt->executeStatement();
    }
}
