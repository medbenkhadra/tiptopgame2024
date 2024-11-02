<?php

namespace App\Tests\Repository;

use App\Entity\TicketHistory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TicketHistoryRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;

    private $ticketHistoryRepository;

    /**
     * @throws NotSupported
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->ticketHistoryRepository = $this->entityManager->getRepository(TicketHistory::class);
    }

    public function testFindAll(): void
    {
        $ticketHistories = $this->ticketHistoryRepository->findAll();

        $this->assertIsArray($ticketHistories);
        foreach ($ticketHistories as $ticketHistory) {
            $this->assertInstanceOf(TicketHistory::class, $ticketHistory);
        }
    }

    public function testFindOneBy(): void
    {
        $criteria = ['id' => 1];
        $ticketHistory = $this->ticketHistoryRepository->findOneBy($criteria);
        $this->assertInstanceOf(TicketHistory::class, $ticketHistory);
    }

}