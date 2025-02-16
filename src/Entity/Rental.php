<?php

namespace App\Entity;

use App\Repository\RentalRepository;
use DateInterval;
use DateMalformedIntervalStringException;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RentalRepository::class)]
class Rental
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $monthlyRentPrice = null;

    private ?int $totalRentPrice = null;

    #[ORM\Column]
    private ?bool $doRenew = null;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private ?DateTimeImmutable $firstRentalDate = null;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $rentalEndDate = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?BillingType $billingType = null;

    #[ORM\ManyToOne(inversedBy: 'rentals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Offer $offer = null;

    #[ORM\ManyToOne(inversedBy: 'rentals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customer $customer = null;

    #[ORM\ManyToOne]
    private ?Discount $discount = null;

    /**
     * @var Collection<int, Unit>
     */
    #[ORM\ManyToMany(targetEntity: Unit::class, inversedBy: 'rentals')]
    private Collection $units;

    /**
     * @var Collection<int, Invoice>
     */
    #[ORM\OneToMany(targetEntity: Invoice::class, mappedBy: 'rental')]
    private Collection $invoices;

    private ?DateTimeImmutable $nextDueDate = null;

    /**
     * @throws DateMalformedIntervalStringException
     */
    public function __construct()
    {
        $this->units    = new ArrayCollection();
        $this->invoices = new ArrayCollection();
        $this->setRentPrice();
        $this->setNextDueDate();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMonthlyRentPrice(): ?float
    {
        return $this->monthlyRentPrice;
    }

    public function setMonthlyRentPrice(float $monthlyRentPrice): static
    {
        $this->monthlyRentPrice = $monthlyRentPrice;

        return $this;
    }

    public function setRentPrice(): static
    {
        if ($this->getBillingType() === null) {
            return $this;
        }

        $totalRentPrice = ($this->getMonthlyRentPrice() * $this->getBillingType()->getMonths() ?? 1)
            * (1 - $this->getBillingType()->getDiscountOverMonthly());

        if ($this->getDiscount() !== null) {
            $this->getDiscount()->isPercentage() ?
                $totalRentPrice *= (1 - $this->getDiscount()->getAmount()) :
                $totalRentPrice -= $this->getDiscount()->getAmount();
        }

        $this->totalRentPrice = $totalRentPrice;

        return $this;
    }

    public function getTotalRentPrice(): ?int
    {
        if ($this->totalRentPrice === null) {
            $this->setRentPrice();
        }

        return $this->totalRentPrice;
    }

    public function isDoRenew(): ?bool
    {
        return $this->doRenew;
    }

    public function setDoRenew(bool $doRenew): static
    {
        $this->doRenew = $doRenew;

        return $this;
    }

    public function getFirstRentalDate(): ?DateTimeImmutable
    {
        return $this->firstRentalDate;
    }

    public function setFirstRentalDate(DateTimeImmutable $firstRentalDate): static
    {
        $this->firstRentalDate = $firstRentalDate;

        return $this;
    }

    public function getRentalEndDate(): ?DateTimeImmutable
    {
        return $this->rentalEndDate;
    }

    public function setRentalEndDate(?DateTimeImmutable $rentalEndDate): static
    {
        $this->rentalEndDate = $rentalEndDate;

        return $this;
    }

    public function getBillingType(): ?BillingType
    {
        return $this->billingType;
    }

    public function setBillingType(?BillingType $billingType): static
    {
        $this->billingType = $billingType;

        return $this;
    }

    public function getOffer(): ?Offer
    {
        return $this->offer;
    }

    public function setOffer(?Offer $offer): static
    {
        $this->offer = $offer;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getDiscount(): ?Discount
    {
        return $this->discount;
    }

    public function setDiscount(?Discount $discount): static
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * @return Collection<int, Unit>
     */
    public function getUnits(): Collection
    {
        return $this->units;
    }

    public function addUnit(Unit $unit): static
    {
        if (!$this->units->contains($unit)) {
            $this->units->add($unit);
        }

        return $this;
    }

    public function removeUnit(Unit $unit): static
    {
        $this->units->removeElement($unit);

        return $this;
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): static
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices->add($invoice);
            $invoice->setRental($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): static
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getRental() === $this) {
                $invoice->setRental(null);
            }
        }

        return $this;
    }

    /**
     * @throws DateMalformedIntervalStringException
     */
    public function setNextDueDate(): void
    {
        if ($this->firstRentalDate === null) {
            return;
        }

        $result = DateTime::createFromImmutable($this->firstRentalDate);
        $today = new DateTime();

        while ($result <= $today) {
            date_add($result, new DateInterval('P' . $this->billingType->getMonths() . 'M'));
        }

        $result->setTime(0, 0, 0);
        $this->nextDueDate = DateTimeImmutable::createFromMutable($result);
    }

    /**
     * @throws DateMalformedIntervalStringException
     */
    public function getNextDueDate(): DateTimeImmutable
    {
        if ($this->nextDueDate === null) {
            $this->setNextDueDate();
        }

        return $this->nextDueDate;
    }
}
