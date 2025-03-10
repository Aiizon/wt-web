<?php

namespace App\DTO;

use App\Entity\Customer;
use App\Entity\Unit;
use App\Repository\UnitRepository;
use Doctrine\DBAL\Exception;

class UnitsStatsDTO
{
    public Customer $customer;
    public int $totalUnits;
    public int $ok;
    public int $ko;
    public int $maintenance;
    public int $on;
    public int $off;

    /**
     * @throws Exception
     */
    public static function create
    (
        Customer $customer,
        UnitRepository $unitRepository
    ): static
    {
        // @todo: génération de facture au début d'une commande
        // @todo: commande avec plusieurs quantités : unités réutilisées (requête renvoie bien des unités libres ?)
        $dto = new self();

        $dto->customer    = $customer;
        $dto->totalUnits  = $unitRepository->countCustomerUnits($customer);

        $dto->ok          = $unitRepository->countCustomerUnitsByStatus($customer, Unit::$OK);
        $dto->ko          = $unitRepository->countCustomerUnitsByStatus($customer, Unit::$KO);
        $dto->maintenance = $unitRepository->countCustomerUnitsByStatus($customer, Unit::$MAINTENANCE);

        $dto->on          = $unitRepository->countCustomerUnitsByStarted($customer, true);
        $dto->off         = $unitRepository->countCustomerUnitsByStarted($customer, false);

        return $dto;
    }
}