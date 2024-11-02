<?php

namespace App\Tests\Command;

use App\Command\GenerateBadges;
use App\Entity\Badge;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateBadgesTest extends TestCase
{
    private $entityManager;
    private $connection;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->connection = $this->createMock(Connection::class);
    }

    public function testExecute(): void
    {
        $this->connection->expects($this->any())
            ->method('executeQuery');

        $this->entityManager->expects($this->exactly(5))
            ->method('persist')
            ->with($this->isInstanceOf(Badge::class));

        $this->entityManager->expects($this->once())
            ->method('flush');

        $command = new GenerateBadges($this->entityManager, $this->connection);

        $application = new Application();
        $application->add($command);

        $command = $application->find('app:generate-badges');

        $commandTester = new CommandTester($command);

        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Badges generated successfully !', $output);
    }
}
