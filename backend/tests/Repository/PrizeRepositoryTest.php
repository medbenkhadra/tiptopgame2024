<?php

namespace App\Tests\Repository;

use App\Entity\Prize;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PrizeRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;

    private $prizeRepository;

    /**
     * @throws NotSupported
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->prizeRepository = $this->entityManager->getRepository(Prize::class);
    }

    public function testFindAll(): void
    {
        $prizes = $this->prizeRepository->findAll();

        $this->assertIsArray($prizes);
        foreach ($prizes as $prize) {
            $this->assertInstanceOf(Prize::class, $prize);
        }
    }

    public function testFindOneBy(): void
    {
        $criteria = ['id' => 1];
        $prize = $this->prizeRepository->findOneBy($criteria);
        $this->assertInstanceOf(Prize::class, $prize);
    }

}
