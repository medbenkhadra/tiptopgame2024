<?php

namespace App\Command;

use App\Entity\GameConfig;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class initializeGameConfigCommand extends Command
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->setName('app:game-config-init');
    }

    protected function configure(): void
    {
        $this->setDescription('Initialize game configuration Date');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $existingGameConfig = $this->entityManager->getRepository(GameConfig::class)->findAll();


        if(empty($existingGameConfig)){
            $gameConfig = new GameConfig();

            $randomFutureDate = new \DateTime();
            $randomFutureDate->modify('+'.rand(0, 5).' days');
            $dateFormatted = $randomFutureDate->format('d/m/Y');
            $gameConfig->setStartDate($dateFormatted);
            $gameConfig->setTime("09:00");
            $this->entityManager->persist($gameConfig);
            $this->entityManager->flush();
            $output->writeln('Game configuration initialized successfully !');
         }else{
            $output->writeln('Game configuration already initialized !!!!');
         }


        return Command::SUCCESS;
    }

   

   


}
