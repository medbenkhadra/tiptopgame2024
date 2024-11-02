<?php

namespace App\Entity;

use App\Repository\EmailServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailServiceRepository::class)]
class EmailService
{

    const PREFIX = 'EMAILSERVICE';
    const DELIMITER = '_';



    const   EMAILSERVICE_CLIENT_CREATE_ACCOUNT = self::PREFIX . self::DELIMITER . 'CLIENT_CREATE_ACCOUNT';

    const EMAILSERVICE_EMPLOYEE_CREATE_ACCOUNT = self::PREFIX . self::DELIMITER . 'EMPLOYEE_CREATE_ACCOUNT';


    const EMAILSERVICE_ACCOUNT_ACTIVATION_CLIENT = self::PREFIX . self::DELIMITER . 'ACCOUNT_ACTIVATION_CLIENT';

    const EMAILSERVICE_ACCOUNT_ACTIVATION_EMPLOYEE = self::PREFIX . self::DELIMITER . 'ACCOUNT_ACTIVATION_EMPLOYEE';


    const EMAILSERVICE_ACCOUNT_ACTIVATION_SUCCESS_CLIENT = self::PREFIX . self::DELIMITER . 'ACCOUNT_ACTIVATION_SUCCESS_CLIENT';

    const EMAILSERVICE_ACCOUNT_ACTIVATION_SUCCESS_EMPLOYEE = self::PREFIX . self::DELIMITER . 'ACCOUNT_ACTIVATION_SUCCESS_EMPLOYEE';


    const EMAILSERVICE_PASSWORD_RESET_CLIENT = self::PREFIX . self::DELIMITER . 'PASSWORD_RESET_CLIENT';

    const EMAILSERVICE_PASSWORD_RESET_EMPLOYEE = self::PREFIX . self::DELIMITER . 'PASSWORD_RESET_EMPLOYEE';

    const EMAILSERVICE_PASSWORD_RESET_SUCCESS_CLIENT = self::PREFIX . self::DELIMITER . 'PASSWORD_RESET_SUCCESS_CLIENT';

    const EMAILSERVICE_PASSWORD_RESET_SUCCESS_EMPLOYEE = self::PREFIX . self::DELIMITER . 'PASSWORD_RESET_SUCCESS_EMPLOYEE';

    const EMAILSERVICE_PASSWORD_RESET_FAILURE_CLIENT = self::PREFIX . self::DELIMITER . 'PASSWORD_RESET_FAILURE_CLIENT';

    const EMAILSERVICE_PASSWORD_RESET_FAILURE_EMPLOYEE = self::PREFIX . self::DELIMITER . 'PASSWORD_RESET_FAILURE_EMPLOYEE';
    const EMAILSERVICE_PASSWORD_RESET_EXPIRED = self::PREFIX . self::DELIMITER . 'PASSWORD_RESET_EXPIRED';


    const EMAILSERVICE_WHEEL_OF_FORTUNE_PARTICIPATION = self::PREFIX . self::DELIMITER . 'WHEEL_OF_FORTUNE_PARTICIPATION';

    const EMAILSERVICE_WIN_DECLARATION_CLIENT = self::PREFIX . self::DELIMITER . 'WIN_DECLARATION_CLIENT';

    const EMAILSERVICE_WIN_DECLARATION_EMPLOYEE = self::PREFIX . self::DELIMITER . 'WIN_DECLARATION_EMPLOYEE';

    const EMAILSERVICE_WIN_DECLARATION_CONFIRMATION_EXPIRED_CLIENT = self::PREFIX . self::DELIMITER . 'WIN_DECLARATION_CONFIRMATION_EXPIRED_CLIENT';


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'service', targetEntity: EmailTemplate::class)]
    private Collection $emailTemplates;

    #[ORM\ManyToMany(targetEntity: EmailTemplateVariable::class, mappedBy: 'services')]
    private Collection $variables;


    public function __construct()
    {
        $this->emailTemplates = new ArrayCollection();
        $this->variables = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, EmailTemplate>
     */
    public function getEmailTemplates(): Collection
    {
        return $this->emailTemplates;
    }


    public function getTemplatesJson(): array
    {
        $data = [];
        foreach ($this->emailTemplates as $emailTemplate) {
            $data[] = [
                'id' => $emailTemplate->getId(),
                'name' => $emailTemplate->getName(),
                'subject' => $emailTemplate->getSubject(),
                'content' => $emailTemplate->getContent(),
                'title' => $emailTemplate->getTitle(),
                'description' => $emailTemplate->getDescription(),
                'type' => $emailTemplate->getType(),
            ];
        }
        return $data;
    }

    public function addEmailTemplate(EmailTemplate $emailTemplate): static
    {
        if (!$this->emailTemplates->contains($emailTemplate)) {
            $this->emailTemplates->add($emailTemplate);
            $emailTemplate->setService($this);
        }

        return $this;
    }

    public function removeEmailTemplate(EmailTemplate $emailTemplate): static
    {
        if ($this->emailTemplates->removeElement($emailTemplate)) {
            // set the owning side to null (unless already changed)
            if ($emailTemplate->getService() === $this) {
                $emailTemplate->setService(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, EmailTemplateVariable>
     */
    public function getVariables(): Collection
    {
        return $this->variables;
    }

    public function addVariable(EmailTemplateVariable $variable): static
    {
        if (!$this->variables->contains($variable)) {
            $this->variables->add($variable);
            $variable->addService($this);
        }

        return $this;
    }

    public function removeVariable(EmailTemplateVariable $variable): static
    {
        if ($this->variables->removeElement($variable)) {
            if ($variable->getServices()->contains($this)) {
                $variable->removeService($this);
            }
        }

        return $this;
    }



    public function getVariablesJson(): array
    {
        $data = [];

        foreach ($this->variables as $variable) {
            $data[] = [
                'id' => $variable->getId(),
                'name' => $variable->getName(),
                'label' => $variable->getLabel(),
            ];
        }

        return $data;
    }

    public function getEmailServiceJson(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'label' => $this->getLabel(),
            'description' => $this->getDescription(),
            'templates' => $this->getTemplatesJson(),
            'variables' => $this->getVariablesJson(),
        ];
    }

    public function setId(int $int)
    {
        $this->id = $int;
    }


}
