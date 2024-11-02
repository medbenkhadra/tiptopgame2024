<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241014194142 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE action_history (id INT AUTO_INCREMENT NOT NULL, user_done_action_id INT NOT NULL, user_action_related_to_id INT DEFAULT NULL, store_id INT DEFAULT NULL, action_type VARCHAR(255) NOT NULL, details LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_FD18F8AAD50815F5 (user_done_action_id), INDEX IDX_FD18F8AAC5672D2D (user_action_related_to_id), INDEX IDX_FD18F8AAB092A811 (store_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE avatar (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, filename VARCHAR(100) NOT NULL, path VARCHAR(200) NOT NULL, UNIQUE INDEX UNIQ_1677722FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE badge (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_final_draw (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_2384160FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE connection_history (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, login_time DATETIME NOT NULL, logout_time DATETIME DEFAULT NULL, is_active TINYINT(1) NOT NULL, duration VARCHAR(50) DEFAULT NULL, INDEX IDX_5CB09668A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email_service (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, label VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email_template (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, title VARCHAR(255) NOT NULL, subject VARCHAR(255) DEFAULT NULL, content LONGTEXT DEFAULT NULL, type VARCHAR(20) DEFAULT NULL, required TINYINT(1) DEFAULT NULL, description LONGTEXT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, INDEX IDX_9C0600CAED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email_template_variable (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email_template_variable_email_service (email_template_variable_id INT NOT NULL, email_service_id INT NOT NULL, INDEX IDX_FA9EBF8736923D6C (email_template_variable_id), INDEX IDX_FA9EBF8741EF5B39 (email_service_id), PRIMARY KEY(email_template_variable_id, email_service_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE emailing_history (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, receiver_id INT DEFAULT NULL, sent_at DATETIME NOT NULL, INDEX IDX_4E1A8D07ED5CA9E6 (service_id), INDEX IDX_4E1A8D07CD53EDB6 (receiver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_config (id INT AUTO_INCREMENT NOT NULL, start_date VARCHAR(100) NOT NULL, time VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE loyalty_points (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, points INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_E0C7D07DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prize (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(150) NOT NULL, name VARCHAR(100) NOT NULL, type VARCHAR(100) NOT NULL, prize_value VARCHAR(150) NOT NULL, winning_rate DOUBLE PRECISION NOT NULL, price NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE social_media_account (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, google_id VARCHAR(100) DEFAULT NULL, facebook_id VARCHAR(100) DEFAULT NULL, UNIQUE INDEX UNIQ_AA5B5E79A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE store (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, headquarters_address VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, postal_code VARCHAR(255) NOT NULL, city VARCHAR(100) NOT NULL, country VARCHAR(100) NOT NULL, capital NUMERIC(10, 2) NOT NULL, status INT NOT NULL, opening_date DATE DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, siren VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE store_user (store_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_6F2A7887B092A811 (store_id), INDEX IDX_6F2A7887A76ED395 (user_id), PRIMARY KEY(store_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, prize_id INT DEFAULT NULL, store_id INT DEFAULT NULL, employee_id INT DEFAULT NULL, ticket_code VARCHAR(100) NOT NULL, win_date DATETIME DEFAULT NULL, status INT NOT NULL, ticket_printed_at DATETIME DEFAULT NULL, ticket_generated_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_97A0ADA3A76ED395 (user_id), INDEX IDX_97A0ADA3BBE43214 (prize_id), INDEX IDX_97A0ADA3B092A811 (store_id), INDEX IDX_97A0ADA38C03F15C (employee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket_history (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, employee_id INT DEFAULT NULL, ticket_id INT NOT NULL, status VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_2B762919A76ED395 (user_id), INDEX IDX_2B7629198C03F15C (employee_id), INDEX IDX_2B762919700047D2 (ticket_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, role_id INT NOT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, gender VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, date_of_birth DATE NOT NULL, password VARCHAR(255) NOT NULL, api_token VARCHAR(255) DEFAULT NULL, api_token_created_at DATETIME DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, status INT NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, activited_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, token_expired_at DATETIME DEFAULT NULL, INDEX IDX_8D93D649D60322AC (role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_store (user_id INT NOT NULL, store_id INT NOT NULL, INDEX IDX_1D95A32FA76ED395 (user_id), INDEX IDX_1D95A32FB092A811 (store_id), PRIMARY KEY(user_id, store_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_badge (user_id INT NOT NULL, badge_id INT NOT NULL, INDEX IDX_1C32B345A76ED395 (user_id), INDEX IDX_1C32B345F7A2C2FC (badge_id), PRIMARY KEY(user_id, badge_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_personal_info (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(20) DEFAULT NULL, city VARCHAR(50) DEFAULT NULL, country VARCHAR(50) DEFAULT NULL, UNIQUE INDEX UNIQ_140D9B3AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE action_history ADD CONSTRAINT FK_FD18F8AAD50815F5 FOREIGN KEY (user_done_action_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE action_history ADD CONSTRAINT FK_FD18F8AAC5672D2D FOREIGN KEY (user_action_related_to_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE action_history ADD CONSTRAINT FK_FD18F8AAB092A811 FOREIGN KEY (store_id) REFERENCES store (id)');
        $this->addSql('ALTER TABLE avatar ADD CONSTRAINT FK_1677722FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE client_final_draw ADD CONSTRAINT FK_2384160FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE connection_history ADD CONSTRAINT FK_5CB09668A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE email_template ADD CONSTRAINT FK_9C0600CAED5CA9E6 FOREIGN KEY (service_id) REFERENCES email_service (id)');
        $this->addSql('ALTER TABLE email_template_variable_email_service ADD CONSTRAINT FK_FA9EBF8736923D6C FOREIGN KEY (email_template_variable_id) REFERENCES email_template_variable (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE email_template_variable_email_service ADD CONSTRAINT FK_FA9EBF8741EF5B39 FOREIGN KEY (email_service_id) REFERENCES email_service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE emailing_history ADD CONSTRAINT FK_4E1A8D07ED5CA9E6 FOREIGN KEY (service_id) REFERENCES email_service (id)');
        $this->addSql('ALTER TABLE emailing_history ADD CONSTRAINT FK_4E1A8D07CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE loyalty_points ADD CONSTRAINT FK_E0C7D07DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE social_media_account ADD CONSTRAINT FK_AA5B5E79A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE store_user ADD CONSTRAINT FK_6F2A7887B092A811 FOREIGN KEY (store_id) REFERENCES store (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE store_user ADD CONSTRAINT FK_6F2A7887A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3BBE43214 FOREIGN KEY (prize_id) REFERENCES prize (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3B092A811 FOREIGN KEY (store_id) REFERENCES store (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA38C03F15C FOREIGN KEY (employee_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ticket_history ADD CONSTRAINT FK_2B762919A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ticket_history ADD CONSTRAINT FK_2B7629198C03F15C FOREIGN KEY (employee_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ticket_history ADD CONSTRAINT FK_2B762919700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('ALTER TABLE user_store ADD CONSTRAINT FK_1D95A32FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_store ADD CONSTRAINT FK_1D95A32FB092A811 FOREIGN KEY (store_id) REFERENCES store (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_badge ADD CONSTRAINT FK_1C32B345A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_badge ADD CONSTRAINT FK_1C32B345F7A2C2FC FOREIGN KEY (badge_id) REFERENCES badge (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_personal_info ADD CONSTRAINT FK_140D9B3AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action_history DROP FOREIGN KEY FK_FD18F8AAD50815F5');
        $this->addSql('ALTER TABLE action_history DROP FOREIGN KEY FK_FD18F8AAC5672D2D');
        $this->addSql('ALTER TABLE action_history DROP FOREIGN KEY FK_FD18F8AAB092A811');
        $this->addSql('ALTER TABLE avatar DROP FOREIGN KEY FK_1677722FA76ED395');
        $this->addSql('ALTER TABLE client_final_draw DROP FOREIGN KEY FK_2384160FA76ED395');
        $this->addSql('ALTER TABLE connection_history DROP FOREIGN KEY FK_5CB09668A76ED395');
        $this->addSql('ALTER TABLE email_template DROP FOREIGN KEY FK_9C0600CAED5CA9E6');
        $this->addSql('ALTER TABLE email_template_variable_email_service DROP FOREIGN KEY FK_FA9EBF8736923D6C');
        $this->addSql('ALTER TABLE email_template_variable_email_service DROP FOREIGN KEY FK_FA9EBF8741EF5B39');
        $this->addSql('ALTER TABLE emailing_history DROP FOREIGN KEY FK_4E1A8D07ED5CA9E6');
        $this->addSql('ALTER TABLE emailing_history DROP FOREIGN KEY FK_4E1A8D07CD53EDB6');
        $this->addSql('ALTER TABLE loyalty_points DROP FOREIGN KEY FK_E0C7D07DA76ED395');
        $this->addSql('ALTER TABLE social_media_account DROP FOREIGN KEY FK_AA5B5E79A76ED395');
        $this->addSql('ALTER TABLE store_user DROP FOREIGN KEY FK_6F2A7887B092A811');
        $this->addSql('ALTER TABLE store_user DROP FOREIGN KEY FK_6F2A7887A76ED395');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3A76ED395');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3BBE43214');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3B092A811');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA38C03F15C');
        $this->addSql('ALTER TABLE ticket_history DROP FOREIGN KEY FK_2B762919A76ED395');
        $this->addSql('ALTER TABLE ticket_history DROP FOREIGN KEY FK_2B7629198C03F15C');
        $this->addSql('ALTER TABLE ticket_history DROP FOREIGN KEY FK_2B762919700047D2');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D60322AC');
        $this->addSql('ALTER TABLE user_store DROP FOREIGN KEY FK_1D95A32FA76ED395');
        $this->addSql('ALTER TABLE user_store DROP FOREIGN KEY FK_1D95A32FB092A811');
        $this->addSql('ALTER TABLE user_badge DROP FOREIGN KEY FK_1C32B345A76ED395');
        $this->addSql('ALTER TABLE user_badge DROP FOREIGN KEY FK_1C32B345F7A2C2FC');
        $this->addSql('ALTER TABLE user_personal_info DROP FOREIGN KEY FK_140D9B3AA76ED395');
        $this->addSql('DROP TABLE action_history');
        $this->addSql('DROP TABLE avatar');
        $this->addSql('DROP TABLE badge');
        $this->addSql('DROP TABLE client_final_draw');
        $this->addSql('DROP TABLE connection_history');
        $this->addSql('DROP TABLE email_service');
        $this->addSql('DROP TABLE email_template');
        $this->addSql('DROP TABLE email_template_variable');
        $this->addSql('DROP TABLE email_template_variable_email_service');
        $this->addSql('DROP TABLE emailing_history');
        $this->addSql('DROP TABLE game_config');
        $this->addSql('DROP TABLE loyalty_points');
        $this->addSql('DROP TABLE prize');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE social_media_account');
        $this->addSql('DROP TABLE store');
        $this->addSql('DROP TABLE store_user');
        $this->addSql('DROP TABLE ticket');
        $this->addSql('DROP TABLE ticket_history');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_store');
        $this->addSql('DROP TABLE user_badge');
        $this->addSql('DROP TABLE user_personal_info');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
