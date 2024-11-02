<?php

namespace App\Entity;

use App\Repository\TicketHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketHistoryRepository::class)]
class TicketHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ticketHistories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'ticketHistories')]
    private ?User $employee = null;

    #[ORM\ManyToOne(inversedBy: 'ticketHistories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ticket $ticket = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updated_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getEmployee(): ?User
    {
        return $this->employee;
    }

    public function setEmployee(?User $employee): static
    {
        $this->employee = $employee;

        return $this;
    }

    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    public function setTicket(?Ticket $ticket): static
    {
        $this->ticket = $ticket;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getUpdatedAtJson(): array
    {
        return [
            'date' => $this->getUpdatedAt()?->format('d/m/Y'),
            'time' => $this->getUpdatedAt()?->format('H:i'),
        ];
    }

    public function getTicketHistoryJson(): array
    {
        return [
            'id' => $this->getId(),
            'user' => $this->getUser()?->getUserJson(),
            'status' => $this->getStatus(),
            'employee' => $this->getEmployee()?->getUserJson(),
            'ticket' => $this->getTicket()?->getTicketJson(),
            'updated_at' => $this->getUpdatedAtJson()
        ];
    }

    public function setId(int $int)
    {
        $this->id = $int;
    }


}
