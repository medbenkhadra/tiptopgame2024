<?php

namespace App\Tests\Repository;

use App\Entity\ActionHistory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ActionHistoryRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;

    private $actionHistoryRepository;

    /**
     * @throws NotSupported
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->actionHistoryRepository = $this->entityManager->getRepository(ActionHistory::class);
    }


    public function testFindAll(): void
    {
        $actionHistories = $this->actionHistoryRepository->findAll();

        $this->assertIsArray($actionHistories);
        foreach ($actionHistories as $actionHistory) {
            $this->assertInstanceOf(ActionHistory::class, $actionHistory);
        }
    }

    public function testFindOneBy(): void
    {
        $criteria = ['id' => 1];
        $actionHistory = $this->actionHistoryRepository->findOneBy($criteria);
        $this->assertInstanceOf(ActionHistory::class, $actionHistory);
    }


}