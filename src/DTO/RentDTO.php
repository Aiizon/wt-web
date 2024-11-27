<?php

namespace App\DTO;

use App\Entity\BillingType;
use App\Entity\Customer;
use App\Entity\Discount;
use App\Entity\Offer;
class RentDTO
{
    public Offer $offer;
    public Customer $customer;
    public BillingType $billingType;
    public ?Discount $discount;
    public int $quantity;
    public bool $doRenew;
    public ?string $firstName;
    public ?string $lastName;
    public ?string $address;

    public static function create
    (
        Offer       $offer,
        Customer    $customer,
        BillingType $billingType,
        ?Discount   $discount,
        int         $quantity,
        bool        $doRenew,
        ?string     $firstName,
        ?string     $lastName,
        ?string     $address
    ): self
    {
        $dto              = new self();

        $dto->offer       = $offer;
        $dto->customer    = $customer;
        $dto->billingType = $billingType;
        $dto->discount    = $discount;
        $dto->quantity    = $quantity;
        $dto->doRenew     = $doRenew;
        $dto->firstName   = $firstName;
        $dto->lastName    = $lastName;
        $dto->address     = $address;

        return $dto;
    }
}