<?php

namespace App\Tests\Repository;

use App\Entity\Role;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RoleRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;

    private $roleRepository;

    /**
     * @throws NotSupported
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->roleRepository = $this->entityManager->getRepository(Role::class);
    }

    public function testFindAll(): void
    {
        $roles = $this->roleRepository->findAll();

        $this->assertIsArray($roles);
        foreach ($roles as $role) {
            $this->assertInstanceOf(Role::class, $role);
        }
    }

    public function testFindOneBy(): void
    {
        $criteria = ['id' => 1];
        $role = $this->roleRepository->findOneBy($criteria);
        $this->assertInstanceOf(Role::class, $role);
    }

}