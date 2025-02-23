<?php

namespace App\Repository;

use App\DTO\TokenCreationDTO;
use App\Entity\Token;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Random\RandomException;

/**
 * @extends ServiceEntityRepository<Token>
 */
class TokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Token::class);
    }

    /**
     * @throws RandomException
     */
    public function generate(TokenCreationDTO $dto): Token
    {
        $token = new Token();

        $token->setUser($dto->user);
        $token->setDescription($dto->description);
        $token->setValue(bin2hex(random_bytes(48)));
        $token->setCreatedAt();

        $this->getEntityManager()->persist($token);
        $this->getEntityManager()->flush();

        return $token;
    }

    //    /**
    //     * @return Token[] Returns an array of Token objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Token
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
