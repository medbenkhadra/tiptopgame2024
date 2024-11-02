<?php

namespace App\Tests\Repository;

use App\Entity\Store;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StoreRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;

    private $storeRepository;

    /**
     * @throws NotSupported
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->storeRepository = $this->entityManager->getRepository(Store::class);
    }

    public function testFindAll(): void
    {
        $stores = $this->storeRepository->findAll();

        $this->assertIsArray($stores);
        foreach ($stores as $store) {
            $this->assertInstanceOf(Store::class, $store);
        }
    }

    public function testFindOneBy(): void
    {
        $criteria = ['id' => 1];
        $store = $this->storeRepository->findOneBy($criteria);
        $this->assertInstanceOf(Store::class, $store);
    }

}