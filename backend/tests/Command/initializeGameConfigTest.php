<?php

namespace App\Tests\Command;

use App\Command\initializeGameConfigCommand;
use App\Entity\GameConfig;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class initializeGameConfigCommandTest extends TestCase
{
    private $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    public function testExecuteWhenGameConfigDoesNotExist(): void
    {
        $repository = $this->getMockBuilder(ObjectRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repository->expects($this->once())
            ->method('findAll')
            ->willReturn(null);

        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($repository);
        $this->entityManager
            ->expects($this->once())
            ->method('persist');
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $command = new initializeGameConfigCommand($this->entityManager);

        $application = new Application();
        $application->add($command);

        $command = $application->find('app:game-config-init');

        $commandTester = new CommandTester($command);

        $commandTester->execute([]);

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Game configuration initialized successfully !', $output);
    }

    public function testExecuteWhenGameConfigExists(): void
    {
        $gameConfig = new GameConfig();

        $repository = $this->getMockBuilder(ObjectRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repository->expects($this->once())
            ->method('findAll')
            ->willReturn([$gameConfig]);

        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($repository);
        $this->entityManager
            ->expects($this->never())
            ->method('persist');
        $this->entityManager
            ->expects($this->never())
            ->method('flush');

        $command = new initializeGameConfigCommand($this->entityManager);

        $application = new Application();
        $application->add($command);

        $command = $application->find('app:game-config-init');

        $commandTester = new CommandTester($command);

        $commandTester->execute([]);

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Game configuration already initialized !!!!', $output);
    }
}
