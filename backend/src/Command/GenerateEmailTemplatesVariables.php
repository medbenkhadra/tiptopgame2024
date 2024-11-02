<?php
// src/Command/GenerateEmailTemplatesVariables.php

namespace App\Command;

use App\Entity\EmailService;
use App\Entity\EmailTemplate;
use App\Entity\EmailTemplateVariable;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Connection;

class GenerateEmailTemplatesVariables extends Command
{

    private EntityManagerInterface $entityManager;

    private Connection $connection;

    public function __construct(EntityManagerInterface $entityManager, Connection $connection)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->connection = $connection;
        $this->setName('app:generate-email-templates-variables');


    }

    protected function configure():void
    {
        $this->setDescription('Generate Email Services');

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $this->connection->executeQuery('SET SQL_SAFE_UPDATES = 0');
        $this->connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');
        $this->connection->executeQuery('DELETE FROM email_template_variable');
        $this->connection->executeQuery('ALTER TABLE email_template_variable AUTO_INCREMENT = 1');
        $this->connection->executeQuery('DELETE FROM email_template_variable_email_service');
        $this->connection->executeQuery('ALTER TABLE email_template_variable_email_service AUTO_INCREMENT = 1');
        $this->connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');
        $this->connection->executeQuery('SET SQL_SAFE_UPDATES = 1');

        $this->generateEmailTemplatesVariables($output);

        $output->writeln('Email templates variables generated successfully.');

        return Command::SUCCESS;
    }

    private function generateEmailTemplatesVariables($output): void
    {
        $createAccountClient = $this->entityManager->getRepository(EmailService::class)->findOneBy(['name' => EmailService::EMAILSERVICE_CLIENT_CREATE_ACCOUNT]);
        $createAccountEmployee = $this->entityManager->getRepository(EmailService::class)->findOneBy(['name' => EmailService::EMAILSERVICE_EMPLOYEE_CREATE_ACCOUNT]);

        $accountActivationClient = $this->entityManager->getRepository(EmailService::class)->findOneBy(['name' => EmailService::EMAILSERVICE_ACCOUNT_ACTIVATION_CLIENT]);
        $accountActivationEmployee = $this->entityManager->getRepository(EmailService::class)->findOneBy(['name' => EmailService::EMAILSERVICE_ACCOUNT_ACTIVATION_EMPLOYEE]);


        $passwordResetClient = $this->entityManager->getRepository(EmailService::class)->findOneBy(['name' => EmailService::EMAILSERVICE_PASSWORD_RESET_CLIENT]);
        $passwordResetEmployee = $this->entityManager->getRepository(EmailService::class)->findOneBy(['name' => EmailService::EMAILSERVICE_PASSWORD_RESET_EMPLOYEE]);


        $successResetClient = $this->entityManager->getRepository(EmailService::class)->findOneBy(['name' => EmailService::EMAILSERVICE_PASSWORD_RESET_SUCCESS_CLIENT]);
        $successResetEmployee = $this->entityManager->getRepository(EmailService::class)->findOneBy(['name' => EmailService::EMAILSERVICE_PASSWORD_RESET_SUCCESS_EMPLOYEE]);

        $failureResetClient = $this->entityManager->getRepository(EmailService::class)->findOneBy(['name' => EmailService::EMAILSERVICE_PASSWORD_RESET_FAILURE_CLIENT]);
        $failureResetEmployee = $this->entityManager->getRepository(EmailService::class)->findOneBy(['name' => EmailService::EMAILSERVICE_PASSWORD_RESET_FAILURE_EMPLOYEE]);

        $expiredReset = $this->entityManager->getRepository(EmailService::class)->findOneBy(['name' => EmailService::EMAILSERVICE_PASSWORD_RESET_EXPIRED]);

        $wheelOfFortuneParticipation = $this->entityManager->getRepository(EmailService::class)->findOneBy(['name' => EmailService::EMAILSERVICE_WHEEL_OF_FORTUNE_PARTICIPATION]);
        $winDeclarationClient = $this->entityManager->getRepository(EmailService::class)->findOneBy(['name' => EmailService::EMAILSERVICE_WIN_DECLARATION_CLIENT]);
        $winDeclarationEmployee = $this->entityManager->getRepository(EmailService::class)->findOneBy(['name' => EmailService::EMAILSERVICE_WIN_DECLARATION_EMPLOYEE]);

        $expiredWinDeclarationClient = $this->entityManager->getRepository(EmailService::class)->findOneBy(['name' => EmailService::EMAILSERVICE_WIN_DECLARATION_CONFIRMATION_EXPIRED_CLIENT]);

        $accountActivationSuccessClient = $this->entityManager->getRepository(EmailService::class)->findOneBy(['name' => EmailService::EMAILSERVICE_ACCOUNT_ACTIVATION_SUCCESS_CLIENT]);
        $accountActivationSuccessEmployee = $this->entityManager->getRepository(EmailService::class)->findOneBy(['name' => EmailService::EMAILSERVICE_ACCOUNT_ACTIVATION_SUCCESS_EMPLOYEE]);



        $variables = [
            [
                'label' => 'Nom du client',
                'name' => 'client_lastname',
                'services' => [$createAccountClient, $accountActivationClient, $passwordResetClient, $successResetClient, $failureResetClient, $wheelOfFortuneParticipation, $winDeclarationClient , $winDeclarationEmployee , $expiredReset , $expiredWinDeclarationClient ,  $accountActivationSuccessClient, $accountActivationSuccessEmployee],
            ],
            [
                'label' => 'Prénom du client',
                'name' => 'client_firstname',
                'services' => [$createAccountClient, $accountActivationClient, $passwordResetClient, $successResetClient, $failureResetClient, $wheelOfFortuneParticipation,  $winDeclarationClient , $winDeclarationEmployee ,$expiredReset , $expiredWinDeclarationClient ,  $accountActivationSuccessClient, $accountActivationSuccessEmployee],
            ],
            [
                'label' => 'E-mail du client',
                'name' => 'client_email',
                'services' => [$createAccountClient, $accountActivationClient, $passwordResetClient, $successResetClient, $failureResetClient, $wheelOfFortuneParticipation,  $winDeclarationClient , $winDeclarationEmployee  , $expiredReset , $expiredWinDeclarationClient ,  $accountActivationSuccessClient, $accountActivationSuccessEmployee],
            ],

            [
                'label' => 'Numéro de téléphone du client',
                'name' => 'client_phone',
                'services' => [$createAccountClient, $accountActivationClient, $passwordResetClient, $successResetClient, $failureResetClient, $wheelOfFortuneParticipation,  $winDeclarationClient , $winDeclarationEmployee , $expiredWinDeclarationClient , $accountActivationSuccessClient, $accountActivationSuccessEmployee ],
            ],

            [
                'label' => 'Adresse du client',
                'name' => 'client_address',
                'services' => [$createAccountClient, $accountActivationClient, $passwordResetClient, $successResetClient, $failureResetClient, $wheelOfFortuneParticipation,  $winDeclarationClient , $winDeclarationEmployee , $expiredWinDeclarationClient , $accountActivationSuccessClient, $accountActivationSuccessEmployee ],
            ],

            [
                'label' => 'Code postal du client',
                'name' => 'client_zipcode',
                'services' => [$createAccountClient, $accountActivationClient, $passwordResetClient, $successResetClient, $failureResetClient, $wheelOfFortuneParticipation,  $winDeclarationClient , $winDeclarationEmployee , $expiredWinDeclarationClient, $accountActivationSuccessClient, $accountActivationSuccessEmployee ],
            ],

            [
                'label' => 'Ville du client',
                'name' => 'client_city',
                'services' => [$createAccountClient, $accountActivationClient, $passwordResetClient, $successResetClient, $failureResetClient, $wheelOfFortuneParticipation,  $winDeclarationClient , $winDeclarationEmployee , $expiredWinDeclarationClient , $accountActivationSuccessClient, $accountActivationSuccessEmployee],
            ],

            [
                'label' => 'Pays du client',
                'name' => 'client_country',
                'services' => [$createAccountClient, $accountActivationClient, $passwordResetClient, $successResetClient, $failureResetClient, $wheelOfFortuneParticipation,  $winDeclarationClient , $winDeclarationEmployee , $expiredWinDeclarationClient , $accountActivationSuccessClient, $accountActivationSuccessEmployee ],
            ],

            [
                'label' => 'Nom de l\'employé',
                'name' => 'employee_lastname',
                'services' => [$createAccountEmployee, $accountActivationEmployee, $passwordResetEmployee, $successResetEmployee, $failureResetEmployee , $expiredWinDeclarationClient , $accountActivationSuccessClient, $accountActivationSuccessEmployee],
            ],

            [
                'label' => 'Prénom de l\'employé',
                'name' => 'employee_firstname',
                'services' => [$createAccountEmployee, $accountActivationEmployee, $passwordResetEmployee, $successResetEmployee, $failureResetEmployee , $expiredWinDeclarationClient , $accountActivationSuccessClient, $accountActivationSuccessEmployee],
            ],

            [
                'label' => 'E-mail de l\'employé',
                'name' => 'employee_email',
                'services' => [$createAccountEmployee, $accountActivationEmployee, $passwordResetEmployee, $successResetEmployee, $failureResetEmployee, $expiredWinDeclarationClient ,  $accountActivationSuccessClient, $accountActivationSuccessEmployee],
            ],

            [
                'label' => 'Numéro de téléphone de l\'employé',
                'name' => 'employee_phone',
                'services' => [$createAccountEmployee, $accountActivationEmployee, $passwordResetEmployee, $successResetEmployee, $failureResetEmployee , $expiredWinDeclarationClient ,  $accountActivationSuccessClient, $accountActivationSuccessEmployee],
            ],

            [
                'label' => 'Numéro du ticket',
                'name' => 'ticket_number',
                'services' => [$wheelOfFortuneParticipation,  $winDeclarationClient , $winDeclarationEmployee , $expiredWinDeclarationClient ,  $accountActivationSuccessClient, $accountActivationSuccessEmployee],
            ],

            [
                'label' => 'Nom du magasin',
                'name' => 'store_name',
                'services' => [$createAccountClient, $createAccountEmployee, $accountActivationClient, $accountActivationEmployee, $passwordResetClient, $passwordResetEmployee , $successResetClient, $successResetEmployee , $failureResetClient , $failureResetEmployee,  $winDeclarationClient , $winDeclarationEmployee, $expiredWinDeclarationClient ],
            ],

            [
                'label' => 'E-mail du magasin',
                'name' => 'store_email',
                'services' => [$createAccountClient, $createAccountEmployee, $accountActivationClient, $accountActivationEmployee, $passwordResetClient, $passwordResetEmployee , $successResetClient, $successResetEmployee , $failureResetClient , $failureResetEmployee,  $winDeclarationClient , $winDeclarationEmployee , $expiredWinDeclarationClient],
            ],

            [
                'label' => 'Adresse du magasin',
                'name' => 'store_address',
                'services' => [$createAccountClient, $createAccountEmployee, $accountActivationClient, $accountActivationEmployee, $passwordResetClient, $passwordResetEmployee , $successResetClient, $successResetEmployee , $failureResetClient , $failureResetEmployee,  $winDeclarationClient , $winDeclarationEmployee , $expiredWinDeclarationClient],
            ],

            [
                'label' => 'Code postal du magasin',
                'name' => 'store_zipcode',
                'services' => [$createAccountClient, $createAccountEmployee, $accountActivationClient, $accountActivationEmployee, $passwordResetClient, $passwordResetEmployee , $successResetClient, $successResetEmployee , $failureResetClient , $failureResetEmployee,  $winDeclarationClient , $winDeclarationEmployee , $expiredWinDeclarationClient],
            ],

            [
                'label' => 'Ville du magasin',
                'name' => 'store_city',
                'services' => [$createAccountClient, $createAccountEmployee, $accountActivationClient, $accountActivationEmployee, $passwordResetClient, $passwordResetEmployee , $successResetClient, $successResetEmployee , $failureResetClient , $failureResetEmployee,  $winDeclarationClient , $winDeclarationEmployee , $expiredWinDeclarationClient],
            ],

            [
                'label' => 'Pays du magasin',
                'name' => 'store_country',
                'services' => [$createAccountClient, $createAccountEmployee, $accountActivationClient, $accountActivationEmployee, $passwordResetClient, $passwordResetEmployee , $successResetClient, $successResetEmployee , $failureResetClient , $failureResetEmployee,  $winDeclarationClient , $winDeclarationEmployee , $expiredWinDeclarationClient],
            ],


            [
                'label' => 'Date de création du ticket',
                'name' => 'ticket_created_at',
                'services' => [$wheelOfFortuneParticipation,  $winDeclarationClient , $winDeclarationEmployee , $expiredWinDeclarationClient],
            ],

            [
                'label' => 'Date d\'impression du ticket',
                'name' => 'ticket_printed_at',
                'services' => [$wheelOfFortuneParticipation,  $winDeclarationClient , $winDeclarationEmployee , $expiredWinDeclarationClient ],
            ],

            [
                'label' => 'Date de gain du ticket',
                'name' => 'ticket_confirmed_at',
                'services' => [$wheelOfFortuneParticipation,  $winDeclarationClient , $winDeclarationEmployee , $expiredWinDeclarationClient],
            ],


            [
                'label' => 'Lien de réinitialisation du mot de passe',
                'name' => 'reset_password_link',
                'services' => [$passwordResetClient, $passwordResetEmployee , $expiredWinDeclarationClient],
            ],

            [
                'label' => 'Lien d\'activation du compte client',
                'name' => 'activate_account_link_client',
                'services' => [$accountActivationClient , $expiredWinDeclarationClient  , $accountActivationSuccessClient, $accountActivationSuccessEmployee],
            ],

            [
                'label' => 'Lien d\'activation du compte employé',
                'name' => 'activate_account_link_employee',
                'services' => [$accountActivationEmployee , $expiredWinDeclarationClient ,  $accountActivationSuccessClient, $accountActivationSuccessEmployee],
            ],

                [
                    'label' => 'Lien de participation à la roue de la fortune',
                    'name' => 'wheel_of_fortune_participation_link',
                    'services' => [$wheelOfFortuneParticipation , $expiredWinDeclarationClient],
                ],

                [
                    'label' => 'Lien de déclaration de gain',
                    'name' => 'win_declaration_link',
                    'services' => [ $winDeclarationClient , $winDeclarationEmployee , $expiredWinDeclarationClient ],
                ],
            [
                'label' => 'Mot de passe',
                'name' => 'password',
                'services' => [$createAccountEmployee , $createAccountClient , $passwordResetClient , $passwordResetEmployee , $expiredWinDeclarationClient],
            ],
            [
                'label' => 'Date d\'expiration Token',
                'name' => 'token_expiration_date',
                'services' => [$createAccountEmployee , $createAccountClient , $passwordResetClient , $passwordResetEmployee , $expiredWinDeclarationClient],
            ]


        ];


        foreach ($variables as $variable) {
            $emailTemplateVariable = new EmailTemplateVariable();
            $emailTemplateVariable->setName($variable['name']);
            $emailTemplateVariable->addServices($variable['services']);
            $emailTemplateVariable->setLabel($variable['label']);

            $this->entityManager->persist($emailTemplateVariable);
        }

        $this->entityManager->flush();


    }


}
