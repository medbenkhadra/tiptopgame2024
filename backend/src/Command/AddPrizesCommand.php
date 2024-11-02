<?php

namespace App\Command;

use App\Entity\Prize;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Connection;

class AddPrizesCommand extends Command
{

    private EntityManagerInterface $entityManager;

    private $connection;
    public function __construct(EntityManagerInterface $entityManager , Connection $connection)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->connection = $connection;
        $this->setName('app:add-prizes');
    }

    protected function configure() : void
    {
        $this->setDescription('Add prizes to the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {


        $this->connection->executeQuery('SET SQL_SAFE_UPDATES = 0');
        $this->connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');
        $this->connection->executeQuery('DELETE FROM prize');
        $this->connection->executeQuery('ALTER TABLE prize AUTO_INCREMENT = 1');
        $this->connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');
        $this->connection->executeQuery('SET SQL_SAFE_UPDATES = 1');



        $prizesData = [
            ['un infuseur à thé', 'Infuser', 'Infuser', 'Infuser', 60.0 , 14.99],
            ['Une boite de 100g d’un thé détox ou d’infusion', 'Tea Box (100g)', 'physical', 'Tea Box (100g)', 20.0 ,24.99],
            ['Une boite de 100g d’un thé signature', 'Signature Tea Box (100g)', 'physical', 'Tea Box (100g)', 10.0 , 29.99],
            ['un coffret découverte à 39€', 'Discovery box (Value: 39€)', 'physical', '39€', 6.0 , 39.00],
            ['un coffret découverte à 69€', 'Discovery box (Value: 69€)', 'physical', '69€', 4.0 , 69.00],
        ];

        foreach ($prizesData as $prizeData) {
            $prize = new Prize();

            $prize->setLabel($prizeData[0]);
            $prize->setName($prizeData[1]);
            $prize->setType($prizeData[2]);
            $prize->setPrizeValue($prizeData[3]);
            $prize->setWinningRate($prizeData[4]);
            $prize->setPrice($prizeData[5]);
            $this->entityManager->persist($prize);
        }

        $this->entityManager->flush();

        $output->writeln('Prizes added to the database.');

        return Command::SUCCESS;
    }
}
