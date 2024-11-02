<?php

namespace App\Tests\Repository;

use App\Entity\LoyaltyPoints;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class LoyaltyPointsRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;

    private $loyaltyPointsRepository;

    /**
     * @throws NotSupported
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->loyaltyPointsRepository = $this->entityManager->getRepository(LoyaltyPoints::class);
    }

    public function testFindAll(): void
    {
        $loyaltyPoints = $this->loyaltyPointsRepository->findAll();

        $this->assertIsArray($loyaltyPoints);
        foreach ($loyaltyPoints as $loyaltyPoint) {
            $this->assertInstanceOf(LoyaltyPoints::class, $loyaltyPoint);
        }
    }

    public function testFindOneBy(): void
    {
        $criteria = ['id' => 1];
        $loyaltyPoint = $this->loyaltyPointsRepository->findOneBy($criteria);
        $this->assertInstanceOf(LoyaltyPoints::class, $loyaltyPoint);
    }

}