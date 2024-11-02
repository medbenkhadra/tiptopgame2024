<?php

namespace App\Entity;

use App\Repository\ActionHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActionHistoryRepository::class)]
class ActionHistory
{

    const STORES_MANAGEMENT = "Gestion des magasins";
    const USERS_MANAGEMENT = "Gestion des utilisateurs";

    const ACCOUNTS_MANAGEMENT = "Gestion des comptes";





    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $action_type = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $details = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\ManyToOne(inversedBy: 'actionHistories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_done_action = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user_action_related_to = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'actionHistories')]
    private ?Store $store = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActionType(): ?string
    {
        return $this->action_type;
    }

    public function setActionType(string $action_type): static
    {
        $this->action_type = $action_type;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(string $details): static
    {
        $this->details = $details;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUserDoneAction(): ?User
    {
        return $this->user_done_action;
    }

    public function setUserDoneAction(?User $user_done_action): static
    {
        $this->user_done_action = $user_done_action;

        return $this;
    }

    public function getUserActionRelatedTo(): ?User
    {
        return $this->user_action_related_to;
    }

    public function setUserActionRelatedTo(?User $user_action_related_to): static
    {
        $this->user_action_related_to = $user_action_related_to;

        return $this;
    }


    public function getCreatedAtJson(): array
    {
        return [
            'date' => $this->getCreatedAt()->format('d-m-Y'),
            'time' => $this->getCreatedAt()->format('H:i'),
        ];
    }

    public function getActionHistoryJson(): array
    {
        return [
            'id' => $this->getId(),
            'action_type' => $this->getActionType(),
            'details' => $this->getDetails(),
            'created_at' => $this->getCreatedAtJson(),
            'user_done_action' => $this->getUserDoneAction()?->getUserJson(),
            'user_action_related_to' => $this->getUserActionRelatedTo()?->getUserJson(),
        ];
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
}
