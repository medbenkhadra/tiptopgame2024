<?php

namespace App\Tests\Command;

use App\Command\AddFirstAdmin;
use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AddFirstAdminTest extends TestCase
{
    private $entityManager;
    private $passwordEncoder;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->passwordEncoder = $this->createMock(UserPasswordHasherInterface::class);
    }

    public function testExecute(): void
    {
        $roleRepository = $this->createMock(RoleRepository::class);
        $roleRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(new Role());

        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->with(Role::class)
            ->willReturn($roleRepository);

        $command = new AddFirstAdmin($this->entityManager, $this->passwordEncoder);
        $commandTester = new CommandTester($command);


        $commandTester->execute([]);

        $expectedOutput = trim('AMMAR has been  added  to the user table. (admin)');
        $actualOutput = trim($commandTester->getDisplay());

        $this->assertStringContainsString($expectedOutput, $actualOutput);

    }
}
