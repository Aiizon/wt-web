<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $number = null;

    #[ORM\Column]
    private ?float $totalRentPrice = null;

    #[ORM\Column(length: 511)]
    private ?string $billingAddress = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Rental $rental = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?DateTimeImmutable $date = null;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $content = null;

    #[ORM\Column]
    private ?bool $needsGeneration = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        if (!$this->number) {
            $this->number = sprintf('INV-%s-%04d', date('Y'), $this->id);
        }
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getTotalRentPrice(): ?float
    {
        return $this->totalRentPrice;
    }

    public function setTotalRentPrice(float $totalRentPrice): static
    {
        $this->totalRentPrice = $totalRentPrice;

        return $this;
    }

    public function getBillingAddress(): ?string
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(string $billingAddress): static
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    public function getRental(): ?Rental
    {
        return $this->rental;
    }

    public function setRental(?Rental $rental): static
    {
        $this->rental = $rental;

        return $this;
    }

    public function getDate(): ?DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content): static
    {
        $this->content = $content;

        return $this;
    }

    public function isNeedsGeneration(): ?bool
    {
        return $this->needsGeneration;
    }

    public function setNeedsGeneration(bool $needsGeneration): static
    {
        $this->needsGeneration = $needsGeneration;

        return $this;
    }
}
