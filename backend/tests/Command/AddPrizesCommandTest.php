<?php

namespace App\Tests\Command;

use App\Command\AddPrizesCommand;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class AddPrizesCommandTest extends TestCase
{
    public function testExecute(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $connection = $this->createMock(Connection::class);

        $connection->expects($this->exactly(6))
            ->method('executeQuery');

        $command = new AddPrizesCommand($entityManager, $connection);
        $application = new Application();
        $application->add($command);

        $command = $application->find('app:add-prizes');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Prizes added to the database.', $output);
    }
}
