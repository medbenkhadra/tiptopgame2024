<?php

namespace App\Tests\Command;

use App\Command\GenerateDefaultTickets;
use App\Entity\GameConfig;
use App\Entity\Prize;
use App\Entity\Role;
use App\Entity\Ticket;
use App\Entity\TicketHistory;
use App\Entity\User;
use App\Repository\GameConfigRepository;
use App\Repository\PrizeRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateDefaultTicketsTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private PrizeRepository $prizeRepository;


    private Connection $connection;

    protected function setUp(): void
    {
        parent::setUp();

        $roleRepository = $this->createMock(RoleRepository::class);
        $role = new Role();
        $roleRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($role);


        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn(new User());


        $gameConfigRepository = $this->createMock(GameConfigRepository::class);
        $gameConfigRepository->expects($this->any())
            ->method('find')
            ->willReturn(new GameConfig());

        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->entityManager->expects($this->any())
            ->method('getRepository')
            ->willReturnMap([
                [Role::class, $roleRepository],
                [User::class, $userRepository],
                [GameConfig::class, $gameConfigRepository]
            ]);




        $this->prizeRepository = $this->createMock(PrizeRepository::class);

        $this->connection = $this->createMock(Connection::class);
    }

    public function testExecute(): void
    {
        $output = $this->createMock(OutputInterface::class);
        $output->expects($this->any())
        ->method('writeln');

        $gameConfig = new GameConfig();
        $gameConfig->setStartDate('01/01/2024');
        $gameConfig->setTime('12:00');

        $prizes = [
            (new Prize())->setWinningRate(60),
            (new Prize())->setWinningRate(20),
            (new Prize())->setWinningRate(10),
            (new Prize())->setWinningRate(6),
            (new Prize())->setWinningRate(4),
        ];

        $this->prizeRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($prizes);

        $command = new GenerateDefaultTickets($this->entityManager, $this->prizeRepository, $this->connection);

        $application = new Application();
        $application->add($command);

        $command = $application->find('app:generate-tickets');

        $commandTester = new CommandTester($command);

        $commandTester->execute([], ['verbosity' => OutputInterface::VERBOSITY_DEBUG]);

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString(' tickets generated with each related prize.', $output);
    }
}
