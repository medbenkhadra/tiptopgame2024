<?php

namespace App\Tests\Repository;

use App\Entity\GameConfig;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GameConfigRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;

    private $gameConfigRepository;

    /**
     * @throws NotSupported
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->gameConfigRepository = $this->entityManager->getRepository(GameConfig::class);
    }

    public function testFindAll(): void
    {
        $gameConfigs = $this->gameConfigRepository->findAll();

        $this->assertIsArray($gameConfigs);
        foreach ($gameConfigs as $gameConfig) {
            $this->assertInstanceOf(GameConfig::class, $gameConfig);
        }
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testFindOneBy(): void
    {
        $gameConfig = new GameConfig();
        $gameConfig->setStartDate('01/01/2021');
        $gameConfig->setTime('12:00');

        $this->entityManager->persist($gameConfig);
        $this->entityManager->flush();


        $criteria = ['id' => $gameConfig->getId()];
        $gameConfig = $this->gameConfigRepository->findOneBy($criteria);
        $this->assertInstanceOf(GameConfig::class, $gameConfig);
    }

}