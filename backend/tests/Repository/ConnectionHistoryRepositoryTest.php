<?php

namespace App\Tests\Repository;

use App\Entity\ConnectionHistory;
use Doctrine\ORM\EntityManager;

use Doctrine\ORM\Exception\NotSupported;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ConnectionHistoryRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;

    private $connectionHistoryRepository;

    /**
     * @throws NotSupported
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->connectionHistoryRepository = $this->entityManager->getRepository(ConnectionHistory::class);
    }

    public function testFindAll(): void
    {
        $connectionHistories = $this->connectionHistoryRepository->findAll();

        $this->assertIsArray($connectionHistories);
        foreach ($connectionHistories as $connectionHistory) {
            $this->assertInstanceOf(ConnectionHistory::class, $connectionHistory);
        }
    }

    public function testFindOneBy(): void
    {
        $criteria = ['id' => 1];
        $connectionHistory = $this->connectionHistoryRepository->findOneBy($criteria);
        $this->assertInstanceOf(ConnectionHistory::class, $connectionHistory);
    }
}
