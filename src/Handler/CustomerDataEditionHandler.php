<?php

namespace App\Handler;

use App\DTO\RentalDto;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
class CustomerDataEditionHandler
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle(RentalDto $dto): void
    {
        $customer = $dto->customer;

        $customer->setFirstName($dto->firstName);
        $customer->setLastName($dto->lastName);
        $customer->setAddress($dto->address);

        $this->entityManager->flush();
    }
}