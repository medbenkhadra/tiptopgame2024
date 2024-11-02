<?php
// src/Command/ResetGame.php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\Process\Process;

class ResetGame extends Command
{
    private EntityManagerInterface $entityManager;

    private Connection $connection;


    public function __construct(EntityManagerInterface $entityManager , Connection $connection )
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->connection = $connection;
        $this->setName('app:reset-game');

    }

    protected function configure(): void
    {
        $this->setDescription('Generate badges for clients');

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $tables=['ticket_history','user_badge','store_user' ,'user_store','user_personal_info','user','store',
            'loyalty_points' ,'connection_history' , 'emailing_history' , 'action_history' ,'avatar' ];
        $this->purgeTables($tables , $output);




        $output->writeln('Next  Generate Role...');
        $process = new Process(['php', 'bin/console', 'app:create-default-role']);
        $process->mustRun();
        $output->writeln('Default roles created successfully. 1/10');
        $output->writeln('Loading...');

        $output->writeln('Next  Generate Company and admin profile...');
        $process = new Process(['php', 'bin/console', 'app:create-default-tiptop-company']);
        $process->mustRun();
        $output->writeln('Default company created successfully. 2/10');
        $output->writeln('Loading...');

        $output->writeln('Next  Generate Prizes...');
        $process = new Process(['php', 'bin/console', 'app:add-prizes']);
        $process->mustRun();
        $output->writeln('Prizes created successfully. 3/10');
        $output->writeln('Loading...');

        $output->writeln('Next  Generate Game Config...');
        $process = new Process(['php', 'bin/console', 'app:game-config-init']);
        $process->mustRun();
        $output->writeln('Badges generated successfully. 4/10');
        $output->writeln('Loading...');

        $output->writeln('Next  Generate Badges...');
        $process = new Process(['php', 'bin/console', 'app:generate-badges']);
        $process->mustRun();
        $output->writeln('Badges generated successfully. 5/10');
        $output->writeln('Loading...');

        $output->writeln('Next  Generate Tickets...');
        $process = new Process(['php', '-d', 'memory_limit=-1', 'bin/console', 'app:generate-tickets']);
        $process->setTimeout(null);
        $process->mustRun();
        $output->writeln('Tickets generated successfully. 6/10');
        $output->writeln('Loading...');

        $output->writeln('Next  Generate Email Services...');
        $process = new Process(['php', 'bin/console', 'app:generate-email-services']);
        $process->mustRun();
        $output->writeln('Email Services generated successfully. 7/10');
        $output->writeln('Loading...');

        $output->writeln('Next  Generate Email Templates Variables...');
        $process = new Process(['php', 'bin/console', 'app:generate-email-templates-variables']);
        $process->mustRun();
        $output->writeln('Email Templates Variables generated successfully. 8/10');
        $output->writeln('Loading...');

        $output->writeln('Next  Generate Email Templates...');
        $process = new Process(['php', 'bin/console', 'app:generate-email-templates']);
        $process->mustRun();
        $output->writeln('Email Templates generated successfully. 9/10');
        $output->writeln('Loading...');

        $output->writeln('Next  Generate Fake Data...');
        $process = new Process(['php', 'bin/console', 'app:generate-data']);
        $process->setTimeout(null);
        $process->mustRun();
        $output->writeln('Data generated successfully. 10/10');
        $output->writeln('100% Complete');

        $output->writeln('Game reset successfully.');

        return Command::SUCCESS;
    }

    private function purgeTables(array $tables , $output): void
    {

        $this->connection->executeQuery('SET SQL_SAFE_UPDATES = 0');
        $this->connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');
        foreach ($tables as $table) {
            $output->writeln('Purging table '.$table);
            $this->connection->executeQuery('DELETE FROM '.$table);
            $this->connection->executeQuery('ALTER TABLE '.$table.' AUTO_INCREMENT = 1');
        }
        $this->connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');
        $this->connection->executeQuery('SET SQL_SAFE_UPDATES = 1');

    }

}
