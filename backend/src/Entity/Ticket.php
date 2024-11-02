<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
class Ticket
{
    const STATUS_GENERATED = 1;
    const STATUS_PRINTED = 2;
    const STATUS_PENDING_VERIFICATION = 3;
    const STATUS_WINNER = 4;
    const STATUS_EXPIRED = 5;
    const STATUS_CANCELLED = 6;


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $ticket_code = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $win_date = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'tickets', cascade: ['persist'])]
    private ?Prize $prize = null;

    #[ORM\Column]
    private ?int $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $ticket_printed_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $ticket_generated_at = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    private ?Store $store = null;

    #[ORM\ManyToOne(inversedBy: 'ticketsEmployee')]
    private ?User $employee = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'ticket', targetEntity: TicketHistory::class)]
    private Collection $ticketHistories;



    public function __construct()
    {
        $this->ticketHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTicketCode(): ?string
    {
        return $this->ticket_code;
    }

    public function setTicketCode(string $ticket_code): static
    {
        $this->ticket_code = $ticket_code;

        return $this;
    }

    public function getWinDate(): ?\DateTimeInterface
    {
        return $this->win_date;
    }

    //getWinDateJson
    public function getWinDateJson(): ?array
    {
        $winDate = $this->getWinDate();
        return [
            "date" =>  $winDate?->format('d/m/Y'),
            "time" =>  $winDate?->format('H:i'),
        ];
    }

    public function setWinDate(?\DateTimeInterface $win_date): static
    {
        $this->win_date = $win_date;

        return $this;
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

    public function getPrize(): ?Prize
    {
        return $this->prize;
    }

    public function setPrize(?Prize $prize): static
    {
        $this->prize = $prize;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getTicketPrintedAt(): ?\DateTimeInterface
    {
        return $this->ticket_printed_at;
    }

    //getTicketPrintedAtJson
    public function getTicketPrintedAtJson(): ?array
    {
        $ticketPrintedAt = $this->getTicketPrintedAt();
        return [
          "date" =>  $ticketPrintedAt?->format('d/m/Y'),
            "time" =>  $ticketPrintedAt?->format('H:i'),
        ];
    }

    public function setTicketPrintedAt(?\DateTimeInterface $ticket_printed_at): static
    {
        $this->ticket_printed_at = $ticket_printed_at;

        return $this;
    }

    public function getTicketGeneratedAt(): ?\DateTimeInterface
    {
        return $this->ticket_generated_at;
    }

    public function getTicketGeneratedAtJson(): ?array
    {
        $ticketGeneratedAt = $this->getTicketGeneratedAt();
        return [
            "date" =>  $ticketGeneratedAt?->format('d/m/Y'),
            "time" =>  $ticketGeneratedAt?->format('H:i')
        ];
    }


    public function setTicketGeneratedAt(\DateTimeInterface $ticket_generated_at): static
    {
        $this->ticket_generated_at = $ticket_generated_at;

        return $this;
    }

    public function getTicketJson(): array
    {
        $ticket = [
            'id' => $this->getId(),
            'ticket_code' => $this->getTicketCode(),
            'win_date' => $this->getWinDateJson(),
            'user' => $this->getUser()?->getUserJson(),
            'prize' => $this->getPrize()->getPrizeJson(),
            'status' => $this->getStatus(),
            'ticket_printed_at' => $this->getTicketPrintedAtJson(),
            'ticket_generated_at' => $this->getTicketGeneratedAtJson(),
            'employee' => $this->getEmployee()?->getUserJson(),
            'store' => $this->getStore()?->getStoreJson(),
            'updated_at' => $this->getUpdatedAtJson()
        ];
        return $ticket;
    }

    public function getStore(): ?Store
    {
        return $this->store;
    }

    public function setStore(?Store $store): static
    {
        $this->store = $store;

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getUpdatedAtJson(): array
    {
        $updatedAt = $this->getUpdatedAt();
        return [
            "date" =>  $updatedAt?->format('d/m/Y'),
            "time" =>  $updatedAt?->format('H:i')
        ];
    }

    /**
     * @return Collection<int, TicketHistory>
     */
    public function getTicketHistories(): Collection
    {
        return $this->ticketHistories;
    }

    public function addTicketHistory(TicketHistory $ticketHistory): static
    {
        if (!$this->ticketHistories->contains($ticketHistory)) {
            $this->ticketHistories->add($ticketHistory);
            $ticketHistory->setTicket($this);
        }

        return $this;
    }

    public function removeTicketHistory(TicketHistory $ticketHistory): static
    {
        if ($this->ticketHistories->removeElement($ticketHistory)) {
            // set the owning side to null (unless already changed)
            if ($ticketHistory->getTicket() === $this) {
                $ticketHistory->setTicket(null);
            }
        }

        return $this;
    }



}
