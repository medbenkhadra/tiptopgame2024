<?php

namespace App\Tests\Repository;

use App\Entity\Badge;
use Doctrine\ORM\EntityManager;

use Doctrine\ORM\Exception\NotSupported;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BadgeRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;

    private $badgeRepository;

    /**
     * @throws NotSupported
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->badgeRepository = $this->entityManager->getRepository(Badge::class);
    }

    public function testFindAll(): void
    {
        $badges = $this->badgeRepository->findAll();

        $this->assertIsArray($badges);
        foreach ($badges as $badge) {
            $this->assertInstanceOf(Badge::class, $badge);
        }
    }

    public function testFindOneBy(): void
    {
        $criteria = ['id' => 1];
        $badge = $this->badgeRepository->findOneBy($criteria);
        $this->assertInstanceOf(Badge::class, $badge);
    }

}