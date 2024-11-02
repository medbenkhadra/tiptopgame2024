<?php

namespace App\Tests\Command;

use App\Command\GenerateEmailTemplatesVariables;
use App\Entity\EmailService;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateEmailTemplatesVariablesTest extends TestCase
{
    private $entityManager;
    private $connection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $emailServiceRepository = $this->createMock(ObjectRepository::class);
        $emailServiceRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn(new EmailService());
        $this->entityManager->expects($this->any())
            ->method('getRepository')
            ->with(EmailService::class)
            ->willReturn($emailServiceRepository);
        $this->connection = $this->createMock(Connection::class);
    }

    public function testExecute(): void
    {
        $this->connection->expects($this->exactly(8))
            ->method('executeQuery')
            ->withConsecutive(
                ['SET SQL_SAFE_UPDATES = 0'],
                ['SET FOREIGN_KEY_CHECKS=0'],
                ['DELETE FROM email_template_variable'],
                ['ALTER TABLE email_template_variable AUTO_INCREMENT = 1'],
                ['DELETE FROM email_template_variable_email_service'],
                ['ALTER TABLE email_template_variable_email_service AUTO_INCREMENT = 1'],
                ['SET FOREIGN_KEY_CHECKS=1'],
                ['SET SQL_SAFE_UPDATES = 1']
            );

        $command = new GenerateEmailTemplatesVariables($this->entityManager, $this->connection);

        $application = new Application();
        $application->add($command);

        $command = $application->find('app:generate-email-templates-variables');

        $commandTester = new CommandTester($command);

        $commandTester->execute([]);

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Email templates variables generated successfully.', $output);
    }
}
