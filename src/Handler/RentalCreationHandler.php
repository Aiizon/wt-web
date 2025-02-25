<?php

namespace App\Handler;

use App\DTO\RentalDto;
use App\Entity\Rental;
use App\Repository\DiscountRepository;
use App\Repository\UnitRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class RentalCreationHandler
{
    private EntityManagerInterface $entityManager;
    private UnitRepository         $unitRepository;
    private DiscountRepository     $discountRepository;

    public function __construct
    (
        EntityManagerInterface $entityManager,
        UnitRepository         $unitRepository,
        DiscountRepository     $discountRepository
    )
    {
        $this->entityManager      = $entityManager;
        $this->unitRepository     = $unitRepository;
        $this->discountRepository = $discountRepository;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws Exception
     */
    public function handle(RentalDto $rentalDto): void
    {
        for ($i = 0; $i < $rentalDto->quantity; $i++) {
            $rental = new Rental();
            $rental->setMonthlyRentPrice($rentalDto->offer->getMonthlyRentPrice());
            $rental->setDoRenew($rentalDto->doRenew);
            $rental->setFirstRentalDate(new DateTimeImmutable());
            $rental->setBillingType($rentalDto->billingType);
            $rental->setOffer($rentalDto->offer);
            $rental->setCustomer($rentalDto->customer);
            $rental->setDiscount($this->discountRepository->findOneBy(['code' => $rentalDto->discount, 'isActive' => true]) ?? null);

            $availableUnitCount = $this->unitRepository->getAvailableUnitCount();

            if ($availableUnitCount < $rentalDto->quantity) {
                throw new Exception('Not enough units available');
            }

            $availableUnits = $this->unitRepository->getAvailableUnitsForRental($rentalDto->offer->getMaxUnits());

            for ($j = 0; $j < $rentalDto->offer->getMaxUnits(); $j++) {
                $rental->addUnit($this->unitRepository->findOneBy(['id' => $availableUnits[$j]]));
                unset($availableUnits[$j]);
            }

            $this->entityManager->persist($rental);
        }
        $this->entityManager->flush();
    }
}