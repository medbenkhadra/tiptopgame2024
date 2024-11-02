<?php

namespace App\Command;

use App\Entity\GameConfig;
use App\Entity\Prize;
use App\Entity\Role;
use App\Entity\Ticket;
use App\Entity\TicketHistory;
use App\Entity\User;
use App\Repository\PrizeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Connection;

class GenerateDefaultTickets extends Command
{

    private EntityManagerInterface $entityManager;
    private PrizeRepository $prizeRepository;

    private Connection $connection;

    public function __construct(EntityManagerInterface $entityManager, PrizeRepository $prizeRepository , Connection $connection)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->prizeRepository = $prizeRepository;
        $this->connection = $connection;
        $this->setName('app:generate-tickets');

    }

    protected function configure(): void
    {
        $this->setDescription('Generate 500,000 tickets with the winning rating of each prize');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->connection->executeQuery('SET SQL_SAFE_UPDATES = 0');
        $this->connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');
        $this->connection->executeQuery('DELETE FROM ticket');
        $this->connection->executeQuery('ALTER TABLE ticket AUTO_INCREMENT = 1');
        $this->connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');
        $this->connection->executeQuery('SET SQL_SAFE_UPDATES = 1');


        $prizes = $this->prizeRepository->findAll();

        $totalWinningRate = array_reduce($prizes, function ($sum, Prize $prize) {
            return $sum + $prize->getWinningRate();
        }, 0);

        $ticketCount = 1000;
        $tickets = [];
        $generatedTicketCodes = [];
        $anonymousRole = $this->entityManager->getRepository(Role::class)->findOneBy(['name' => Role::ROLE_ANONYMOUS]);
        $anonymousUser = $this->entityManager->getRepository(User::class)->findOneBy(['role' => $anonymousRole]);
        $gameConfig = $this->entityManager->getRepository(GameConfig::class)->find(1);
        $gameConfigStartDate = null;
        $dateFormat = 'd/m/Y H:i';
        if($gameConfig){
            $gameConfigStartDate = \DateTime::createFromFormat($dateFormat , $gameConfig->getStartDate()." " . $gameConfig->getTime());
        }

        for ($i = 0; $i < $ticketCount; $i++) {
            $output->writeln('Generating ticket ' . ($i + 1) . ' of ' . $ticketCount);
            //do {
            $randomTicketCode = 'TK' . substr(uniqid(), -8);
            //} while (in_array($randomTicketCode, $generatedTicketCodes));

            //$generatedTicketCodes[] = $randomTicketCode;

            $randomNumber = mt_rand(1, $totalWinningRate);
            $winningPrize = null;

            foreach ($prizes as $prize) {
                $randomNumber -= $prize->getWinningRate();

                if ($randomNumber <= 0) {
                    $winningPrize = $prize;
                    break;
                }
            }



            if ($winningPrize) {
                $ticket = new Ticket();
                $ticket->setPrize($winningPrize);
                $ticket->setTicketCode(strtoupper($randomTicketCode));
                $ticket->setTicketGeneratedAt($gameConfigStartDate ? $gameConfigStartDate : new \DateTime());
                $ticket->setTicketPrintedAt(null);
                $ticket->setWinDate(null);
                $ticket->setStatus(Ticket::STATUS_GENERATED);

                $ticketHistory = new TicketHistory();
                $ticketHistory->setTicket($ticket);
                $ticketHistory->setUser($anonymousUser);
                $ticketHistory->setEmployee(null);
                $ticketHistory->setStatus(Ticket::STATUS_GENERATED);
                $ticketHistory->setUpdatedAt(new \DateTime());
                $this->entityManager->persist($ticket);
                $this->entityManager->persist($ticketHistory);
            }

            $output->writeln('Generated ticket ' . ($i + 1) . ' of ' . $ticketCount);
        }

        $this->entityManager->flush();

        $output->writeln( $ticketCount.' tickets generated with each related prize.');
        return Command::SUCCESS;
    }
}
