<?php

namespace App\Entity;

use App\Repository\UnitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UnitRepository::class)]
class Unit
{
    public static int $OK = 1;
    public static int $KO = 2;
    public static int $MAINTENANCE = 3;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups('unit:status')]
    private ?string $name = null;

    /**
     * @var Collection<int, Rental>
     */
    #[ORM\ManyToMany(targetEntity: Rental::class, mappedBy: 'units')]
    private Collection $rentals;

    #[ORM\ManyToOne]
    private ?UnitUsage $unitUsage = null;

    #[ORM\Column(nullable: false)]
    #[Groups('unit:status')]
    private ?bool $isStarted = false;

    #[ORM\Column(nullable: false)]
    #[Groups('unit:status')]
    private ?int $status = 2;

    #[ORM\ManyToOne(inversedBy: 'units')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('unit:status')]
    private ?Bay $bay = null;

    /**
     * @var Collection<int, Intervention>
     */
    #[ORM\ManyToMany(targetEntity: Intervention::class, inversedBy: 'units')]
    private Collection $interventions;

    public function __construct()
    {
        $this->rentals = new ArrayCollection();
        $this->interventions = new ArrayCollection();
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
            $rental->addUnit($this);
        }

        return $this;
    }

    public function removeRental(Rental $rental): static
    {
        if ($this->rentals->removeElement($rental)) {
            $rental->removeUnit($this);
        }

        return $this;
    }

    public function getUnitUsage(): ?UnitUsage
    {
        return $this->unitUsage;
    }

    public function setUnitUsage(?UnitUsage $unitUsage): static
    {
        $this->unitUsage = $unitUsage;

        return $this;
    }

    public function getIsStarted(): ?bool
    {
        return $this->isStarted;
    }

    public function setIsStarted(?bool $isStarted): void
    {
        $this->isStarted = $isStarted;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): void
    {
        $this->status = $status;
    }
    

    public function getBay(): ?Bay
    {
        return $this->bay;
    }

    public function setBay(?Bay $bay): static
    {
        $this->bay = $bay;

        return $this;
    }

    /**
     * @return Collection<int, Intervention>
     */
    public function getInterventions(): Collection
    {
        return $this->interventions;
    }

    public function addIntervention(Intervention $intervention): static
    {
        if (!$this->interventions->contains($intervention)) {
            $this->interventions->add($intervention);
        }

        return $this;
    }

    public function removeIntervention(Intervention $intervention): static
    {
        $this->interventions->removeElement($intervention);

        return $this;
    }
}
