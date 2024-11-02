<?php

namespace App\Entity;

use App\Repository\ConnectionHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConnectionHistoryRepository::class)]
class ConnectionHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'connectionHistories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $login_time = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $logout_time = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $duration = null;


    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }
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

    public function getLoginTime(): ?\DateTimeInterface
    {
        return $this->login_time;
    }

    public function setLoginTime(\DateTimeInterface $login_time): static
    {
        $this->login_time = $login_time;

        return $this;
    }

    public function getLogoutTime(): ?\DateTimeInterface
    {
        return $this->logout_time;
    }

    public function setLogoutTime(?\DateTimeInterface $logout_time): static
    {
        $this->logout_time = $logout_time;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(?string $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getConnectionHistoryJson(): array
    {
        return [
            'id' => $this->getId(),
            'user' => $this->getUser()->getUserJson(),
            'login_time' => $this->getLoginTimeJson(),
            'logout_time' => $this->getLogoutTimeJson(),
            'is_active' => $this->isIsActive(),
            'duration' => $this->getDuration(),
        ];
    }

    private function getLoginTimeJson() : array
    {
        return [
            'date' => $this->getLoginTime()->format('d-m-Y'),
            'time' => $this->getLoginTime()->format('H:i'),
        ];
    }

    public function getLogoutTimeJson() : array
    {
        if($this->getLogoutTime()){
            return [
                'date' => $this->getLogoutTime()->format('d-m-Y'),
                'time' => $this->getLogoutTime()->format('H:i'),
            ];
        }
        return [];
    }


}
