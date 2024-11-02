<?php

namespace App\Tests\Feature\Controller\Api\Ticket;

use App\Entity\Badge;
use App\Entity\Prize;
use App\Entity\Role;
use App\Entity\Store;
use App\Entity\Ticket;
use App\Entity\TicketHistory;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TicketControllerTest extends WebTestCase
{
    private $client;

    private $entityManager;

    private $passwordEncoder;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $this->passwordEncoder = $this->client->getContainer()->get(UserPasswordHasherInterface::class);
    }

    private function generateUniqueEmail(): string
    {
        return 'email_' . rand(0, 100000) . '@tiptop.com';
    }


    public function testGetTicketByCode(): void
    {
        $randomTicket = $this->entityManager->getRepository(Ticket::class)->findOneBy([]);
        if (!$randomTicket) {
            $this->fail('No ticket found in database');
        }
        $this->client->request('GET', '/api/ticket/' . $randomTicket->getTicketCode());

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }


    public function testGetTickets()
    {
        $url = '/api/tickets';

        $admin = new User();
        $admin->setEmail('admin@tiptop.com');
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'password'));
        $admin->setIsActive(true);
        $admin->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_ADMIN']));
        $admin->setCreatedAt(new \DateTime());
        $admin->setUpdatedAt(new \DateTime());
        $admin->setDateOfBirth(new \DateTime());
        $admin->setFirstName('Test');
        $admin->setLastName('User');
        $admin->setGender('Homme');
        $admin->setPhone('123456789');
        $admin->setStatus(true);

        $client = new User();
        $client->setEmail('client' . rand(0, 100000) . '@tiptop.com');
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

        $employee = new User();
        $employee->setEmail('employee' . rand(0, 100000) . '@tiptop.com');
        $employee->setPassword($this->passwordEncoder->hashPassword($employee, 'password'));
        $employee->setIsActive(true);
        $employee->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_EMPLOYEE']));
        $employee->setCreatedAt(new \DateTime());
        $employee->setUpdatedAt(new \DateTime());
        $employee->setDateOfBirth(new \DateTime());
        $employee->setFirstName('Test');
        $employee->setLastName('User');
        $employee->setGender('Homme');
        $employee->setPhone('123456789');
        $employee->setStatus(true);


        $store = new Store();
        $store->setName('Store');
        $store->setAddress('Address');
        $store->setHeadquartersAddress('Headquarters Address');
        $store->setEmail($this->generateUniqueEmail());
        $store->setPostalCode('12345');
        $store->setCity('City');
        $store->setCountry('Country');
        $store->setCapital(1000);
        $store->setStatus(true);
        $store->setSiren('123456789');


        $prize = $this->entityManager->getRepository(Prize::class)->findOneBy([]);

        if (!$prize) {
            $prize = new Prize();
            $prize->setName('Prize');
            $prize->setPrice(100);
            $prize->setLabel('Label');
            $prize->setPrizeValue('Prize Value');
            $prize->setType('Type');
            $prize->setWinningRate(0.5);
            $this->entityManager->persist($prize);
            $this->entityManager->flush();
        }

        $ticket = new Ticket();
        $ticket->setTicketCode(uniqid());
        $ticket->setUser($client);
        $ticket->setEmployee($employee);
        $ticket->setUpdatedAt(new \DateTime());
        $ticket->setTicketGeneratedAt(new \DateTime());
        $ticket->setTicketPrintedAt(new \DateTime());
        $ticket->setStore($store);
        $ticket->setStatus(Ticket::STATUS_PRINTED);
        $ticket->setPrize($prize);
        $client->addStore($store);
        $store->addUser($client);
        $employee->addStore($store);
        $store->addUser($employee);


        $this->entityManager->persist($employee);

        $this->entityManager->persist($store);
        $this->entityManager->persist($client);
        $this->entityManager->persist($ticket);
        $this->entityManager->persist($admin);

        $this->entityManager->flush();

        $this->client->loginUser($admin);

        $params = [
            'ticket_code' => $ticket->getTicketCode(),
            'status' => $ticket->getStatus(),
            'store' => $store->getId(),
            'caissier' => $employee->getLastname(),
            'client' => $client->getLastname(),
            'prize' => $prize->getId(),
            'page' => '1',
            'limit' => '10'
        ];

        $url .= '?' . http_build_query($params);

        $this->client->request('GET', $url);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $content = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('tickets', $content);
        $this->assertArrayHasKey('totalCount', $content);
    }


    public function testGetTickets2()
    {
        $url = '/api/tickets';

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

        $this->client->loginUser($admin);

        $params = [
            'ticket_code' => '123456',
            'status' => 'pending',
            'store' => '1',
            'caissier' => 1,
            'client' => 1,
            'prize' => 1,
            'page' => 1,
            'limit' => '9'
        ];

        $url .= '?' . http_build_query($params);

        $this->client->request('GET', $url);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $content = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('tickets', $content);
        $this->assertArrayHasKey('totalCount', $content);
    }

    public function testGetTickets3()
    {
        $url = '/api/tickets';

        $admin = new User();
        $admin->setEmail('admin@tiptop.com');
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'password'));
        $admin->setIsActive(true);
        $admin->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_CLIENT']));
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

        $this->client->loginUser($admin);

        $params = [
            'ticket_code' => '123456',
            'status' => 'pending',
            'store' => '1',
            'caissier' => 1,
            'client' => 1,
            'prize' => 1,
            'page' => 1,
            'limit' => '9'
        ];

        $url .= '?' . http_build_query($params);

        $this->client->request('GET', $url);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $content = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('tickets', $content);
        $this->assertArrayHasKey('totalCount', $content);
    }

    public function testGetTickets4()
    {
        $url = '/api/tickets';

        $admin = new User();
        $admin->setEmail('admin@tiptop.com');
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'password'));
        $admin->setIsActive(true);
        $admin->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_EMPLOYEE']));
        $admin->setCreatedAt(new \DateTime());
        $admin->setUpdatedAt(new \DateTime());
        $admin->setDateOfBirth(new \DateTime());
        $admin->setFirstName('Test');
        $admin->setLastName('User');
        $admin->setGender('Homme');
        $admin->setPhone('123456789');
        $admin->setStatus(true);

        $client = new User();
        $client->setEmail('client' . rand(0, 100000) . '@tiptop.com');
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

        $employee = new User();
        $employee->setEmail('employee' . rand(0, 100000) . '@tiptop.com');
        $employee->setPassword($this->passwordEncoder->hashPassword($employee, 'password'));
        $employee->setIsActive(true);
        $employee->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_EMPLOYEE']));
        $employee->setCreatedAt(new \DateTime());
        $employee->setUpdatedAt(new \DateTime());
        $employee->setDateOfBirth(new \DateTime());
        $employee->setFirstName('Test');
        $employee->setLastName('User');
        $employee->setGender('Homme');
        $employee->setPhone('123456789');
        $employee->setStatus(true);


        $store = new Store();
        $store->setName('Store');
        $store->setAddress('Address');
        $store->setHeadquartersAddress('Headquarters Address');
        $store->setEmail($this->generateUniqueEmail());
        $store->setPostalCode('12345');
        $store->setCity('City');
        $store->setCountry('Country');
        $store->setCapital(1000);
        $store->setStatus(true);
        $store->setSiren('123456789');


        $prize = $this->entityManager->getRepository(Prize::class)->findOneBy([]);

        if (!$prize) {
            $prize = new Prize();
            $prize->setName('Prize');
            $prize->setPrice(100);
            $prize->setLabel('Label');
            $prize->setPrizeValue('Prize Value');
            $prize->setType('Type');
            $prize->setWinningRate(0.5);
            $this->entityManager->persist($prize);
            $this->entityManager->flush();
        }

        $ticket = new Ticket();
        $ticket->setTicketCode(uniqid());
        $ticket->setUser($client);
        $ticket->setEmployee($employee);
        $ticket->setUpdatedAt(new \DateTime());
        $ticket->setTicketGeneratedAt(new \DateTime());
        $ticket->setTicketPrintedAt(new \DateTime());
        $ticket->setStore($store);
        $ticket->setStatus(Ticket::STATUS_PRINTED);
        $ticket->setPrize($prize);
        $client->addStore($store);
        $store->addUser($client);
        $employee->addStore($store);
        $store->addUser($employee);


        $this->entityManager->persist($employee);

        $this->entityManager->persist($store);
        $this->entityManager->persist($client);
        $this->entityManager->persist($ticket);
        $this->entityManager->persist($admin);

        $this->entityManager->flush();

        $this->client->loginUser($admin);

        $params = [
            'ticket_code' => $ticket->getTicketCode(),
            'status' => $ticket->getStatus(),
            'store' => $store->getId(),
            'caissier' => $employee->getLastname(),
            'client' => $client->getLastname(),
            'prize' => $prize->getId(),
            'page' => '1',
            'limit' => '10'
        ];

        $url .= '?' . http_build_query($params);

        $this->client->request('GET', $url);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $content = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('tickets', $content);
        $this->assertArrayHasKey('totalCount', $content);
    }

    public function testCheckTicketForPlay()
    {
        $url = '/api/tickets/check/play';

        $admin = new User();
        $admin->setEmail('email' . rand() . '@tiptop.com');
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'password'));
        $admin->setIsActive(true);
        $admin->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_CLIENT']));
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
        $store->setEmail($this->generateUniqueEmail());
        $store->setPostalCode('12345');
        $store->setCity('City');
        $store->setCountry('Country');
        $store->setCapital(1000);
        $store->setStatus(true);
        $store->setSiren('123456789');

        $this->entityManager->persist($store);
        $this->entityManager->flush();

        $prize = $this->entityManager->getRepository(Prize::class)->findOneBy([]);

        if (!$prize) {
            $prize = new Prize();
            $prize->setName('Prize');
            $prize->setPrice(100);
            $prize->setLabel('Label');
            $prize->setPrizeValue('Prize Value');
            $prize->setType('Type');
            $prize->setWinningRate(0.5);
            $this->entityManager->persist($prize);
            $this->entityManager->flush();
        }

        $ticket = new Ticket();
        $ticket->setTicketCode(uniqid());
        $ticket->setUser($admin);
        $ticket->setUpdatedAt(new \DateTime());
        $ticket->setTicketGeneratedAt(new \DateTime());
        $ticket->setTicketPrintedAt(new \DateTime());
        $ticket->setStore($store);
        $ticket->setStatus(Ticket::STATUS_PRINTED);
        $ticket->setPrize($prize);
        $admin->addStore($store);
        $store->addUser($admin);


        $this->entityManager->persist($store);
        $this->entityManager->persist($admin);
        $this->entityManager->persist($ticket);
        $this->entityManager->flush();


        $params = [
            'ticketCode' => $ticket->getTicketCode(),
        ];


        $this->client->loginUser($admin);
        $this->client->request('POST', $url, [], [], [], json_encode($params));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);


    }

    public function testCheckTicketForPlay2()
    {
        $url = '/api/tickets/check/play';

        $admin = new User();
        $admin->setEmail('email' . rand() . '@tiptop.com');
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'password'));
        $admin->setIsActive(true);
        $admin->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_CLIENT']));
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
        $store->setEmail($this->generateUniqueEmail());
        $store->setPostalCode('12345');
        $store->setCity('City');
        $store->setCountry('Country');
        $store->setCapital(1000);
        $store->setStatus(true);
        $store->setSiren('123456789');

        $this->entityManager->persist($store);
        $this->entityManager->flush();

        $prize = $this->entityManager->getRepository(Prize::class)->findOneBy([]);

        if (!$prize) {
            $prize = new Prize();
            $prize->setName('Prize');
            $prize->setPrice(100);
            $prize->setLabel('Label');
            $prize->setPrizeValue('Prize Value');
            $prize->setType('Type');
            $prize->setWinningRate(0.5);
            $this->entityManager->persist($prize);
            $this->entityManager->flush();
        }

        $ticket = new Ticket();
        $ticket->setTicketCode(uniqid());
        $ticket->setUser($admin);
        $ticket->setUpdatedAt(new \DateTime());
        $ticket->setTicketGeneratedAt(new \DateTime());
        $ticket->setTicketPrintedAt(new \DateTime());
        $ticket->setStore($store);
        $ticket->setStatus(Ticket::STATUS_WINNER);
        $ticket->setPrize($prize);
        $admin->addStore($store);
        $store->addUser($admin);


        $this->entityManager->persist($store);
        $this->entityManager->persist($admin);
        $this->entityManager->persist($ticket);
        $this->entityManager->flush();


        $params = [
            'ticketCode' => $ticket->getTicketCode(),
        ];


        $this->client->loginUser($admin);
        $this->client->request('POST', $url, [], [], [], json_encode($params));

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        $content = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('status', $content);
        $this->assertEquals('error', $content['status']);
        $this->assertArrayHasKey('message', $content);
        $this->assertEquals('Ticket already played', $content['message']);


    }

    public function testCheckTicketForPlayError()
    {
        $url = '/api/tickets/check/play';

        $admin = new User();
        $admin->setEmail('email' . rand() . '@tiptop.com');
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'password'));
        $admin->setIsActive(true);
        $admin->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_CLIENT']));
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


        $params = [
            'ticketCode' => '123456',
        ];


        $this->client->loginUser($admin);
        $this->client->request('POST', $url, [], [], [], json_encode($params));

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        $content = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('status', $content);
        $this->assertEquals('error', $content['status']);
        $this->assertArrayHasKey('message', $content);
        $this->assertEquals('Ticket not found', $content['message']);
    }


    public function testPrintTicketByEmployee(): void
    {

        $employee = new User();
        $employee->setEmail('employee' . rand(0, 100000) . '@tiptop.com');
        $employee->setPassword($this->passwordEncoder->hashPassword($employee, 'password'));
        $employee->setIsActive(true);
        $employee->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_EMPLOYEE']));
        $employee->setCreatedAt(new \DateTime());
        $employee->setUpdatedAt(new \DateTime());
        $employee->setDateOfBirth(new \DateTime());
        $employee->setFirstName('Test');
        $employee->setLastName('User');
        $employee->setGender('Homme');
        $employee->setPhone('123456789');
        $employee->setStatus(true);

        $this->entityManager->persist($employee);
        $this->entityManager->flush();

        $store = new Store();
        $store->setName('Store');
        $store->setAddress('Address');
        $store->setHeadquartersAddress('Headquarters Address');
        $store->setEmail($this->generateUniqueEmail());
        $store->setPostalCode('12345');
        $store->setCity('City');
        $store->setCountry('Country');
        $store->setCapital(1000);
        $store->setStatus(true);
        $store->setSiren('123456789');

        $this->entityManager->persist($store);
        $this->entityManager->flush();

        $prize = $this->entityManager->getRepository(Prize::class)->findOneBy([]);

        if (!$prize) {
            $prize = new Prize();
            $prize->setName('Prize');
            $prize->setPrice(100);
            $prize->setLabel('Label');
            $prize->setPrizeValue('Prize Value');
            $prize->setType('Type');
            $prize->setWinningRate(0.5);
            $this->entityManager->persist($prize);
            $this->entityManager->flush();
        }

        $ticket = new Ticket();
        $ticket->setTicketCode(uniqid());
        $ticket->setUser($employee);
        $ticket->setUpdatedAt(new \DateTime());
        $ticket->setTicketGeneratedAt(new \DateTime());
        $ticket->setTicketPrintedAt(new \DateTime());
        $ticket->setStore($store);
        $ticket->setStatus(Ticket::STATUS_GENERATED);
        $ticket->setPrize($prize);
        $employee->addStore($store);
        $store->addUser($employee);


        $this->entityManager->persist($store);
        $this->entityManager->persist($employee);
        $this->entityManager->persist($ticket);
        $this->entityManager->flush();

        $requestData = [
            'ticketCode' => $ticket->getTicketCode(),
        ];


        $this->client->loginUser($employee);

        $this->client->request(
            'POST',
            '/api/print_ticket',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('ticket', $responseData);
    }

    public function testPrintTicketByEmployeeError(): void
    {

        $employee = new User();
        $employee->setEmail('employee' . rand(0, 100000) . '@tiptop.com');
        $employee->setPassword($this->passwordEncoder->hashPassword($employee, 'password'));
        $employee->setIsActive(true);
        $employee->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_EMPLOYEE']));
        $employee->setCreatedAt(new \DateTime());
        $employee->setUpdatedAt(new \DateTime());
        $employee->setDateOfBirth(new \DateTime());
        $employee->setFirstName('Test');
        $employee->setLastName('User');
        $employee->setGender('Homme');
        $employee->setPhone('123456789');
        $employee->setStatus(true);

        $this->entityManager->persist($employee);
        $this->entityManager->flush();


        $requestData = [
            'ticketCode' => '',
        ];


        $this->client->loginUser($employee);

        $this->client->request(
            'POST',
            '/api/print_ticket',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $this->assertSame(404, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('error', $responseData['status']);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Ticket not found', $responseData['message']);
    }


    public function testConfirmTicketPlay(): void
    {

        $employee = new User();
        $employee->setEmail('employee' . rand(0, 100000) . '@tiptop.com');
        $employee->setPassword($this->passwordEncoder->hashPassword($employee, 'password'));
        $employee->setIsActive(true);
        $employee->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_EMPLOYEE']));
        $employee->setCreatedAt(new \DateTime());
        $employee->setUpdatedAt(new \DateTime());
        $employee->setDateOfBirth(new \DateTime());
        $employee->setFirstName('Test');
        $employee->setLastName('User');
        $employee->setGender('Homme');
        $employee->setPhone('123456789');
        $employee->setStatus(true);

        $this->entityManager->persist($employee);
        $this->entityManager->flush();

        $client = new User();
        $client->setEmail('employee' . rand(0, 100000) . '@tiptop.com');
        $client->setPassword($this->passwordEncoder->hashPassword($employee, 'password'));
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

        $badge = new Badge();
        $badge->setName('Badge');
        $badge->setDescription('Description');
        $this->entityManager->persist($badge);
        $this->entityManager->flush();

        $client->addBadge($badge);
        $badge->addUser($client);


        $this->entityManager->persist($badge);
        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $store = new Store();
        $store->setName('Store');
        $store->setAddress('Address');
        $store->setHeadquartersAddress('Headquarters Address');
        $store->setEmail($this->generateUniqueEmail());
        $store->setPostalCode('12345');
        $store->setCity('City');
        $store->setCountry('Country');
        $store->setCapital(1000);
        $store->setStatus(true);
        $store->setSiren('123456789');

        $this->entityManager->persist($store);
        $this->entityManager->flush();

        $prize = $this->entityManager->getRepository(Prize::class)->findOneBy([]);

        if (!$prize) {
            $prize = new Prize();
            $prize->setName('Prize');
            $prize->setPrice(100);
            $prize->setLabel('Label');
            $prize->setPrizeValue('Prize Value');
            $prize->setType('Type');
            $prize->setWinningRate(0.5);
            $this->entityManager->persist($prize);
            $this->entityManager->flush();
        }

        $ticket = new Ticket();
        $ticket->setTicketCode(uniqid());
        $ticket->setEmployee($employee);
        $ticket->setUser($client);
        $ticket->setUpdatedAt(new \DateTime());
        $ticket->setTicketGeneratedAt(new \DateTime());
        $ticket->setTicketPrintedAt(new \DateTime());
        $ticket->setStore($store);
        $ticket->setStatus(Ticket::STATUS_PRINTED);
        $ticket->setPrize($prize);
        $employee->addStore($store);
        $store->addUser($employee);


        $this->entityManager->persist($client);
        $this->entityManager->persist($store);
        $this->entityManager->persist($employee);
        $this->entityManager->persist($ticket);
        $this->entityManager->flush();

        $requestData = [
            'ticketCode' => $ticket->getTicketCode(),
        ];

        $this->client->loginUser($client);

        $this->client->request(
            'POST',
            '/api/ticket/confirm/play',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('ticket', $responseData);
        $this->assertArrayHasKey('gainedBadges', $responseData);
        $this->assertArrayHasKey('userLoyaltyPoints', $responseData);
    }

    public function testConfirmTicketPlayTicketNotFound(): void
    {

        $employee = new User();
        $employee->setEmail('employee' . rand(0, 100000) . '@tiptop.com');
        $employee->setPassword($this->passwordEncoder->hashPassword($employee, 'password'));
        $employee->setIsActive(true);
        $employee->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_EMPLOYEE']));
        $employee->setCreatedAt(new \DateTime());
        $employee->setUpdatedAt(new \DateTime());
        $employee->setDateOfBirth(new \DateTime());
        $employee->setFirstName('Test');
        $employee->setLastName('User');
        $employee->setGender('Homme');
        $employee->setPhone('123456789');
        $employee->setStatus(true);

        $this->entityManager->persist($employee);
        $this->entityManager->flush();


        $requestData = [
            'ticketCode' => '',
        ];

        $this->client->loginUser($employee);

        $this->client->request(
            'POST',
            '/api/ticket/confirm/play',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $this->assertSame(404, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('error', $responseData['status']);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Ticket not found', $responseData['message']);
    }

    public function testConfirmTicketPlayNotLoggedInError(): void
    {
        $requestData = [
            'ticketCode' => ''
        ];


        $this->client->request(
            'POST',
            '/api/ticket/confirm/play',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $this->assertSame(404, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('error', $responseData['status']);
        $this->assertEquals('User not found', $responseData['message']);
    }


    public function testConfirmTicketGain(): void
    {

        $employee = new User();
        $employee->setEmail('employee' . rand(0, 100000) . '@tiptop.com');
        $employee->setPassword($this->passwordEncoder->hashPassword($employee, 'password'));
        $employee->setIsActive(true);
        $employee->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_EMPLOYEE']));
        $employee->setCreatedAt(new \DateTime());
        $employee->setUpdatedAt(new \DateTime());
        $employee->setDateOfBirth(new \DateTime());
        $employee->setFirstName('Test');
        $employee->setLastName('User');
        $employee->setGender('Homme');
        $employee->setPhone('123456789');
        $employee->setStatus(true);

        $this->entityManager->persist($employee);
        $this->entityManager->flush();

        $client = new User();
        $client->setEmail('employee' . rand(0, 100000) . '@tiptop.com');
        $client->setPassword($this->passwordEncoder->hashPassword($employee, 'password'));
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

        $badge = new Badge();
        $badge->setName('Badge');
        $badge->setDescription('Description');
        $this->entityManager->persist($badge);
        $this->entityManager->flush();

        $client->addBadge($badge);
        $badge->addUser($client);

        $this->entityManager->persist($badge);
        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $store = new Store();
        $store->setName('Store');
        $store->setAddress('Address');
        $store->setHeadquartersAddress('Headquarters Address');
        $store->setEmail($this->generateUniqueEmail());
        $store->setPostalCode('12345');
        $store->setCity('City');
        $store->setCountry('Country');
        $store->setCapital(1000);
        $store->setStatus(true);
        $store->setSiren('123456789');

        $this->entityManager->persist($store);
        $this->entityManager->flush();

        $prize = $this->entityManager->getRepository(Prize::class)->findOneBy([]);

        if (!$prize) {
            $prize = new Prize();
            $prize->setName('Prize');
            $prize->setPrice(100);
            $prize->setLabel('Label');
            $prize->setPrizeValue('Prize Value');
            $prize->setType('Type');
            $prize->setWinningRate(0.5);
            $this->entityManager->persist($prize);
            $this->entityManager->flush();
        }

        $ticket = new Ticket();
        $ticket->setTicketCode(uniqid());
        $ticket->setEmployee($employee);
        $ticket->setUser($client);
        $ticket->setUpdatedAt(new \DateTime());
        $ticket->setTicketGeneratedAt(new \DateTime());
        $ticket->setTicketPrintedAt(new \DateTime());
        $ticket->setStore($store);
        $ticket->setStatus(Ticket::STATUS_PENDING_VERIFICATION);
        $ticket->setPrize($prize);
        $employee->addStore($store);
        $store->addUser($employee);

        $this->entityManager->persist($client);
        $this->entityManager->persist($store);
        $this->entityManager->persist($employee);
        $this->entityManager->persist($ticket);
        $this->entityManager->flush();

        $requestData = [
            'ticketId' => $ticket->getId(),
        ];


        $this->client->loginUser($client);

        $this->client->request(
            'POST',
            '/api/ticket/confirm/gain',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('ticket', $responseData);

    }

    public function testConfirmTicketGainTicketNotFound(): void
    {

        $employee = new User();
        $employee->setEmail('employee' . rand(0, 100000) . '@tiptop.com');
        $employee->setPassword($this->passwordEncoder->hashPassword($employee, 'password'));
        $employee->setIsActive(true);
        $employee->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_EMPLOYEE']));
        $employee->setCreatedAt(new \DateTime());
        $employee->setUpdatedAt(new \DateTime());
        $employee->setDateOfBirth(new \DateTime());
        $employee->setFirstName('Test');
        $employee->setLastName('User');
        $employee->setGender('Homme');
        $employee->setPhone('123456789');
        $employee->setStatus(true);

        $this->entityManager->persist($employee);
        $this->entityManager->flush();

        $requestData = [
            'ticketId' => '',
        ];

        $this->client->loginUser($employee);

        $this->client->request(
            'POST',
            '/api/ticket/confirm/gain',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $this->assertSame(404, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('error', $responseData['status']);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Ticket not found', $responseData['message']);

    }

    public function testGetTicketsHistory(): void
    {


        $user = new User();
        $user->setEmail('user' . rand(0, 100000) . '@tiptop.com');
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

        $store = new Store();
        $store->setName('Store');
        $store->setAddress('Address');
        $store->setHeadquartersAddress('Headquarters Address');
        $store->setEmail($this->generateUniqueEmail());
        $store->setPostalCode('12345');
        $store->setCity('City');
        $store->setCountry('Country');
        $store->setCapital(1000);
        $store->setStatus(true);
        $store->setSiren('123456789');

        $prize = $this->entityManager->getRepository(Prize::class)->findOneBy([]);

        if (!$prize) {
            $prize = new Prize();
            $prize->setName('Prize');
            $prize->setPrice(100);
            $prize->setLabel('Label');
            $prize->setPrizeValue('Prize Value');
            $prize->setType('Type');
            $prize->setWinningRate(0.5);
            $this->entityManager->persist($prize);
            $this->entityManager->flush();
        }

        $ticket = new Ticket();
        $ticket->setTicketCode(uniqid());
        $ticket->setUser($user);
        $ticket->setUpdatedAt(new \DateTime());
        $ticket->setTicketGeneratedAt(new \DateTime());
        $ticket->setTicketPrintedAt(new \DateTime());
        $ticket->setStore($store);
        $ticket->setStatus(Ticket::STATUS_WINNER);
        $ticket->setPrize($prize);
        $user->addStore($store);
        $store->addUser($user);

        $this->entityManager->persist($store);
        $this->entityManager->persist($user);
        $this->entityManager->persist($ticket);
        $this->entityManager->flush();

        $ticketHistory = new TicketHistory();
        $ticketHistory->setTicket($ticket);
        $ticketHistory->setUser($user);
        $ticketHistory->setEmployee($user);
        $ticketHistory->setUpdatedAt(new \DateTime());
        $ticketHistory->setStatus(Ticket::STATUS_WINNER);

        $this->entityManager->persist($ticketHistory);
        $this->entityManager->flush();

        $this->client->loginUser($user);


        $lastYear = new \DateTime();
        $lastYear->modify('-1 year');

        $tomorrow = new \DateTime();
        $tomorrow->modify('+1 day');

        $requestData = [
            'page' => 1,
            'limit' => 9,
            'ticket_code' => $ticket->getTicketCode(),
            'store' => $store->getId(),
            'employee' => $user->getLastName(),
            'client' => $user->getLastName(),
            'status' => $ticket->getStatus(),
            'start_date' => $lastYear->format('d/m/Y'),
            'end_date' => $tomorrow->format('d/m/Y')

        ];

        $this->client->request(
            'GET',
            '/api/tickets_history',
            $requestData
        );


        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('ticketHistory', $responseData);
        $this->assertArrayHasKey('totalCount', $responseData);
    }


    public function testGetTicketsHistoryEmployee(): void
    {


        $user = new User();
        $user->setEmail('user' . rand(0, 100000) . '@tiptop.com');
        $user->setPassword($this->passwordEncoder->hashPassword($user, 'password'));
        $user->setIsActive(true);
        $user->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_EMPLOYEE']));
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());
        $user->setDateOfBirth(new \DateTime());
        $user->setFirstName('Test');
        $user->setLastName('User');
        $user->setGender('Homme');
        $user->setPhone('123456789');
        $user->setStatus(true);

        $store = new Store();
        $store->setName('Store');
        $store->setAddress('Address');
        $store->setHeadquartersAddress('Headquarters Address');
        $store->setEmail($this->generateUniqueEmail());
        $store->setPostalCode('12345');
        $store->setCity('City');
        $store->setCountry('Country');
        $store->setCapital(1000);
        $store->setStatus(true);
        $store->setSiren('123456789');

        $prize = $this->entityManager->getRepository(Prize::class)->findOneBy([]);

        if (!$prize) {
            $prize = new Prize();
            $prize->setName('Prize');
            $prize->setPrice(100);
            $prize->setLabel('Label');
            $prize->setPrizeValue('Prize Value');
            $prize->setType('Type');
            $prize->setWinningRate(0.5);
            $this->entityManager->persist($prize);
            $this->entityManager->flush();
        }

        $ticket = new Ticket();
        $ticket->setTicketCode(uniqid());
        $ticket->setUser($user);
        $ticket->setUpdatedAt(new \DateTime());
        $ticket->setTicketGeneratedAt(new \DateTime());
        $ticket->setTicketPrintedAt(new \DateTime());
        $ticket->setStore($store);
        $ticket->setStatus(Ticket::STATUS_WINNER);
        $ticket->setPrize($prize);
        $user->addStore($store);
        $store->addUser($user);

        $this->entityManager->persist($store);
        $this->entityManager->persist($user);
        $this->entityManager->persist($ticket);
        $this->entityManager->flush();

        $ticketHistory = new TicketHistory();
        $ticketHistory->setTicket($ticket);
        $ticketHistory->setUser($user);
        $ticketHistory->setEmployee($user);
        $ticketHistory->setUpdatedAt(new \DateTime());
        $ticketHistory->setStatus(Ticket::STATUS_WINNER);

        $this->entityManager->persist($ticketHistory);
        $this->entityManager->flush();

        $this->client->loginUser($user);


        $lastYear = new \DateTime();
        $lastYear->modify('-1 year');

        $tomorrow = new \DateTime();
        $tomorrow->modify('+1 day');

        $requestData = [
            'page' => 1,
            'limit' => 9,
            'ticket_code' => $ticket->getTicketCode(),
            'store' => $store->getId(),
            'employee' => $user->getId(),
            'client' => $user->getId(),
            'end_date' => $tomorrow->format('d/m/Y')

        ];

        $this->client->request(
            'GET',
            '/api/tickets_history',
            $requestData
        );


        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('ticketHistory', $responseData);
        $this->assertArrayHasKey('totalCount', $responseData);
    }


    public function testGetTicketsHistoryStoreManager(): void
    {


        $user = new User();
        $user->setEmail('user' . rand(0, 100000) . '@tiptop.com');
        $user->setPassword($this->passwordEncoder->hashPassword($user, 'password'));
        $user->setIsActive(true);
        $user->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_STOREMANAGER']));
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());
        $user->setDateOfBirth(new \DateTime());
        $user->setFirstName('Test');
        $user->setLastName('User');
        $user->setGender('Homme');
        $user->setPhone('123456789');
        $user->setStatus(true);

        $store = new Store();
        $store->setName('Store');
        $store->setAddress('Address');
        $store->setHeadquartersAddress('Headquarters Address');
        $store->setEmail($this->generateUniqueEmail());
        $store->setPostalCode('12345');
        $store->setCity('City');
        $store->setCountry('Country');
        $store->setCapital(1000);
        $store->setStatus(true);
        $store->setSiren('123456789');

        $prize = $this->entityManager->getRepository(Prize::class)->findOneBy([]);

        if (!$prize) {
            $prize = new Prize();
            $prize->setName('Prize');
            $prize->setPrice(100);
            $prize->setLabel('Label');
            $prize->setPrizeValue('Prize Value');
            $prize->setType('Type');
            $prize->setWinningRate(0.5);
            $this->entityManager->persist($prize);
            $this->entityManager->flush();
        }

        $ticket = new Ticket();
        $ticket->setTicketCode(uniqid());
        $ticket->setUser($user);
        $ticket->setUpdatedAt(new \DateTime());
        $ticket->setTicketGeneratedAt(new \DateTime());
        $ticket->setTicketPrintedAt(new \DateTime());
        $ticket->setStore($store);
        $ticket->setStatus(Ticket::STATUS_WINNER);
        $ticket->setPrize($prize);
        $user->addStore($store);
        $store->addUser($user);

        $this->entityManager->persist($store);
        $this->entityManager->persist($user);
        $this->entityManager->persist($ticket);
        $this->entityManager->flush();

        $ticketHistory = new TicketHistory();
        $ticketHistory->setTicket($ticket);
        $ticketHistory->setUser($user);
        $ticketHistory->setEmployee($user);
        $ticketHistory->setUpdatedAt(new \DateTime());
        $ticketHistory->setStatus(Ticket::STATUS_WINNER);

        $this->entityManager->persist($ticketHistory);
        $this->entityManager->flush();

        $this->client->loginUser($user);


        $lastYear = new \DateTime();
        $lastYear->modify('-1 year');

        $tomorrow = new \DateTime();
        $tomorrow->modify('+1 day');

        $requestData = [
            'page' => 1,
            'limit' => 9,
            'ticket_code' => $ticket->getTicketCode(),
            'store' => $store->getId(),
            'employee' => $user->getId(),
            'client' => $user->getId(),
            'start_date' => $lastYear->format('d/m/Y'),

        ];

        $this->client->request(
            'GET',
            '/api/tickets_history',
            $requestData
        );


        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('ticketHistory', $responseData);
        $this->assertArrayHasKey('totalCount', $responseData);
    }






    public function testGetWinnerTickets(): void
    {
        $user = new User();
        $user->setEmail('user' . rand(0, 100000) . '@tiptop.com');
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

        $store = new Store();
        $store->setName('Store');
        $store->setAddress('Address');
        $store->setHeadquartersAddress('Headquarters Address');
        $store->setEmail($this->generateUniqueEmail());
        $store->setPostalCode('12345');
        $store->setCity('City');
        $store->setCountry('Country');
        $store->setCapital(1000);
        $store->setStatus(true);
        $store->setSiren('123456789');

        $prize = $this->entityManager->getRepository(Prize::class)->findOneBy([]);

        if (!$prize) {
            $prize = new Prize();
            $prize->setName('Prize');
            $prize->setPrice(100);
            $prize->setLabel('Label');
            $prize->setPrizeValue('Prize Value');
            $prize->setType('Type');
            $prize->setWinningRate(0.5);
            $this->entityManager->persist($prize);
            $this->entityManager->flush();
        }

        $ticket = new Ticket();
        $ticket->setTicketCode(uniqid());
        $ticket->setUser($user);
        $ticket->setEmployee($user);
        $ticket->setUpdatedAt(new \DateTime());
        $ticket->setTicketGeneratedAt(new \DateTime());
        $ticket->setTicketPrintedAt(new \DateTime());
        $ticket->setStore($store);
        $ticket->setStatus(Ticket::STATUS_WINNER);
        $ticket->setPrize($prize);
        $user->addStore($store);
        $store->addUser($user);

        $this->entityManager->persist($store);
        $this->entityManager->persist($user);
        $this->entityManager->persist($ticket);
        $this->entityManager->flush();

        $requestData = [
            'page' => 1,
            'limit' => 9,
            'ticket_code' => $ticket->getTicketCode(),
            'store' => $store->getId(),
            'client' => $user->getId(),
            'employee' => $user->getId(),
            'status' => $ticket->getStatus(),
        ];

        $this->client->loginUser($user);

        $this->client->request(
            'GET',
            '/api/winner_tickets',
            $requestData
        );

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('gains', $responseData);
        $this->assertArrayHasKey('totalCount', $responseData);


    }


    public function testGetWinnerTicketsAux(): void
    {
        $user = new User();
        $user->setEmail('user' . rand(0, 100000) . '@tiptop.com');
        $user->setPassword($this->passwordEncoder->hashPassword($user, 'password'));
        $user->setIsActive(true);
        $user->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_EMPLOYEE']));
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());
        $user->setDateOfBirth(new \DateTime());
        $user->setFirstName('Test');
        $user->setLastName('User');
        $user->setGender('Homme');
        $user->setPhone('123456789');
        $user->setStatus(true);

        $store = new Store();
        $store->setName('Store');
        $store->setAddress('Address');
        $store->setHeadquartersAddress('Headquarters Address');
        $store->setEmail($this->generateUniqueEmail());
        $store->setPostalCode('12345');
        $store->setCity('City');
        $store->setCountry('Country');
        $store->setCapital(1000);
        $store->setStatus(true);
        $store->setSiren('123456789');

        $prize = $this->entityManager->getRepository(Prize::class)->findOneBy([]);

        if (!$prize) {
            $prize = new Prize();
            $prize->setName('Prize');
            $prize->setPrice(100);
            $prize->setLabel('Label');
            $prize->setPrizeValue('Prize Value');
            $prize->setType('Type');
            $prize->setWinningRate(0.5);
            $this->entityManager->persist($prize);
            $this->entityManager->flush();
        }

        $ticket = new Ticket();
        $ticket->setTicketCode(uniqid());
        $ticket->setUser($user);
        $ticket->setEmployee($user);
        $ticket->setUpdatedAt(new \DateTime());
        $ticket->setTicketGeneratedAt(new \DateTime());
        $ticket->setTicketPrintedAt(new \DateTime());
        $ticket->setStore($store);
        $ticket->setStatus(Ticket::STATUS_WINNER);
        $ticket->setPrize($prize);
        $user->addStore($store);
        $store->addUser($user);

        $this->entityManager->persist($store);
        $this->entityManager->persist($user);
        $this->entityManager->persist($ticket);
        $this->entityManager->flush();

        $requestData = [
            'page' => 1,
            'limit' => 9,
            'ticket_code' => $ticket->getTicketCode(),
            'store' => $store->getId(),
            'client' => $user->getLastname(),
            'employee' => $user->getLastname(),
            'status' => $ticket->getStatus(),
            'caissier' => $user->getLastname(),
            'prize' => $prize->getId(),
        ];

        $this->client->loginUser($user);

        $this->client->request(
            'GET',
            '/api/winner_tickets',
            $requestData
        );




        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('gains', $responseData);
        $this->assertArrayHasKey('totalCount', $responseData);


    }


    public function testGetWinnerTickets2(): void
    {
        $user = new User();
        $user->setEmail('user' . rand(0, 100000) . '@tiptop.com');
        $user->setPassword($this->passwordEncoder->hashPassword($user, 'password'));
        $user->setIsActive(true);
        $user->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_STOREMANAGER']));
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());
        $user->setDateOfBirth(new \DateTime());
        $user->setFirstName('Test');
        $user->setLastName('User');
        $user->setGender('Homme');
        $user->setPhone('123456789');
        $user->setStatus(true);

        $store = new Store();
        $store->setName('Store');
        $store->setAddress('Address');
        $store->setHeadquartersAddress('Headquarters Address');
        $store->setEmail($this->generateUniqueEmail());
        $store->setPostalCode('12345');
        $store->setCity('City');
        $store->setCountry('Country');
        $store->setCapital(1000);
        $store->setStatus(true);
        $store->setSiren('123456789');

        $prize = $this->entityManager->getRepository(Prize::class)->findOneBy([]);

        if (!$prize) {
            $prize = new Prize();
            $prize->setName('Prize');
            $prize->setPrice(100);
            $prize->setLabel('Label');
            $prize->setPrizeValue('Prize Value');
            $prize->setType('Type');
            $prize->setWinningRate(0.5);
            $this->entityManager->persist($prize);
            $this->entityManager->flush();
        }

        $ticket = new Ticket();
        $ticket->setTicketCode(uniqid());
        $ticket->setUser($user);
        $ticket->setEmployee($user);
        $ticket->setUpdatedAt(new \DateTime());
        $ticket->setTicketGeneratedAt(new \DateTime());
        $ticket->setTicketPrintedAt(new \DateTime());
        $ticket->setStore($store);
        $ticket->setStatus(Ticket::STATUS_WINNER);
        $ticket->setPrize($prize);
        $user->addStore($store);
        $store->addUser($user);

        $this->entityManager->persist($store);
        $this->entityManager->persist($user);
        $this->entityManager->persist($ticket);
        $this->entityManager->flush();

        $requestData = [
            'page' => 1,
            'limit' => 9,
            'ticket_code' => $ticket->getTicketCode(),
            'store' => $store->getId(),
            'client' => $user->getId(),
            'employee' => $user->getId(),
            'status' => $ticket->getStatus(),
        ];

        $this->client->loginUser($user);

        $this->client->request(
            'GET',
            '/api/winner_tickets',
            $requestData
        );

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('gains', $responseData);
        $this->assertArrayHasKey('totalCount', $responseData);


    }




    public function testGetWinnerTicketsHistory(): void
    {

        $requestData = [
            'page' => 1,
            'limit' => 9,
        ];

        $this->client->request(
            'GET',
            '/api/winner_tickets/history',
            $requestData
        );


        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('gains', $responseData);
        $this->assertArrayHasKey('totalCount', $responseData);


    }

}