<?php

namespace App\Handler;

use App\DTO\RentalDto;
use App\Entity\Rental;
use App\Repository\UnitRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
class RentalCreationHandler
{
    private EntityManagerInterface $entityManager;
    private UnitRepository         $unitRepository;

    public function __construct
    (
        EntityManagerInterface $entityManager,
        UnitRepository         $unitRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->unitRepository = $unitRepository;
    }

    public function handle(RentalDto $rentalDto)
    {
        for ($i = 0; $i < $rentalDto->quantity; $i++) {
            $rental = new Rental();
            $rental->setMonthlyRentPrice($rentalDto->offer->getMonthlyRentPrice());
            $rental->setDoRenew($rentalDto->doRenew);
            $rental->setFirstRentalDate(new DateTimeImmutable());
            $rental->setBillingType($rentalDto->billingType);
            $rental->setOffer($rentalDto->offer);
            $rental->setCustomer($rentalDto->customer);
            $rental->setDiscount($rentalDto->discount);

            $availableUnits = $this->unitRepository->getAvailableUnitsForRental($rentalDto->quantity);
            for ($j = 0; $j < $rentalDto->offer->getMaxUnits(); $j++) {
                $rental->addUnit($this->unitRepository->findOneBy(['id' => $availableUnits[$j]]));
                unset($availableUnits[$j]);
            }

            $this->entityManager->persist($rental);
        }
        $this->entityManager->flush();
    }
}