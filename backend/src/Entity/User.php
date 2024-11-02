<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Validator\Constraints\UniqueEmail;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    const STATUS_OPEN = 1;
    const STATUS_CLOSED = 2;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $gender = null;

    #[ORM\Column(length: 255, unique: false)]
    #[UniqueEmail]
    private ?string $email = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_of_birth = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Role $role = null;


    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Ticket::class)]
    private Collection $tickets;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $api_token = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $api_token_created_at = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column]
    private ?int $status = null;

    #[ORM\ManyToMany(targetEntity: Store::class)]
    private Collection $stores;

    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: Ticket::class)]
    private Collection $ticketsEmployee;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: TicketHistory::class)]
    private Collection $ticketHistories;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?UserPersonalInfo $userPersonalInfo = null;

    #[ORM\Column]
    private ?bool $is_active = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $activited_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $token = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $token_expired_at = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Avatar $avatar = null;

    #[ORM\ManyToMany(targetEntity: Badge::class, inversedBy: 'users')]
    private Collection $badges;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: LoyaltyPoints::class , cascade: ['persist', 'remove'])]
    private Collection $loyaltyPoints;

    #[ORM\OneToMany(mappedBy: 'user_done_action', targetEntity: ActionHistory::class, orphanRemoval: true)]
    private Collection $actionHistories;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ConnectionHistory::class)]
    private Collection $connectionHistories;

    #[ORM\OneToMany(mappedBy: 'receiver', targetEntity: EmailingHistory::class)]
    private Collection $emailingHistories;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?SocialMediaAccount $socialMediaAccount = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?ClientFinalDraw $clientFinalDraw = null;


    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->stores = new ArrayCollection();
        $this->ticketsEmployee = new ArrayCollection();
        $this->ticketHistories = new ArrayCollection();
        $this->badges = new ArrayCollection();
        $this->loyaltyPoints = new ArrayCollection();
        $this->actionHistories = new ArrayCollection();
        $this->connectionHistories = new ArrayCollection();
        $this->emailingHistories = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->date_of_birth;
    }

    public function setDateOfBirth(\DateTimeInterface $date_of_birth): static
    {
        $this->date_of_birth = $date_of_birth;

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): static
    {
        $this->role = $role;

        return $this;
    }


    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

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
            $ticket->setUser($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getUser() === $this) {
                $ticket->setUser(null);
            }
        }

        return $this;
    }

    public function getApiToken(): ?string
    {
        return $this->api_token;
    }

    public function setApiToken(?string $api_token): static
    {
        $this->api_token = $api_token;

        return $this;
    }

    public function getApiTokenCreatedAt(): ?\DateTimeInterface
    {
        return $this->api_token_created_at;
    }

    public function setApiTokenCreatedAt(?\DateTimeInterface $api_token_created_at): static
    {
        $this->api_token_created_at = $api_token_created_at;

        return $this;
    }


    public function getSalt()
    {
        // Since we are using bcrypt, there is no need for a separate salt.
        // However, the method is required by the UserInterface, so just return null.
        return null;
    }

    public function getUsername()
    {
        // Return the unique identifier for the user (e.g., email).
        return $this->email;
    }

    public function eraseCredentials() : void
    {
        // If you have any sensitive data in your entity that should be removed after authentication,
        // you can handle it here. For most cases, you can leave it empty.
    }

    // Implement the new getUserIdentifier() method
    public function getUserIdentifier(): string
    {
        return $this->email; // Return the unique user identifier, typically the email.
    }


    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = [];
        if (isset($this->role)) {
            $roles[] = $this->role->getName();
        }
        return $roles;
    }

    /**
     * Get the user stores formatted as JSON objects.
     *
     * @return array
     * @SerializedName("userStores")
     */
    public function getUserStoresJson(): array
    {
        $stores = [];
        foreach ($this->stores as $store) {
            $storeValue = $store->getStoreJson();
            $storeAux = [
                $storeValue,
            ];
            $stores[] = $storeAux;
        }
        return $stores;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

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

    /**
     * @return Collection<int, Store>
     */
    public function getStores(): Collection
    {
        return $this->stores;
    }

    public function addStore(Store $store): static
    {
        if (!$this->stores->contains($store)) {
            $this->stores->add($store);
        }

        return $this;
    }

    public function removeStore(Store $store): static
    {
        $this->stores->removeElement($store);

        return $this;
    }


    /**
     * Convert the Store object to an associative array for JSON serialization.
     *
     * @return array
     */
    public function getUserJson(): array
    {
        return [
            'id' => $this->getId(),
            'lastname' => $this->getLastname(),
            'firstname' => $this->getFirstname(),
            'phone' => $this->getPhone(),
            'email' => $this->getEmail(),
            'status' => $this->getStatus(),
            'role' => $this->getRole()->getName(),
            'stores' => $this->getUserStoresJson(),
            'dateOfBirth' => $this->getDateOfBirth()->format('d/m/Y'),
            'age' => $this->getAge(),
            'gender' => $this->getGender(),
            'store'=> count($this->getStores())>0 ? $this->getStores()[0]->getStoreJson() : null,
            'address' => $this->getUserPersonalInfo()?->getAddress(),
            'postalCode' => $this->getUserPersonalInfo()?->getPostalCode(),
            'city' => $this->getUserPersonalInfo()?->getCity(),
            'country' => $this->getUserPersonalInfo()?->getCountry(),
            'is_activated' => $this->isIsActive(),
            'created_at' => [
                'date' => $this->getCreatedAt()->format('d/m/Y'),
                'time' => $this->getCreatedAt()->format('H:i'),
            ],
            'activated_at' => [
                'date' => $this->getActivitedAt()?->format('d/m/Y'),
                'time' => $this->getActivitedAt()?->format('H:i'),
            ],

            'updated_at' => [
                'date' => $this->getUpdatedAt()?->format('d/m/Y'),
                'time' => $this->getUpdatedAt()?->format('H:i'),
            ],
            'avatar_image' => $this->avatar?->getAvatarJson(),
            'avatar' => $this->avatar?->getAvatarUrl(),


        ];
    }

    public function getUserPersonalInfoJson(): array
    {
        if ($this->getUserPersonalInfo() === null) {
            return [];
        }
        return [
            'id' => $this->getId(),
            'lastname' => $this->getLastname(),
            'firstname' => $this->getFirstname(),
            'phone' => $this->getPhone(),
            'email' => $this->getEmail(),
            'status' => $this->getStatus(),
            'role' => $this->getRole()->getName(),
            'stores' => $this->getUserStoresJson(),
            'dateOfBirth' => $this->getDateOfBirth()->format('d/m/Y'),
            'age' => $this->getAge(),
            'gender' => $this->getGender(),
            'userPersonalInfo' => $this->getUserPersonalInfo()->getUserPersonalInfoJson(),
            'is_activated' => $this->isIsActive(),
            'created_at' => [
                'date' => $this->getCreatedAt()->format('d/m/Y'),
                'time' => $this->getCreatedAt()->format('H:i'),
            ],
            'activated_at' => [
                'date' => $this->getActivitedAt()?->format('d/m/Y'),
                'time' => $this->getActivitedAt()?->format('H:i'),
            ],
            'avatar_image' => $this->avatar?->getAvatarJson(),
            'avatar' => $this->avatar?->getAvatarUrl(),
        ];
    }

    public function getAge(): int
    {
        $now = new \DateTime();
        $interval = $this->getDateOfBirth()->diff($now);
        return $interval->y;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTicketsEmployee(): Collection
    {
        return $this->ticketsEmployee;
    }

    public function addTicketsEmployee(Ticket $ticketsEmployee): static
    {
        if (!$this->ticketsEmployee->contains($ticketsEmployee)) {
            $this->ticketsEmployee->add($ticketsEmployee);
            $ticketsEmployee->setEmployee($this);
        }

        return $this;
    }

    public function removeTicketsEmployee(Ticket $ticketsEmployee): static
    {
        if ($this->ticketsEmployee->removeElement($ticketsEmployee)) {
            // set the owning side to null (unless already changed)
            if ($ticketsEmployee->getEmployee() === $this) {
                $ticketsEmployee->setEmployee(null);
            }
        }

        return $this;
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
            $ticketHistory->setUser($this);
        }

        return $this;
    }

    public function removeTicketHistory(TicketHistory $ticketHistory): static
    {
        if ($this->ticketHistories->removeElement($ticketHistory)) {
            // set the owning side to null (unless already changed)
            if ($ticketHistory->getUser() === $this) {
                $ticketHistory->setUser(null);
            }
        }

        return $this;
    }

    public function getUserPersonalInfo(): ?UserPersonalInfo
    {
        return $this->userPersonalInfo;
    }

    public function setUserPersonalInfo(?UserPersonalInfo $userPersonalInfo): static
    {
        // unset the owning side of the relation if necessary
        if ($userPersonalInfo === null && $this->userPersonalInfo !== null) {
            $this->userPersonalInfo->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($userPersonalInfo !== null && $userPersonalInfo->getUser() !== $this) {
            $userPersonalInfo->setUser($this);
        }

        $this->userPersonalInfo = $userPersonalInfo;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): static
    {
        $this->is_active = $is_active;

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

    public function getActivitedAt(): ?\DateTimeInterface
    {
        return $this->activited_at;
    }

    public function setActivitedAt(?\DateTimeInterface $activited_at): static
    {
        $this->activited_at = $activited_at;

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

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getTokenExpiredAt(): ?\DateTimeInterface
    {
        return $this->token_expired_at;
    }

    public function setTokenExpiredAt(?\DateTimeInterface $token_expired_at): static
    {
        $this->token_expired_at = $token_expired_at;

        return $this;
    }

    public function getAvatar(): ?Avatar
    {
        return $this->avatar;
    }

    public function setAvatar(?Avatar $avatar): static
    {
        if ($avatar->getUser() !== $this) {
            $avatar->setUser($this);
        }

        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection<int, Badge>
     */
    public function getBadges(): Collection
    {
        return $this->badges;
    }

    public function addBadge(Badge $badge): static
    {
        if (!$this->badges->contains($badge)) {
            $this->badges->add($badge);
        }

        return $this;
    }

    public function removeBadge(Badge $badge): static
    {
        $this->badges->removeElement($badge);

        return $this;
    }

    /**
     * @return Collection<int, LoyaltyPoints>
     */
    public function getLoyaltyPoints(): Collection
    {
        return $this->loyaltyPoints;
    }

    public function addLoyaltyPoint(LoyaltyPoints $loyaltyPoint): static
    {
        if (!$this->loyaltyPoints->contains($loyaltyPoint)) {
            $this->loyaltyPoints->add($loyaltyPoint);
            $loyaltyPoint->setUser($this);
        }

        return $this;
    }

    public function removeLoyaltyPoint(LoyaltyPoints $loyaltyPoint): static
    {
        if ($this->loyaltyPoints->removeElement($loyaltyPoint)) {
            // set the owning side to null (unless already changed)
            if ($loyaltyPoint->getUser() === $this) {
                $loyaltyPoint->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ActionHistory>
     */
    public function getActionHistories(): Collection
    {
        return $this->actionHistories;
    }

    public function addActionHistory(ActionHistory $actionHistory): static
    {
        if (!$this->actionHistories->contains($actionHistory)) {
            $this->actionHistories->add($actionHistory);
            $actionHistory->setUserDoneAction($this);
        }

        return $this;
    }

    public function removeActionHistory(ActionHistory $actionHistory): static
    {
        if ($this->actionHistories->removeElement($actionHistory)) {
            // set the owning side to null (unless already changed)
            if ($actionHistory->getUserDoneAction() === $this) {
                $actionHistory->setUserDoneAction(null);
            }
        }

        return $this;
    }


    public function getFullName(): string
    {
        return $this->getFirstname() . ' ' . $this->getLastname();
    }

    /**
     * @return Collection<int, ConnectionHistory>
     */
    public function getConnectionHistories(): Collection
    {
        return $this->connectionHistories;
    }

    public function addConnectionHistory(ConnectionHistory $connectionHistory): static
    {
        if (!$this->connectionHistories->contains($connectionHistory)) {
            $this->connectionHistories->add($connectionHistory);
            $connectionHistory->setUser($this);
        }

        return $this;
    }

    public function removeConnectionHistory(ConnectionHistory $connectionHistory): static
    {
        if ($this->connectionHistories->removeElement($connectionHistory)) {
            // set the owning side to null (unless already changed)
            if ($connectionHistory->getUser() === $this) {
                $connectionHistory->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EmailingHistory>
     */
    public function getEmailingHistories(): Collection
    {
        return $this->emailingHistories;
    }

    public function addEmailingHistory(EmailingHistory $emailingHistory): static
    {
        if (!$this->emailingHistories->contains($emailingHistory)) {
            $this->emailingHistories->add($emailingHistory);
            $emailingHistory->setReceiver($this);
        }

        return $this;
    }

    public function removeEmailingHistory(EmailingHistory $emailingHistory): static
    {
        if ($this->emailingHistories->removeElement($emailingHistory)) {
            // set the owning side to null (unless already changed)
            if ($emailingHistory->getReceiver() === $this) {
                $emailingHistory->setReceiver(null);
            }
        }

        return $this;
    }

    public function getSocialMediaAccount(): ?SocialMediaAccount
    {
        return $this->socialMediaAccount;
    }

    public function setSocialMediaAccount(SocialMediaAccount $socialMediaAccount): static
    {
        // set the owning side of the relation if necessary
        if ($socialMediaAccount->getUser() !== $this) {
            $socialMediaAccount->setUser($this);
        }

        $this->socialMediaAccount = $socialMediaAccount;

        return $this;
    }

    public function setId(int $int): void
    {
        $this->id = $int;
    }


    public function __toString(): string
    {
        return $this->getFullName();
    }

    public function getClientFinalDraw(): ?ClientFinalDraw
    {
        return $this->clientFinalDraw;
    }

    public function setClientFinalDraw(ClientFinalDraw $clientFinalDraw): static
    {
        // set the owning side of the relation if necessary
        if ($clientFinalDraw->getUser() !== $this) {
            $clientFinalDraw->setUser($this);
        }

        $this->clientFinalDraw = $clientFinalDraw;

        return $this;
    }

}
