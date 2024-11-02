<?php

namespace App\Tests\Repository;

use App\Entity\EmailingHistory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class EmailingHistoryRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;

    private $emailingHistoryRepository;

    /**
     * @throws NotSupported
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->emailingHistoryRepository = $this->entityManager->getRepository(EmailingHistory::class);
    }

    public function testFindAll(): void
    {
        $emailingHistories = $this->emailingHistoryRepository->findAll();

        $this->assertIsArray($emailingHistories);
        foreach ($emailingHistories as $emailingHistory) {
            $this->assertInstanceOf(EmailingHistory::class, $emailingHistory);
        }
    }

    public function testFindOneBy(): void
    {
        $criteria = ['id' => 1];
        $emailingHistory = $this->emailingHistoryRepository->findOneBy($criteria);
        $this->assertInstanceOf(EmailingHistory::class, $emailingHistory);
    }

}
