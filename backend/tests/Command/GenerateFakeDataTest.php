<?php

namespace App\Tests\Command;

use App\Command\GenerateFakeData;
use App\Entity\GameConfig;
use App\Entity\Role;
use App\Entity\Store;
use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class GenerateFakeDataTest extends TestCase
{
    private $entityManager;
    private $passwordEncoder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->passwordEncoder = $this->createMock(UserPasswordHasherInterface::class);
    }

    public function testExecute(): void
    {
        $roleRepository = $this->createMock(EntityRepository::class);
        $roleRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn(new Role());

        $storeRepository = $this->createMock(EntityRepository::class);
        $storeRepository->expects($this->any())
            ->method('findAll')
            ->willReturn([new Store()]);

        $userRepository = $this->createMock(EntityRepository::class);
        $userRepository->expects($this->any())
            ->method('findBy')
            ->willReturn([new User()]);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->any())
            ->method('getQuery')
            ->willReturnSelf();

        $ticketRepository = $this->createMock(EntityRepository::class);
        $ticketRepository->expects($this->any())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);


        $gameConfigRepository = $this->createMock(EntityRepository::class);
        $gameConfigRepository->expects($this->any())
            ->method('find')
            ->willReturn(new GameConfig());

        $this->entityManager->expects($this->any())
            ->method('getRepository')
            ->willReturnMap([
                [Role::class, $roleRepository],
                [Store::class, $storeRepository],
                [User::class, $userRepository],
                [Ticket::class, $ticketRepository],
                [GameConfig::class, $gameConfigRepository]
            ]);

        $command = new GenerateFakeData($this->entityManager, $this->passwordEncoder);

        $application = new Application();
        $application->add($command);

        $command = $application->find('app:generate-data');

        $commandTester = new CommandTester($command);

        $commandTester->execute([]);

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Fake data generated.', $output);
    }
}
