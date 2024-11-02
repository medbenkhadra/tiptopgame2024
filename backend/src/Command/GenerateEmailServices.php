<?php
// src/Command/AddCompanyCommand.php

namespace App\Command;

use App\Entity\EmailService;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Connection;

class GenerateEmailServices extends Command
{

    private EntityManagerInterface $entityManager;

    private Connection $connection;

    public function __construct(EntityManagerInterface $entityManager  , Connection $connection)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->connection = $connection;
        $this->setName('app:generate-email-services');


    }

    protected function configure():void
    {
        $this->setDescription('Generate Email Services');

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $this->connection->executeQuery('SET SQL_SAFE_UPDATES = 0');
        $this->connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');
        $this->connection->executeQuery('DELETE FROM email_service');
        $this->connection->executeQuery('ALTER TABLE email_service AUTO_INCREMENT = 1');
        $this->connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');
        $this->connection->executeQuery('SET SQL_SAFE_UPDATES = 1');

        $this->generateTemplatesServices();

        $output->writeln('Email Services generated successfully.');

        return Command::SUCCESS;
    }

    private function generateTemplatesServices()
    {

        $services = [
            [
                'name' => 'EMAILSERVICE_CLIENT_CREATE_ACCOUNT',
                'label' => 'Création de compte client',
                'description' => 'Email envoyé lors de d\'une nouvelle inscription à un compte client',
            ],
            [
                'name' => 'EMAILSERVICE_EMPLOYEE_CREATE_ACCOUNT',
                'label' => 'Création de compte employé',
                'description' => 'Email envoyé lors de d\'une nouvelle inscription à un compte employé',
            ],


            [
                'name' => 'EMAILSERVICE_ACCOUNT_ACTIVATION_CLIENT',
                'label' => 'Activation de compte client',
                'description' => 'Email envoyé lors de l\'activation d\'un compte client',
            ],

            [
                'name' => 'EMAILSERVICE_ACCOUNT_ACTIVATION_EMPLOYEE',
                'label' => 'Activation de compte employé',
                'description' => 'Email envoyé lors de l\'activation d\'un compte employé',
            ],

            [
                'name' => 'EMAILSERVICE_ACCOUNT_ACTIVATION_SUCCESS_CLIENT',
                'label' => 'Activation de compte client réussie',
                'description' => 'Email envoyé lors de l\'activation réussie d\'un compte client',
            ],

            [
                'name' => 'EMAILSERVICE_ACCOUNT_ACTIVATION_SUCCESS_EMPLOYEE',
                'label' => 'Activation de compte employé réussie',
                'description' => 'Email envoyé lors de l\'activation réussie d\'un compte employé',
            ],

            [
                'name' => 'EMAILSERVICE_PASSWORD_RESET_EMPLOYEE',
                'label' => 'Réinitialisation de mot de passe employé',
                'description' => 'Email envoyé lors de la réinitialisation d\'un mot de passe employé',
            ],

            [
                'name' => 'EMAILSERVICE_PASSWORD_RESET_CLIENT',
                'label' => 'Réinitialisation de mot de passe client',
                'description' => 'Email envoyé lors de la réinitialisation d\'un mot de passe client',
            ],

            [
                'name' => 'EMAILSERVICE_PASSWORD_RESET_SUCCESS_CLIENT',
                'label' => 'Réinitialisation de mot de passe réussie client',
                'description' => 'Email envoyé lors de la réinitialisation réussie d\'un mot de passe client',
            ],
            [
                'name' => 'EMAILSERVICE_PASSWORD_RESET_SUCCESS_EMPLOYEE',
                'label' => 'Réinitialisation de mot de passe réussie employé',
                'description' => 'Email envoyé lors de la réinitialisation réussie d\'un mot de passe employé',
            ],
            [
                'name' => 'EMAILSERVICE_PASSWORD_RESET_FAILURE_CLIENT',
                'label' => 'Réinitialisation de mot de passe échouée - client',
                'description' => 'Email envoyé lors de la réinitialisation échouée d\'un mot de passe client',
            ],
            [
                'name' => 'EMAILSERVICE_PASSWORD_RESET_FAILURE_EMPLOYEE',
                'label' => 'Réinitialisation de mot de passe échouée - employé',
                'description' => 'Email envoyé lors de la réinitialisation échouée d\'un mot de passe employé',
            ],
            [
                'name' => 'EMAILSERVICE_PASSWORD_RESET_EXPIRED',
                'label' => 'Réinitialisation de mot de passe expirée',
                'description' => 'Email envoyé lors de la réinitialisation expirée d\'un mot de passe',
            ],
            [
                'name' => 'EMAILSERVICE_WHEEL_OF_FORTUNE_PARTICIPATION',
                'label' => 'Participation à la roue de la fortune',
                'description' => 'Email envoyé lors de la participation à la roue de la fortune',
            ],
            [
                'name' => 'EMAILSERVICE_WIN_DECLARATION_CLIENT',
                'label' => 'Déclaration de gain client',
                'description' => 'Email envoyé lors de la déclaration d\'un gain client',
            ],
            [
                'name' => 'EMAILSERVICE_WIN_DECLARATION_EMPLOYEE',
                'label' => 'Déclaration de gain employé',
                'description' => 'Email envoyé lors de la déclaration d\'un gain employé',
            ],
            [
                'name' => 'EMAILSERVICE_WIN_DECLARATION_CONFIRMATION_EXPIRED_CLIENT',
                'label' => 'Expiration de confirmation de gain',
                'description' => 'Email envoyé lors de l\'expiration de confirmation d\'un gain',
            ],

        ];



        foreach ($services as $service) {
            $emailService = new EmailService();
            $emailService->setName($service['name']);
            $emailService->setLabel($service['label']);
            $emailService->setDescription($service['description']);

            $this->entityManager->persist($emailService);
        }

        $this->entityManager->flush();



    }


}
