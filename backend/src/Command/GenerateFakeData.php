<?php

namespace App\Command;

use App\Entity\GameConfig;
use App\Entity\Role;
use App\Entity\Store;
use App\Entity\Ticket;
use App\Entity\TicketHistory;
use App\Entity\User;
use App\Entity\UserPersonalInfo;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class GenerateFakeData extends Command
{

    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->setName('app:generate-data');
    }

    protected function configure():void
    {
        $this->setDescription('Generate fake data for stores, tickets, user, (managers, employees, clients) table.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $faker = Factory::create('fr_FR');

        $this->generateFakeStores($output, $faker);
        $this->generateFakeUsers($output, $faker, 'storemanager', Role::ROLE_STOREMANAGER, 5);
        $this->generateFakeUsers($output, $faker, 'employee', Role::ROLE_EMPLOYEE, 15);
        $this->generateFakeUsers($output, $faker, 'client', Role::ROLE_CLIENT, 30);

        $randomTickets = $this->getRandomTickets();
        $this->generateFakeGains($output, $faker, $randomTickets);

        $output->writeln('Fake data generated.');
        return Command::SUCCESS;
    }

    public function generateFakeStores(OutputInterface $output, $faker): void
    {
        $storeCount = 5;

        $cities = ["Paris", "Lyon", "Marseille", "Madrid", "Nice"];

        for ($i = 0; $i < $storeCount; $i++) {
            $store = new Store();
            $store->setName($faker->company);
            $store->setAddress($faker->streetAddress);
            $store->setHeadquartersAddress($faker->address);
            $store->setEmail($faker->email);
            $store->setPostalCode($faker->postcode);
            $store->setCity($cities[array_rand($cities)]);
            $store->setCountry($faker->country);
            $store->setCapital($faker->randomFloat(2, 1000, 1000000));
            $store->setStatus($faker->randomElement([1, 2]));
            $store->setOpeningDate($faker->dateTimeThisDecade);
            $store->setPhone($faker->phoneNumber);
            $store->setSiren($faker->randomNumber(9, true));

            $this->entityManager->persist($store);
        }

        $output->writeln('5 stores added to the store table.');
        $this->entityManager->flush();
    }

    public function generateFakeUsers(OutputInterface $output, $faker, $type, $role, $count): void
    {
        $roleEntity = $this->entityManager->getRepository(Role::class)->findOneBy(['name' => $role]);

        if (!$roleEntity) {
            $output->writeln("Error: $role role not found.");
            return;
        }

        $allStores = $this->entityManager->getRepository(Store::class)->findAll();

        if (empty($allStores)) {
            $output->writeln('Error: No stores found in the database.');
            return;
        }

        for ($i = 0; $i < $count; $i++) {
            $email ="";

            if($role == Role::ROLE_CLIENT){
                   if($i === 0) {
                       $email = "client@dsp5-archi-f23-15m-g7.ovh";
                   }else{
                       $email = "client".($i+1)."@dsp5-archi-f23-15m-g7.ovh";
                   }
            }
            else if ($role == Role::ROLE_EMPLOYEE){
                    $email = "employee".($i+1)."@dsp5-archi-f23-15m-g7.ovh";
            }
            else if ($role == Role::ROLE_STOREMANAGER){
                    $email = "manager".($i+1)."@dsp5-archi-f23-15m-g7.ovh";
            }

            $store = $allStores[array_rand($allStores)];
            $user = new User();
            $user->setEmail($email);
            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setGender($faker->randomElement(['Homme', 'Femme']));
            $dob = $faker->dateTimeBetween('-100 years', '-16 years');
            $user->setDateOfBirth($dob);
            $user->setPhone($faker->phoneNumber);
            $plainPassword = 'Mohamed6759F@';
            $hashedPassword = $this->passwordEncoder->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
            $user->setRole($roleEntity);
            $user->addStore($store);
            $store->addUser($user);
            $user->setStatus(User::STATUS_OPEN);
            $user->setCreatedAt(new DateTime());
            $user->setIsActive($faker->boolean(70));
            $user->setActivitedAt(new DateTime());

            $userPersonalInfo = new UserPersonalInfo();
            $userPersonalInfo->setUser($user);
            $userPersonalInfo->setAddress($faker->streetAddress);
            $userPersonalInfo->setPostalCode($faker->postcode);
            $userPersonalInfo->setCity($faker->city);
            $userPersonalInfo->setCountry('France');

            $this->entityManager->persist($user);
            $this->entityManager->persist($userPersonalInfo);
        }

        $this->entityManager->flush();

        $output->writeln("$count $type added to the user table. (relationship too)");
    }

    public function getRandomTickets(): array
    {
        $ticketRepository = $this->entityManager->getRepository(Ticket::class);

        $qb = $ticketRepository->createQueryBuilder('t')
            ->select('COUNT(t.id)');

        if($qb === null) {
            return [];
        }

        $totalTickets = $qb
            ->where('t.status = 1')
            ->getQuery()
            ->getSingleScalarResult();

        $offset = mt_rand(0, max(0, $totalTickets - 400));

        return $ticketRepository->createQueryBuilder('t')
            ->setFirstResult($offset)
            ->setMaxResults(400)
            ->getQuery()
            ->getResult();
    }

    public function generateFakeGains(OutputInterface $output, $faker, array $randomTickets): void
    {
        $clientRole = $this->entityManager->getRepository(Role::class)->findOneBy(['name' => Role::ROLE_CLIENT]);
        $employeeRole = $this->entityManager->getRepository(Role::class)->findOneBy(['name' => Role::ROLE_EMPLOYEE]);
        $gameConfig = $this->entityManager->getRepository(GameConfig::class)->find(1);
        $gameConfigStartDate = null;
        $dateFormat = 'd/m/Y H:i';
        if ($gameConfig) {
            $gameConfigStartDate = DateTime::createFromFormat($dateFormat, $gameConfig->getStartDate() . " " . $gameConfig->getTime());
        }

        if (!$clientRole || !$employeeRole) {
            $output->writeln('Error: CLIENT or EMPLOYEE role not found.');
            return;
        }

        $clients = $this->getRandomUsersByRole($clientRole);
        $anonymousRole = $this->entityManager->getRepository(Role::class)->findOneBy(['name' => Role::ROLE_ANONYMOUS]);
        $anonymousUser = $this->entityManager->getRepository(User::class)->findOneBy(['role' => $anonymousRole]);

        $employees = $this->getRandomUsersByRole($employeeRole);

        $statuses = [Ticket::STATUS_PRINTED, Ticket::STATUS_PENDING_VERIFICATION, Ticket::STATUS_WINNER];

        foreach ($randomTickets as $ticket) {
            $randomDate = clone $gameConfigStartDate;
            $client = $clients[array_rand($clients)];
            $employee = $employees[array_rand($employees)];

            $clientAux = $clients[array_rand($clients)];

            foreach ($statuses as $randomStatus) {
                $randomDate->modify(rand(1, 24) . ' hours');
                $randomDate->modify(rand(1, 60) . ' minutes');
                $randomDate->modify(rand(1, 60) . ' seconds');

                if ($randomStatus === Ticket::STATUS_PENDING_VERIFICATION) {
                    $randomDate->modify(rand(1, 7) . ' days');
                }

                if ($randomStatus === Ticket::STATUS_WINNER) {
                    $randomDate->modify(rand(7, 60) . ' days');
                }

                if ($randomStatus === Ticket::STATUS_PRINTED) {
                    $client = $anonymousUser;
                } else {
                    $client = $clientAux;
                }

                $this->updateTicket($ticket, $client, $employee, $randomStatus, clone $randomDate);
                $this->createTicketHistory($ticket, clone $randomDate, $randomStatus, $employee, $client);
            }
        }

        $this->entityManager->flush();

        $output->writeln('Fake gains generated and linked to clients and employees.');
    }

    public function createTicketHistory(Ticket $ticket, DateTime $randomDate, string $randomStatus, User $employee, User $client): void
    {
        $ticketHistory = new TicketHistory();
        $ticketHistory->setTicket($ticket);
        $ticketHistory->setUser($client);
        $ticketHistory->setEmployee($employee);
        $ticketHistory->setStatus($randomStatus);
        $ticketHistory->setUpdatedAt($randomDate);

        $this->entityManager->persist($ticketHistory);
    }

    public function updateTicket(Ticket $ticket, User $client, User $employee, int $randomStatus, DateTime $randomDate): void
    {
        $ticket->setStatus($randomStatus);

        if ($randomStatus === Ticket::STATUS_WINNER) {
            $ticket->setWinDate($randomDate);
        }

        if ($randomStatus === Ticket::STATUS_PRINTED) {
            $ticket->setTicketPrintedAt($randomDate);
        }

        $ticket->setUpdatedAt($randomDate);
        $ticket->setEmployee($employee);
        $ticket->setUser($client);
        $ticket->setStore($employee->getStores()[0]);

        $employee->addTicket($ticket);
        $client->addTicket($ticket);
        $client->addStore($employee->getStores()[0]);
        $store = $employee->getStores()[0];

        $store->addUser($client);

        $this->entityManager->persist($store);
        $this->entityManager->persist($ticket);
        $this->entityManager->persist($client);
    }

    public function getRandomUsersByRole(Role $role): array
    {
        $users = $this->entityManager->getRepository(User::class)->findBy(['role' => $role]);

        $defaultUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'client@dsp5-archi-f23-15m-g7.ovh']);

        if ($defaultUser && !in_array($defaultUser, $users) && $role->getName() === Role::ROLE_CLIENT) {
            $users[] = $defaultUser;
        }
        
        if (count($users) < 20) {
            return $users;
        }



        shuffle($users);
        $users = array_slice($users, 0, 20);

        if ($defaultUser && !in_array($defaultUser, $users) && $role->getName() === Role::ROLE_CLIENT) {
            $users[] = $defaultUser;
        }

        return $users;
    }
}
