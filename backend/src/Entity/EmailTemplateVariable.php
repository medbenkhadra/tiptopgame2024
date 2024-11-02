<?php

namespace App\Entity;

use App\Repository\EmailTemplateVariableRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailTemplateVariableRepository::class)]
class EmailTemplateVariable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: EmailService::class, inversedBy: 'emailTemplateVariables')]
    private Collection $services;

    #[ORM\Column(length: 255)]
    private ?string $label = null;


    public function __construct()
    {
        $this->services = new ArrayCollection();
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




    public function getServiceAsJson(): array
    {
        $services = [];
        foreach ($this->services as $service) {
            $services[] = $service->getEmailServiceJson();
        }
        return $services;
    }


    /**
     * @return Collection<int, EmailService>
     */
    public function getService(): Collection
    {
        return $this->services;
    }



    /**
     * @return Collection<int, EmailService>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(EmailService $service): static
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
        }

        return $this;
    }

    public function addServices(array $services): static
    {
        foreach ($services as $service) {
            if (!$this->services->contains($service)) {
                $this->addService($service);
            }
        }

        return $this;
    }

    public function getServicesJson(): array
    {
        $services = [];
        foreach ($this->services as $service) {
            $services[] =
                [
                    'id' => $service->getId(),
                    'name' => $service->getName(),
                    'label' => $service->getLabel(),
                ];
        }
        return $services;
    }

    public function removeServices(array $services): static
    {
        foreach ($services as $service) {
            if ($this->services->contains($service)) {
                $this->removeService($service);
            }
        }

        return $this;
    }

    public function removeService(EmailService $service): static
    {
        $this->services->removeElement($service);

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

    public function setId(int $int)
    {
        $this->id = $int;
    }


}
