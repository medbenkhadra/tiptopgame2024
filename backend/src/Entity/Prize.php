<?php

namespace App\Entity;

use App\Repository\PrizeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrizeRepository::class)]
class Prize
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $label = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $type = null;

    #[ORM\Column(length: 150)]
    private ?string $prize_value = null;

    #[ORM\Column]
    private ?float $winning_rate = null;

    #[ORM\OneToMany(mappedBy: 'prize', targetEntity: Ticket::class)]
    private Collection $tickets;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getPrizeValue(): ?string
    {
        return $this->prize_value;
    }

    public function setPrizeValue(string $prize_value): static
    {
        $this->prize_value = $prize_value;

        return $this;
    }

    public function getWinningRate(): ?float
    {
        return $this->winning_rate;
    }

    public function setWinningRate(float $winning_rate): static
    {
        $this->winning_rate = $winning_rate;

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): static
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setPrize($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getPrize() === $this) {
                $ticket->setPrize(null);
            }
        }

        return $this;
    }


    public function getPrizeJson(): array
    {
        return [
            'id' => $this->getId(),
            'label' => $this->getLabel(),
            'name' => $this->getName(),
            'type' => $this->getType(),
            'prize_value' => $this->getPrizeValue(),
            'winning_rate' => $this->getWinningRate(),
        ];
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }
}
