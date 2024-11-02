<?php

namespace App\Tests\Feature\Controller\Api\Dashboard;

use App\Entity\LoyaltyPoints;
use App\Entity\Prize;
use App\Entity\Role;
use App\Entity\Store;
use App\Entity\Ticket;
use App\Entity\TicketHistory;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DashboardControllerTest extends WebTestCase
{
    private $client;

    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $this->passwordEncoder = $this->client->getContainer()->get(UserPasswordHasherInterface::class);

    }

    public function testGetClientDashboardCounters(): void
    {

        $user = new User();
        $user->setEmail('client@tiptop.com');
        $user->setPassword($this->passwordEncoder->hashPassword($user, 'password'));
        $user->setIsActive(true);
        $user->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_CLIENT']));
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());
        $user->setDateOfBirth(new \DateTime());
        $user->setFirstName('Test');
        $user->setLastName('User');
        $user->setGender('Homme');
        $user->setPhone('123456789');
        $user->setStatus(true);
        $this->entityManager->persist($user);

        $loyaltyPoint = new LoyaltyPoints();
        $loyaltyPoint->setPoints(100);
        $loyaltyPoint->setUser($user);
        $loyaltyPoint->setCreatedAt(new \DateTime());

        $this->entityManager->persist($loyaltyPoint);

        $user->addLoyaltyPoint($loyaltyPoint);
        $loyaltyPoint->setUser($user);

        $this->entityManager->persist($user);
        $this->entityManager->persist($loyaltyPoint);



        $this->generateRandomTickets(10, $user);



        $this->entityManager->flush();

        $this->client->loginUser($user);

        $this->client->request('GET', '/api/client/dashboard/counters');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }

    private function generateRandomTickets(int $int, User $user): void
    {
        $tickets = [];
        $status = [
            Ticket::STATUS_GENERATED,
            Ticket::STATUS_PRINTED,
            Ticket::STATUS_PENDING_VERIFICATION,
            Ticket::STATUS_WINNER,
        ];

        $prizes = $this->entityManager->getRepository(Prize::class)->findAll();

        if(count($prizes) > 0){
            $prize = $prizes[array_rand($prizes)];
        }else {
            $prize = new Prize();
            $prize->setName('Prize');
            $prize->setLabel('Prize');
            $prize->setPrizeValue('Prize');
            $prize->setType('Prize');
            $prize->setWinningRate('20');
            $this->entityManager->persist($prize);
        }

        for ($i = 0; $i < $int; $i++) {
            $randomStatus = $status[array_rand($status)];
            $ticket = new Ticket();
            $ticket->setTicketCode('TICKETCODE' . $i. rand(1, 1000));
            $winDate = new \DateTime();
            $ticket->setWinDate($winDate);
            $ticket->setStatus($randomStatus);
            $ticket->setTicketGeneratedAt(new \DateTime());
            $ticket->setTicketPrintedAt(new \DateTime());
            $ticket->setTicketGeneratedAt(new \DateTime());
            $ticket->setUpdatedAt(new \DateTime());
            $ticket->setUser($user);
            $user->addTicket($ticket);


            $store = $user->getStores()[0];
            if($store){
                $ticket->setStore($store);
                $store->addTicket($ticket);
            }

            $ticket->setPrize($prize);
            $this->entityManager->persist($user);
            $this->entityManager->persist($ticket);

            $ticketHistory = new TicketHistory();
            $ticket->addTicketHistory($ticketHistory);
            $ticketHistory->setTicket($ticket);
            $ticketHistory->setUser($user);
            $ticketHistory->setEmployee(null);
            $ticketHistory->setStatus($randomStatus);
            $ticketHistory->setUpdatedAt(new \DateTime());
            $this->entityManager->persist($ticket);
            $this->entityManager->persist($ticketHistory);
        }
    }

    public function testGetAdminDashboardCounters(): void
    {

        $admin = new User();
        $admin->setEmail('admin@tiptop.com');
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'password'));
        $admin->setIsActive(true);
        $admin->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_STOREMANAGER']));
        $admin->setCreatedAt(new \DateTime());
        $admin->setUpdatedAt(new \DateTime());
        $admin->setDateOfBirth(new \DateTime());
        $admin->setFirstName('Test');
        $admin->setLastName('User');
        $admin->setGender('Homme');
        $admin->setPhone('123456789');
        $admin->setStatus(true);
        $this->entityManager->persist($admin);

        $this->entityManager->flush();

        $store = new Store();
        $store->setName('Store');
        $store->setAddress('Address');
        $store->setHeadquartersAddress('Headquarters Address');
        $store->setEmail('store@tiptop.com');
        $store->setPostalCode('12345');
        $store->setCity('City');
        $store->setCountry('Country');
        $store->setCapital(1000);
        $store->setStatus(true);
        $store->setSiren('123456789');

        $store->addUser($admin);
        $admin->addStore($store);

        $this->generateRandomTickets(10, $admin);

        $this->entityManager->persist($store);
        $this->entityManager->persist($admin);

        $this->entityManager->flush();


        $this->client->loginUser($admin);


        $this->client->request('POST', '/api/admin/dashboard/counters', [], [], [], json_encode([
            'startDate' => '01/01/2024',
            'endDate' => '31/12/2024',
            'storeId' => $store->getId()
        ]));


        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }

    public function testGetDashboardStats(): void
    {

        $admin = new User();
        $admin->setEmail('admin@tiptop.com');
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'password'));
        $admin->setIsActive(true);
        $admin->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_STOREMANAGER']));
        $admin->setCreatedAt(new \DateTime());
        $admin->setUpdatedAt(new \DateTime());
        $admin->setDateOfBirth(new \DateTime());
        $admin->setFirstName('Test');
        $admin->setLastName('User');
        $admin->setGender('Homme');
        $admin->setPhone('123456789');
        $admin->setStatus(true);
        $this->entityManager->persist($admin);


        $store = new Store();
        $store->setName('Store');
        $store->setAddress('Address');
        $store->setHeadquartersAddress('Headquarters Address');
        $store->setEmail('store@email.com');
        $store->setPostalCode('12345');
        $store->setCity('City');
        $store->setCountry('Country');
        $store->setCapital(1000);
        $store->setStatus(true);
        $store->setSiren('123456789');

        $store->addUser($admin);
        $admin->addStore($store);

        $this->entityManager->persist($store);


        $client = new User();
        $client->setEmail('client@tiptop');
        $client->setPassword($this->passwordEncoder->hashPassword($client, 'password'));
        $client->setIsActive(true);
        $client->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_CLIENT']));
        $client->setCreatedAt(new \DateTime());
        $client->setUpdatedAt(new \DateTime());
        $client->setDateOfBirth(new \DateTime());
        $client->setFirstName('Test');
        $client->setLastName('User');
        $client->setGender('Homme');
        $client->setPhone('123456789');
        $client->setStatus(true);
        $this->entityManager->persist($client);

        $store->addUser($client);
        $client->addStore($store);

        $this->entityManager->persist($client);

        $this->generateRandomTickets(50, $client);


        $this->entityManager->flush();

        $this->client->loginUser($admin);

        $this->client->request('GET', '/api/dashboard/stats' , [
            'startDate' => '01/01/2024',
            'endDate' => '31/12/2024',
            'storeId' => 1
        ]);

        $this->assertNotNull(json_decode($this->client->getResponse()->getContent(), true));
        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertArrayHasKey('stats', json_decode($this->client->getResponse()->getContent(), true));
        $this->assertArrayHasKey('totalGainAmount', json_decode($this->client->getResponse()->getContent(), true));
        $this->assertArrayHasKey('gameCount', json_decode($this->client->getResponse()->getContent(), true));
        $this->assertArrayHasKey('topGain', json_decode($this->client->getResponse()->getContent(), true));





    }

}
