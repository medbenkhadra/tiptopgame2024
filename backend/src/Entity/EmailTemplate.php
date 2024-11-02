<?php

namespace App\Entity;

use App\Repository\EmailTemplateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailTemplateRepository::class)]
class EmailTemplate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255 , nullable: true)]
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $type = null;


    #[ORM\Column(nullable: true)]
    private ?bool $required = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'emailTemplates')]
    #[ORM\JoinColumn(nullable: false)]
    private ?EmailService $service = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getRequired(): ?bool
    {
        return $this->required;
    }

    public function setRequired(?bool $required): static
    {
        $this->required = $required;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getService(): ?EmailService
    {
        return $this->service;
    }

    public function setService(?EmailService $service): static
    {
        $this->service = $service;

        return $this;
    }

    //getServiceJson

    public function getServiceJson(): ?array
    {
        return [
            'id' => $this->service->getId(),
            'name' => $this->service->getName(),
            'description' => $this->service->getDescription(),
            'label' => $this->service->getLabel(),
            'templates' => $this->service->getTemplatesJson(),
            'variables' => $this->service->getVariablesJson(),

        ];
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function setId(int $int)
    {
        $this->id = $int;
    }
}
