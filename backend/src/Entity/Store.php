<?php

namespace App\Entity;

use App\Repository\StoreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StoreRepository::class)]
class Store
{
    const STATUS_OPEN = 1;
    const STATUS_CLOSED = 2;


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    private ?string $headquarters_address = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $postal_code = null;

    #[ORM\Column(length: 100)]
    private ?string $city = null;

    #[ORM\Column(length: 100)]
    private ?string $country = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $capital = null;


    #[ORM\Column]
    private ?int $status = null;

    #[ORM\ManyToMany(targetEntity: User::class)]
    private Collection $users;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $opening_date = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    private ?string $siren = null;

    #[ORM\OneToMany(mappedBy: 'store', targetEntity: Ticket::class)]
    private Collection $tickets;

    #[ORM\OneToMany(mappedBy: 'store', targetEntity: ActionHistory::class)]
    private Collection $actionHistories;

   

    

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->tickets = new ArrayCollection();
        $this->actionHistories = new ArrayCollection();
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getHeadquartersAddress(): ?string
    {
        return $this->headquarters_address;
    }

    public function setHeadquartersAddress(string $headquarters_address): static
    {
        $this->headquarters_address = $headquarters_address;

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

    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    public function setPostalCode(string $postal_code): static
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getCapital(): ?string
    {
        return $this->capital;
    }

    public function setCapital(string $capital): static
    {
        $this->capital = $capital;

        return $this;
    }






    /**
     * Convert the Store object to an associative array for JSON serialization.
     *
     * @return array
     */
    public function getStoreJson(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'address' => $this->getAddress(),
            'headquarters_address' => $this->getHeadquartersAddress(),
            'email' => $this->getEmail(),
            'postal_code' => $this->getPostalCode(),
            'city' => $this->getCity(),
            'country' => $this->getCountry(),
            'capital' => $this->getCapital(),
            'status' => $this->getStatus(),
            'opening_date' => $this->getFormattedOpeningDate(),
            'phone' => $this->getPhone(),
            'siren' => $this->getSiren(),
        ];
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
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->users->removeElement($user);

        return $this;
    }

    public function getOpeningDate(): ?\DateTimeInterface
    {
        return $this->opening_date;
    }

    public function getFormattedOpeningDate(): ?string
    {
        $openingDate = $this->getOpeningDate();
        return $openingDate?->format('d/m/Y');

    }

    public function setOpeningDate(?\DateTimeInterface $opening_date): static
    {
        $this->opening_date = $opening_date;
        return $this;
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

    public function getSiren(): ?string
    {
        return $this->siren;
    }

    public function setSiren(string $siren): static
    {
        $this->siren = $siren;

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
            $ticket->setStore($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getStore() === $this) {
                $ticket->setStore(null);
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
            $actionHistory->setStore($this);
        }

        return $this;
    }

    public function removeActionHistory(ActionHistory $actionHistory): static
    {
        if ($this->actionHistories->removeElement($actionHistory)) {
            // set the owning side to null (unless already changed)
            if ($actionHistory->getStore() === $this) {
                $actionHistory->setStore(null);
            }
        }

        return $this;
    }

    public function setId(int $int): void
    {
        $this->id = $int;
    }


}
