<?php

namespace App\Tests\Command;

use App\Command\GenerateEmailServices;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateEmailServicesTest extends TestCase
{
    private $entityManager;

    private $connection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->connection = $this->createMock(Connection::class);
    }

    public function testExecute(): void
    {
        $this->entityManager
            ->expects($this->exactly(17))
            ->method('persist');
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $command = new GenerateEmailServices($this->entityManager, $this->connection);

        $application = new Application();
        $application->add($command);

        $command = $application->find('app:generate-email-services');

        $commandTester = new CommandTester($command);

        $commandTester->execute([]);

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Email Services generated successfully.', $output);
    }
}
