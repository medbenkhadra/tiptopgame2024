<?php

namespace App\Entity;

use App\Repository\EmailingHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailingHistoryRepository::class)]
class EmailingHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;




    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $sent_at = null;

    #[ORM\ManyToOne(inversedBy: 'emailingHistories')]
    #[ORM\JoinColumn(unique: false, nullable: false)]
    private ?EmailService $service = null;

    #[ORM\ManyToOne(inversedBy: 'emailingHistories')]
    #[ORM\JoinColumn(unique: false, nullable: true)]
    private ?User $receiver = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getService(): EmailService
    {
        return $this->service;
    }

    public function setService(EmailService $service): static
    {
        $this->service = $service;

        return $this;
    }



    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sent_at;
    }

    public function setSentAt(\DateTimeInterface $sent_at): static
    {
        $this->sent_at = $sent_at;

        return $this;
    }

    public function getEmailingHistoryJson(): array
    {
        return [
            'id' => $this->getId(),
            'service' => $this->getService()->getEmailServiceJson(),
            'receiver' => $this->getReceiver()->getUserJson(),
            'sent_at' => $this->getSentAtJson(),
        ];
    }

    private function getSentAtJson() : array
    {
        return [
            'date' => $this->getSentAt()->format('d-m-Y'),
            'time' => $this->getSentAt()->format('H:i'),
        ];
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): static
    {
        $this->receiver = $receiver;

        return $this;
    }




}
