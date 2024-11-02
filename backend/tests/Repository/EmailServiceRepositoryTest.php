<?php

namespace App\Tests\Repository;

use App\Entity\EmailService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EmailServiceRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;

    private $emailServiceRepository;

    /**
     * @throws NotSupported
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->emailServiceRepository = $this->entityManager->getRepository(EmailService::class);
    }

    public function testFindAll(): void
    {
        $emailServices = $this->emailServiceRepository->findAll();

        $this->assertIsArray($emailServices);
        foreach ($emailServices as $emailService) {
            $this->assertInstanceOf(EmailService::class, $emailService);
        }
    }

    public function testFindOneBy(): void
    {
        $criteria = ['id' => 1];
        $emailService = $this->emailServiceRepository->findOneBy($criteria);
        $this->assertInstanceOf(EmailService::class, $emailService);
    }

}