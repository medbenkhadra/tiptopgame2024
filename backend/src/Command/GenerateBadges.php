<?php
// src/Command/AddCompanyCommand.php

namespace App\Command;

use App\Entity\Badge;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Connection;

class GenerateBadges extends Command
{

    private EntityManagerInterface $entityManager;

    private Connection $connection;


    public function __construct(EntityManagerInterface $entityManager , Connection $connection )
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->connection = $connection;
        $this->setName('app:generate-badges');

    }

    protected function configure():void
    {
        $this->setDescription('Generate badges for clients');

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $this->connection->executeQuery('SET SQL_SAFE_UPDATES = 0');
        $this->connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');
        $this->connection->executeQuery('DELETE FROM badge');
        $this->connection->executeQuery('ALTER TABLE badge AUTO_INCREMENT = 1');
        $this->connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');
        $this->connection->executeQuery('SET SQL_SAFE_UPDATES = 1');
        $this->generateClientBadges();

        $output->writeln('Badges generated successfully !');

        return Command::SUCCESS;
    }

    public function generateClientBadges()
    {
        $badgeData = [
            ['name' => 'Explorateur des Saveurs', 'description' => 'Explorez différentes saveurs de thé et obtenez ce badge.'],
            ['name' => 'Maître Infuseur', 'description' => 'Maîtrisez l\'art de l\'infusion et obtenez ce badge expert.'],
            ['name' => 'Collectionneur de Thé', 'description' => 'Élargissez votre collection de thés et de tisanes pour remporter ce badge.'],
            ['name' => 'Gourmet du Thé', 'description' => 'Savourez des thés accompagnés de délicieuses pâtisseries et obtenez ce badge.'],
            ['name' => 'Grand Maître du Thé', 'description' => 'Atteignez le plus haut niveau de maîtrise du thé et obtenez ce prestigieux badge.'],
        ];

        foreach ($badgeData as $badgeInfo) {
            $badge = new Badge();
            $badge->setName($badgeInfo['name']);
            $badge->setDescription($badgeInfo['description']);

            $this->entityManager->persist($badge);
        }

        $this->entityManager->flush();
    }
}
