<?php
// src/Command/AddCompanyCommand.php

namespace App\Command;

use App\Entity\EmailService;
use App\Entity\EmailTemplate;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Connection;

class GenerateEmailTemplates extends Command
{

    private EntityManagerInterface $entityManager;

    private Connection $connection;

    public function __construct(EntityManagerInterface $entityManager  , Connection $connection)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->connection = $connection;
        $this->setName('app:generate-email-templates');


    }

    protected function configure():void
    {
        $this->setDescription('Generate Email templates');

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $this->connection->executeQuery('SET SQL_SAFE_UPDATES = 0');
        $this->connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');
        $this->connection->executeQuery('DELETE FROM email_template');
        $this->connection->executeQuery('ALTER TABLE email_template AUTO_INCREMENT = 1');
        $this->connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');
        $this->connection->executeQuery('SET SQL_SAFE_UPDATES = 1');

        $this->generateEmailTemplates();

        $output->writeln('Email templates generated successfully.');

        return Command::SUCCESS;
    }

    private function generateEmailTemplates(): void
    {
        $templatesServices = $this->entityManager->getRepository(EmailService::class)->findAll();


        $templates = [
            [
                'name' => 'création de compte Client',
                'title' => 'Création de Compte Client',
                'description' => 'Email envoyé pour informer de la création d\'un compte client.',
                'type' => 'service',
                'service' => array_key_exists(0, $templatesServices) ? $templatesServices[0] : null,
                'subject' => 'Création de Compte Client',
                'content' => "<p><strong>Bonjour</strong> {{ client_lastname }} {{ client_firstname }},</p><p>Votre compte employé chez TipTop a été créé avec succès.</p><p>Vous pouvez maintenant accéder à votre tableau de bord employé et commencer à utiliser nos outils internes.</p><p>Lors de votre première connexion, veuillez utiliser le mot de passe temporaire suivant :&nbsp; {{ password }}</p><p>Nous vous recommandons de le changer dès votre première connexion pour des raisons de sécurité.</p><p>Si vous avez des questions ou avez besoin d'assistance, n'hésitez pas à nous contacter.</p><p>Cordialement, <strong>L'équipe TipTop</strong></p>",
            ],
            [
                'name' => 'Création de compte Employé',
                'title' => 'Création de Compte Employé',
                'description' => 'Email envoyé pour informer de la création d\'un compte employé.',
                'type' => 'service',
                'service' => array_key_exists(1, $templatesServices) ? $templatesServices[1] : null,
                'subject' => 'Création de Compte Employé',
                'content' => "<p><strong>Bonjour</strong> {{ employee_lastname }} {{ employee_firstname }},</p><p>Votre compte employé chez TipTop a été créé avec succès.</p><p>Vous pouvez maintenant accéder à votre tableau de bord employé et commencer à utiliser nos outils internes.</p><p>Lors de votre première connexion, veuillez utiliser le mot de passe temporaire suivant :&nbsp; {{ password }}</p><p>Nous vous recommandons de le changer dès votre première connexion pour des raisons de sécurité.</p><p>Si vous avez des questions ou avez besoin d'assistance, n'hésitez pas à nous contacter.</p><p>Cordialement, <strong>L'équipe TipTop</strong></p>",
            ],
            [
                'name' => 'Activation de compte Client',
                'title' => 'Activation de Compte Client',
                'description' => 'Email envoyé pour activer le compte client après l\'inscription.',
                'type' => 'service',
                'service' => array_key_exists(2, $templatesServices) ? $templatesServices[2] : null,
                'subject' => 'Activation de Compte Client',
                'content' => '<p>Bonjour {{ client_lastname }} {{ client_firstname }},</p><p>Votre compte chez TipTop a été créé avec succès.</p><p>Pour activer votre compte, veuillez cliquer sur le lien ci-dessous :</p><p>{{ activate_account_link_client }}</p><p>Une fois votre compte activé, vous pourrez profiter pleinement de votre expérience chez TipTop.</p><p>Si vous avez des questions, n\'hésitez pas à nous contacter.</p><p>Bienvenue chez TipTop !</p><p>Cordialement, L\'équipe TipTop</p>'
            ],
            [
                'name' => 'Activation de compte Employé',
                'title' => 'Réinitialisation de Mot de Passe - Client',
                'description' => 'Email envoyé pour réinitialiser le mot de passe du client.',
                'type' => 'service',
                'service' => array_key_exists(3, $templatesServices) ? $templatesServices[3] : null,
                'subject' => 'Réinitialisation de Mot de Passe - Client',
                'content' => '<p>Bonjour {{ client_lastname }} {{ client_firstname }},</p><p>Vous avez demandé la réinitialisation de votre mot de passe chez TipTop.</p><p>Pour réinitialiser votre mot de passe, veuillez cliquer sur le lien ci-dessous :</p><p>{{ password_reset_link_client }}</p><p>Si vous n\'avez pas demandé cette réinitialisation, veuillez ignorer cet email.</p><p>Si vous avez des questions, n\'hésitez pas à nous contacter.</p><p>Cordialement, L\'équipe TipTop</p>'
            ],

            [
                'name' => 'Confirmation de verification de compte Client',
                'label' => 'Confirmation de verification de compte Client',
                'title'=> 'Confirmation de verification de compte Client',
                'description' => 'Email envoyé pour confirmer la vérification du compte client.',
                'type' => 'service',
                'service' => array_key_exists(4, $templatesServices) ? $templatesServices[4] : null,
                'subject' => 'Confirmation de verification de compte Client',
                'content' => '<p>Bonjour {{ client_lastname }} {{ client_firstname }},</p><p>Votre compte chez TipTop a été vérifié avec succès.</p><p>Si vous avez des questions, n\'hésitez pas à nous contacter.</p><p>Bienvenue chez TipTop !</p><p>Cordialement, L\'équipe TipTop</p>'
            ],

            [
                'name' => 'Confirmation de verification de compte Employé',
                'label' => 'Confirmation de verification de compte Employé',
                'title' => "Confirmation de verification de compte Employé",
                'description' => 'Email envoyé pour confirmer la vérification du compte employé.',
                'type' => 'service',
                'service' => array_key_exists(5, $templatesServices) ? $templatesServices[5] : null,
                'subject' => 'Confirmation de verification de compte Employé',
                'content' => '<p>Bonjour {{ employee_lastname }} {{ employee_firstname }},</p><p>Votre compte chez TipTop a été vérifié avec succès.</p><p>Si vous avez des questions, n\'hésitez pas à nous contacter.</p><p>Bienvenue chez TipTop !</p><p>Cordialement, L\'équipe TipTop</p>'
            ],

            [
                'name' => 'Réinitialisation de Mot de Passe - Employé',
                'title' => 'Réinitialisation de Mot de Passe - Employé',
                'description' => 'Email envoyé pour réinitialiser le mot de passe de l\'employé.',
                'type' => 'service',
                'service' => array_key_exists(6, $templatesServices) ? $templatesServices[6] : null,
                'subject' => 'Réinitialisation de Mot de Passe - Employé',
                'content' => '<p>Bonjour {{ employee_lastname }} {{ employee_firstname }},</p><p>Vous avez demandé la réinitialisation de votre mot de passe chez TipTop.</p><p>Pour réinitialiser votre mot de passe, veuillez cliquer sur le lien ci-dessous :</p><p>{{ password_reset_link_employee }}</p><p>Si vous n\'avez pas demandé cette réinitialisation, veuillez ignorer cet email.</p><p>Si vous avez des questions, n\'hésitez pas à nous contacter.</p><p>Cordialement, L\'équipe TipTop</p>'
            ],
            [
                'name' => 'Réinitialisation de Mot de Passe - Client',
                'title' => 'Réinitialisation de Mot de Passe - Client',
                'description' => 'Email envoyé pour réinitialiser le mot de passe de l\'client.',
                'type' => 'service',
                'service' => array_key_exists(7, $templatesServices) ? $templatesServices[7] : null,
                'subject' => 'Réinitialisation de Mot de Passe - CLIENT',
                'content' => '<p>Bonjour {{ employee_lastname }} {{ employee_firstname }},</p><p>Vous avez demandé la réinitialisation de votre mot de passe chez TipTop.</p><p>Pour réinitialiser votre mot de passe, veuillez cliquer sur le lien ci-dessous :</p><p>{{ password_reset_link_employee }}</p><p>Si vous n\'avez pas demandé cette réinitialisation, veuillez ignorer cet email.</p><p>Si vous avez des questions, n\'hésitez pas à nous contacter.</p><p>Cordialement, L\'équipe TipTop</p>'
            ],
            [
                'name' => 'Réinitialisation de Mot de Passe Réussie - Client',
                'title' => 'Réinitialisation de Mot de Passe Réussie - Client',
                'description' => 'Email envoyé pour informer le client que la réinitialisation de mot de passe a réussi.',
                'type' => 'service',
                'service' => array_key_exists(8, $templatesServices) ? $templatesServices[8] : null,
                'subject' => 'Réinitialisation de Mot de Passe Réussie - Client',
                'content' => '<p>Bonjour {{ client_lastname }} {{ client_firstname }},</p><p>Votre mot de passe chez TipTop a été réinitialisé avec succès.</p><p>Si vous n\'avez pas demandé cette réinitialisation, veuillez nous contacter immédiatement.</p><p>Si vous avez des questions, n\'hésitez pas à nous contacter.</p><p>Cordialement, L\'équipe TipTop</p>'
            ],
            [
                'name' => 'Réinitialisation de Mot de Passe Réussie - Employé',
                'title' => 'Réinitialisation de Mot de Passe Réussie - Employé',
                'description' => 'Email envoyé pour informer l\'employé que la réinitialisation de mot de passe a réussi.',
                'type' => 'service',
                'service' => array_key_exists(9, $templatesServices) ? $templatesServices[9] : null,
                'subject' => 'Réinitialisation de Mot de Passe Réussie - Employé',
                'content' => '<p>Bonjour {{ employee_lastname }} {{ employee_firstname }},</p><p>Votre mot de passe chez TipTop a été réinitialisé avec succès.</p><p>Si vous n\'avez pas demandé cette réinitialisation, veuillez nous contacter immédiatement.</p><p>Si vous avez des questions, n\'hésitez pas à nous contacter.</p><p>Cordialement, L\'équipe TipTop</p>'
            ],
            [
                'name' => 'Échec de Réinitialisation de Mot de Passe - Client',
                'title' => 'Échec de Réinitialisation de Mot de Passe - Client',
                'description' => 'Email envoyé pour informer le client que la réinitialisation de mot de passe a échoué.',
                'type' => 'service',
                'service' => array_key_exists(10, $templatesServices) ? $templatesServices[10] : null,
                'subject' => 'Échec de Réinitialisation de Mot de Passe - Client',
                'content' => '<p>Bonjour {{ client_lastname }} {{ client_firstname }},</p><p>La réinitialisation de votre mot de passe chez TipTop a échoué.</p><p>Si vous avez des questions ou avez besoin d\'assistance, veuillez nous contacter immédiatement.</p><p>Cordialement, L\'équipe TipTop</p>'
            ],
            [
                'name' => 'Échec de Réinitialisation de Mot de Passe - Employé',
                'title' => 'Échec de Réinitialisation de Mot de Passe - Employé',
                'subject' => 'Échec de Réinitialisation de Mot de Passe - Employé',
                'description' => 'Email envoyé pour informer l\'employé que la réinitialisation de mot de passe a échoué.',
                'type' => 'service',
                'service' => array_key_exists(11, $templatesServices) ? $templatesServices[11] : null,
                'content' => '<p>Bonjour {{ employee_lastname }} {{ employee_firstname }},</p><p>La réinitialisation de votre mot de passe chez TipTop a échoué.</p><p>Si vous avez des questions ou avez besoin d\'assistance, veuillez nous contacter immédiatement.</p><p>Cordialement, L\'équipe TipTop</p>'
            ],

            [
                'name' => 'Expiration de Réinitialisation de Mot de Passe',
                'title' => 'Expiration de Réinitialisation de Mot de Passe',
                'description' => 'Email envoyé pour informer que le lien de réinitialisation de mot de passe a expiré.',
                'type' => 'service',
                'service' => array_key_exists(12, $templatesServices) ? $templatesServices[12] : null,
                'subject' => 'Expiration de Réinitialisation de Mot de Passe',
                'content' => '<p>Bonjour {{ client_lastname }} {{ client_firstname }},</p><p>Le lien de réinitialisation de votre mot de passe chez TipTop a expiré.</p><p>Si vous avez besoin de réinitialiser votre mot de passe, veuillez en faire la demande à nouveau.</p><p>Si vous avez des questions, n\'hésitez pas à nous contacter.</p><p>Cordialement, L\'équipe TipTop</p>'
            ],
            [
                'name' => 'Participation à la Roue de la Fortune',
                'title' => 'Participation à la Roue de la Fortune',
                'description' => 'Email envoyé pour informer de la participation à la Roue de la Fortune.',
                'type' => 'service',
                'service' => array_key_exists(13, $templatesServices) ? $templatesServices[13] : null,
                'subject' => 'Participation à la Roue de la Fortune',
                'content' => '<p>Bonjour {{ client_lastname }} {{ client_firstname }},</p><p>Merci de participer à notre Roue de la Fortune chez TipTop.</p><p>Vous avez la chance de gagner des récompenses exclusives. Cliquez sur le lien ci-dessous pour participer :</p><p>{{ wheel_of_fortune_link }}</p><p>Si vous avez des questions, n\'hésitez pas à nous contacter.</p><p>Que la chance soit avec vous !</p><p>Cordialement, L\'équipe TipTop</p>'
            ],
            [
                'name' => 'Déclaration de Victoire - Client',
                'title' => 'Déclaration de Victoire - Client',
                'description' => 'Email envoyé pour informer de la victoire à la Roue de la Fortune à un client',
                'type' => 'service',
                'service' => array_key_exists(14, $templatesServices) ? $templatesServices[14] : null,
                'subject' => 'Déclaration de Victoire - Client',
                'content' => '<p>Bonjour {{ client_lastname }} {{ client_firstname }},</p><p>Félicitations ! Vous avez remporté un prix à notre Roue de la Fortune chez TipTop.</p><p>Nous sommes ravis de vous annoncer que vous êtes notre gagnant. Cliquez sur le lien ci-dessous pour réclamer votre récompense :</p><p>{{ wheel_of_fortune_link }}</p><p>Si vous avez des questions, n\'hésitez pas à nous contacter.</p><p>Encore une fois, félicitations !</p><p>Cordialement, L\'équipe TipTop</p>'
            ],

            [
                'name' => 'Déclaration de Victoire - Employé',
                'title' => 'Déclaration de Victoire - Employé',
                'description' => 'Email envoyé pour informer de la victoire à la Roue de la Fortune à un employé du magasin',
                'type' => 'service',
                'service' =>  array_key_exists(15, $templatesServices) ? $templatesServices[15] : null,
                'subject' => 'Déclaration de Victoire - Employé',
                'content' => '<p>Bonjour {{ client_lastname }} {{ client_firstname }},</p><p>Félicitations ! Vous avez remporté un prix à notre Roue de la Fortune chez TipTop.</p><p>Nous sommes ravis de vous annoncer que vous êtes notre gagnant. Cliquez sur le lien ci-dessous pour réclamer votre récompense :</p><p>{{ wheel_of_fortune_link }}</p><p>Si vous avez des questions, n\'hésitez pas à nous contacter.</p><p>Encore une fois, félicitations !</p><p>Cordialement, L\'équipe TipTop</p>'
            ],

            [
                'name' => 'Expiration de Confirmation de Victoire',
                'title' => 'Expiration de Confirmation de Victoire',
                'description' => 'Email envoyé pour informer que le lien de confirmation de victoire a expiré.',
                'type' => 'service',
                'service' => array_key_exists(16, $templatesServices) ? $templatesServices[16] : null,
                'subject' => 'Expiration de Confirmation de Victoire',
                'content' => '<p>Bonjour {{ client_lastname }} {{ client_firstname }},</p><p>Le lien de confirmation de votre victoire chez TipTop a expiré.</p><p>Si vous avez besoin de confirmer votre victoire, veuillez en faire la demande à nouveau.</p><p>Si vous avez des questions, n\'hésitez pas à nous contacter.</p><p>Cordialement, L\'équipe TipTop</p>'
            ]



        ];



        foreach ($templates as $template) {
            $emailTemplate = new EmailTemplate();
            $emailTemplate->setName($template['name']);
            $emailTemplate->setTitle($template['title']);
            $emailTemplate->setDescription($template['description']);
            $emailTemplate->setType($template['type']);
            $emailTemplate->setService($template['service']);
            $emailTemplate->setContent($template['content']);
            $emailTemplate->setSubject($template['subject']);
            $emailTemplate->setRequired(true);

            $this->entityManager->persist($emailTemplate);
        }

        $this->entityManager->flush();



    }


}
