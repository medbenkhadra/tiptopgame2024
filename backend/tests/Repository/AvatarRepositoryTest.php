<?php

namespace App\Tests\Repository;

use App\Entity\Avatar;
use Doctrine\ORM\EntityManager;

use Doctrine\ORM\Exception\NotSupported;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AvatarRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;

    private $avatarRepository;

    /**
     * @throws NotSupported
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->avatarRepository = $this->entityManager->getRepository(Avatar::class);
    }

    public function testFindAll(): void
    {
        $avatars = $this->avatarRepository->findAll();

        $this->assertIsArray($avatars);
        foreach ($avatars as $avatar) {
            $this->assertInstanceOf(Avatar::class, $avatar);
        }
    }

    public function testFindOneBy(): void
    {
        $criteria = ['id' => 1];
        $avatar = $this->avatarRepository->findOneBy($criteria);
        $this->assertInstanceOf(Avatar::class, $avatar);
    }

}

