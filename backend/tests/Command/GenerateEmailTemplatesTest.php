<?php

namespace App\Tests\Command;

use App\Command\GenerateEmailTemplates;
use App\Entity\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateEmailTemplatesTest extends TestCase
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
        $emailService = new EmailService();
        $emailServiceRepository = $this->createMock(ObjectRepository::class);
        $emailServiceRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$emailService]);

        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->with(EmailService::class)
            ->willReturn($emailServiceRepository);

        $this->connection->expects($this->any())
            ->method('executeQuery')
            ->withConsecutive(
                ['SET SQL_SAFE_UPDATES = 0'],
                ['SET FOREIGN_KEY_CHECKS=0'],
                ['DELETE FROM email_template'],
                ['ALTER TABLE email_template AUTO_INCREMENT = 1'],
                ['SET FOREIGN_KEY_CHECKS=1']
            );

        $command = new GenerateEmailTemplates($this->entityManager, $this->connection);

        $application = new Application();
        $application->add($command);

        $command = $application->find('app:generate-email-templates');

        $commandTester = new CommandTester($command);

        $commandTester->execute([]);

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Email templates generated successfully.', $output);
    }
}
