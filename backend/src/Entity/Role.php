<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role
{


    const PREFIX = 'ROLE';
    const DELIMITER = '_';
    const ROLE_ADMIN = self::PREFIX . self::DELIMITER . 'ADMIN';
    const ROLE_CLIENT = self::PREFIX . self::DELIMITER . 'CLIENT';
    const ROLE_STOREMANAGER = self::PREFIX . self::DELIMITER . 'STOREMANAGER';
    const ROLE_EMPLOYEE = self::PREFIX . self::DELIMITER . 'EMPLOYEE';
    const ROLE_ANONYMOUS = self::PREFIX . self::DELIMITER . 'ANONYMOUS';
    const ROLE_BAILIFF = self::PREFIX . self::DELIMITER . 'BAILIFF';
   

    /**
     * Adds role prefix to role name
     * @param string roleName
     *
     * @return string
     */
    public static function formatToRole(string $roleName): string
    {
        return self::PREFIX . self::DELIMITER . mb_strtoupper($roleName);
    }


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\OneToMany(mappedBy: 'role', targetEntity: User::class)]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
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

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

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
            $user->setRole($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getRole() === $this) {
                $user->setRole(null);
            }
        }

        return $this;
    }
}
