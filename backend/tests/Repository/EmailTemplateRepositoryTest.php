<?php

namespace App\Tests\Repository;

use App\Entity\EmailTemplate;
use Doctrine\ORM\EntityManager;

use Doctrine\ORM\Exception\NotSupported;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EmailTemplateRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;

    private $emailTemplateRepository;

    /**
     * @throws NotSupported
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->emailTemplateRepository = $this->entityManager->getRepository(EmailTemplate::class);
    }

    public function testFindAll(): void
    {
        $emailTemplates = $this->emailTemplateRepository->findAll();

        $this->assertIsArray($emailTemplates);
        foreach ($emailTemplates as $emailTemplate) {
            $this->assertInstanceOf(EmailTemplate::class, $emailTemplate);
        }
    }

    public function testFindOneBy(): void
    {
        $criteria = ['id' => 1];
        $emailTemplate = $this->emailTemplateRepository->findOneBy($criteria);
        $this->assertInstanceOf(EmailTemplate::class, $emailTemplate);
    }

}