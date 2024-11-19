<?php

namespace App\Entity;

use App\Repository\BillingTypeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BillingTypeRepository::class)]
class BillingType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $months = null;

    #[ORM\Column]
    private ?float $discountOverMonthly = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMonths(): ?int
    {
        return $this->months;
    }

    public function setMonths(int $months): static
    {
        $this->months = $months;

        return $this;
    }

    public function getDiscountOverMonthly(): ?int
    {
        return $this->discountOverMonthly;
    }

    public function setDiscountOverMonthly(int $discountOverMonthly): static
    {
        $this->discountOverMonthly = $discountOverMonthly;

        return $this;
    }
}
