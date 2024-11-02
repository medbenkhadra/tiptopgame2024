<?php

namespace App\Tests\Command;

use App\Command\ResetGame;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ResetGameTest extends TestCase
{
    private $entityManager;
    private $connection;

    protected function setUp(): void
    {
        parent::setUp();

        // Mocking EntityManagerInterface
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        // Mocking Connection
        $this->connection = $this->createMock(Connection::class);
    }

    public function testExecute(): void
    {
        $process = $this->createMock(Process::class);
        $process->expects($this->any())
            ->method('mustRun');

        $command = new ResetGame($this->entityManager, $this->connection);

        $application = new Application();
        $application->add($command);

        $command = $application->find('app:reset-game');

        $commandTester = new CommandTester($command);

        $commandTester->execute([]);

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Game reset successfully.', $output);
    }
}
