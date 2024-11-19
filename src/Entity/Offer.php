<?php

namespace App\Entity;

use App\Repository\OfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OfferRepository::class)]
class Offer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $maxUnits = null;

    #[ORM\Column(length: 255)]
    private ?string $availability = null;

    #[ORM\Column]
    private ?float $monthlyRentPrice = null;

    #[ORM\Column(length: 255)]
    private ?string $bandwidth = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    /**
     * @var Collection<int, Rental>
     */
    #[ORM\OneToMany(targetEntity: Rental::class, mappedBy: 'offer')]
    private Collection $rentals;

    public function __construct()
    {
        $this->rentals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getMaxUnits(): ?int
    {
        return $this->maxUnits;
    }

    public function setMaxUnits(int $maxUnits): static
    {
        $this->maxUnits = $maxUnits;

        return $this;
    }

    public function getAvailability(): ?string
    {
        return $this->availability;
    }

    public function setAvailability(string $availability): static
    {
        $this->availability = $availability;

        return $this;
    }

    public function getMonthlyRentPrice(): ?int
    {
        return $this->monthlyRentPrice;
    }

    public function setMonthlyRentPrice(int $monthlyRentPrice): static
    {
        $this->monthlyRentPrice = $monthlyRentPrice;

        return $this;
    }

    public function getBandwidth(): ?string
    {
        return $this->bandwidth;
    }

    public function setBandwidth(string $bandwidth): static
    {
        $this->bandwidth = $bandwidth;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection<int, Rental>
     */
    public function getRentals(): Collection
    {
        return $this->rentals;
    }

    public function addRental(Rental $rental): static
    {
        if (!$this->rentals->contains($rental)) {
            $this->rentals->add($rental);
            $rental->setOffer($this);
        }

        return $this;
    }

    public function removeRental(Rental $rental): static
    {
        if ($this->rentals->removeElement($rental)) {
            // set the owning side to null (unless already changed)
            if ($rental->getOffer() === $this) {
                $rental->setOffer(null);
            }
        }

        return $this;
    }
}
