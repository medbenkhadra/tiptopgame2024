<?php

namespace App\Tests\Repository;

use App\Entity\Prize;
use App\Entity\Role;
use App\Entity\Store;
use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TicketRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;

    private $ticketRepository;

    private $passwordEncoder;

    /**
     * @throws NotSupported
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->passwordEncoder = $this->createMock(UserPasswordHasherInterface::class);

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->ticketRepository = $this->entityManager->getRepository(Ticket::class);
    }

    public function testFindAll(): void
    {
        $tickets = $this->ticketRepository->findAll();

        $this->assertIsArray($tickets);
        foreach ($tickets as $ticket) {
            $this->assertInstanceOf(Ticket::class, $ticket);
        }
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws NotSupported
     */
    public function testFindOneBy(): void
    {

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

        $this->entityManager->persist($client);
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
        $store->setEmail('store@tiptop.com');
        $store->setPostalCode('12345');
        $store->setCity('City');
        $store->setCountry('Country');
        $store->setCapital(1000);
        $store->setStatus(true);
        $store->setSiren('123456789');

        $this->entityManager->persist($store);
        $this->entityManager->flush();

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
        $this->entityManager->flush();


        $criteria = ['id' => $ticket->getId()];

        $ticket = $this->ticketRepository->findOneBy($criteria);
        $this->assertInstanceOf(Ticket::class, $ticket);
    }



    public function testGetTicketCountByStatus(): void
    {
        $tickets = $this->ticketRepository->getTicketCountByStatus($this->ticketRepository->findAll());

        $this->assertIsArray($tickets);

        foreach ($tickets as $ticket) {
            $this->assertIsArray($ticket);
            foreach ($ticket as $t) {
                $this->assertInstanceOf(Ticket::class, $t);
            }
        }
    }


    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws NotSupported
     */
    public function testFindByDateAndStore(): void
    {
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

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $store = new Store();
        $store->setName('Store');
        $store->setAddress('Address');
        $store->setHeadquartersAddress('Headquarters Address');
        $store->setEmail(rand(0, 100000) . 'test@tiptop.com');
        $store->setPostalCode('12345');
        $store->setCity('City');
        $store->setCountry('Country');
        $store->setCapital(1000);
        $store->setStatus(true);
        $store->setSiren('123456789');

        $this->entityManager->persist($store);
        $this->entityManager->flush();

        $store->addUser($client);
        $client->addStore($store);

        $this->entityManager->persist($store);
        $this->entityManager->persist($client);
        $this->entityManager->flush();


        $tickets = $this->ticketRepository->findByDateAndStore('01/01/2021', '01/01/2022', $store, $client);

        $this->assertIsArray($tickets);

        foreach ($tickets as $ticket) {
            $this->assertInstanceOf(Ticket::class, $ticket);
        }
    }



    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws NotSupported
     */
    public function testFindByDateAndStoreStoreManager(): void
    {
        $client = new User();
        $client->setEmail('client' . rand(0, 100000) . '@tiptop.com');
        $client->setPassword($this->passwordEncoder->hashPassword($client, 'password'));
        $client->setIsActive(true);
        $client->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_STOREMANAGER']));
        $client->setCreatedAt(new \DateTime());
        $client->setUpdatedAt(new \DateTime());
        $client->setDateOfBirth(new \DateTime());
        $client->setFirstName('Test');
        $client->setLastName('User');
        $client->setGender('Homme');
        $client->setPhone('123456789');
        $client->setStatus(true);

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $store = new Store();
        $store->setName('Store');
        $store->setAddress('Address');
        $store->setHeadquartersAddress('Headquarters Address');
        $store->setEmail(rand(0, 100000) . 'test@tiptop.com');
        $store->setPostalCode('12345');
        $store->setCity('City');
        $store->setCountry('Country');
        $store->setCapital(1000);
        $store->setStatus(true);
        $store->setSiren('123456789');

        $this->entityManager->persist($store);
        $this->entityManager->flush();

        $store->addUser($client);
        $client->addStore($store);

        $this->entityManager->persist($store);
        $this->entityManager->persist($client);
        $this->entityManager->flush();


        $tickets = $this->ticketRepository->findByDateAndStore('01/01/2021', '01/01/2022', $store, $client);

        $this->assertIsArray($tickets);

        foreach ($tickets as $ticket) {
            $this->assertInstanceOf(Ticket::class, $ticket);
        }
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws NotSupported
     */
    public function testFindByDateAndStoreEmployee(): void
    {
        $client = new User();
        $client->setEmail('client' . rand(0, 100000) . '@tiptop.com');
        $client->setPassword($this->passwordEncoder->hashPassword($client, 'password'));
        $client->setIsActive(true);
        $client->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_EMPLOYEE']));
        $client->setCreatedAt(new \DateTime());
        $client->setUpdatedAt(new \DateTime());
        $client->setDateOfBirth(new \DateTime());
        $client->setFirstName('Test');
        $client->setLastName('User');
        $client->setGender('Homme');
        $client->setPhone('123456789');
        $client->setStatus(true);

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $store = new Store();
        $store->setName('Store');
        $store->setAddress('Address');
        $store->setHeadquartersAddress('Headquarters Address');
        $store->setEmail(rand(0, 100000) . 'test@tiptop.com');
        $store->setPostalCode('12345');
        $store->setCity('City');
        $store->setCountry('Country');
        $store->setCapital(1000);
        $store->setStatus(true);
        $store->setSiren('123456789');

        $this->entityManager->persist($store);
        $this->entityManager->flush();

        $store->addUser($client);
        $client->addStore($store);

        $this->entityManager->persist($store);
        $this->entityManager->persist($client);
        $this->entityManager->flush();


        $tickets = $this->ticketRepository->findByDateAndStore('01/01/2021', '01/01/2022', $store, $client);

        $this->assertIsArray($tickets);

        foreach ($tickets as $ticket) {
            $this->assertInstanceOf(Ticket::class, $ticket);
        }
    }



    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws NotSupported
     */
    public function testFindTicketsRelatedToUser(): void
    {
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

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $store = new Store();
        $store->setName('Store');
        $store->setAddress('Address');
        $store->setHeadquartersAddress('Headquarters Address');
        $store->setEmail(rand(0, 100000) . 'test@tiptop.com');
        $store->setPostalCode('12345');
        $store->setCity('City');
        $store->setCountry('Country');
        $store->setCapital(1000);
        $store->setStatus(true);
        $store->setSiren('123456789');

        $store->addUser($client);
        $client->addStore($store);

        $this->entityManager->persist($store);
        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $tickets = $this->ticketRepository->findTicketsRelatedToUser($client, '01/01/2021', '01/01/2022', $store, 'ROLE_CLIENT');

        $this->assertIsArray($tickets);

    }



    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws NotSupported
     */
    public function testFindTicketsRelatedToUserStoreManager(): void
    {
        $client = new User();
        $client->setEmail('client' . rand(0, 100000) . '@tiptop.com');
        $client->setPassword($this->passwordEncoder->hashPassword($client, 'password'));
        $client->setIsActive(true);
        $client->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_STOREMANAGER']));
        $client->setCreatedAt(new \DateTime());
        $client->setUpdatedAt(new \DateTime());
        $client->setDateOfBirth(new \DateTime());
        $client->setFirstName('Test');
        $client->setLastName('User');
        $client->setGender('Homme');
        $client->setPhone('123456789');
        $client->setStatus(true);

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $store = new Store();
        $store->setName('Store');
        $store->setAddress('Address');
        $store->setHeadquartersAddress('Headquarters Address');
        $store->setEmail(rand(0, 100000) . 'test@tiptop.com');
        $store->setPostalCode('12345');
        $store->setCity('City');
        $store->setCountry('Country');
        $store->setCapital(1000);
        $store->setStatus(true);
        $store->setSiren('123456789');

        $store->addUser($client);
        $client->addStore($store);

        $this->entityManager->persist($store);
        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $tickets = $this->ticketRepository->findTicketsRelatedToUser($client, '01/01/2021', '01/01/2022', $store, 'ROLE_CLIENT');

        $this->assertIsArray($tickets);

    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws NotSupported
     */
    public function testFindTicketsRelatedToUserEmployee(): void
    {
        $client = new User();
        $client->setEmail('client' . rand(0, 100000) . '@tiptop.com');
        $client->setPassword($this->passwordEncoder->hashPassword($client, 'password'));
        $client->setIsActive(true);
        $client->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_EMPLOYEE']));
        $client->setCreatedAt(new \DateTime());
        $client->setUpdatedAt(new \DateTime());
        $client->setDateOfBirth(new \DateTime());
        $client->setFirstName('Test');
        $client->setLastName('User');
        $client->setGender('Homme');
        $client->setPhone('123456789');
        $client->setStatus(true);

        $this->entityManager->persist($client);
        $this->entityManager->flush();



        $store = new Store();
        $store->setName('Store');
        $store->setAddress('Address');
        $store->setHeadquartersAddress('Headquarters Address');
        $store->setEmail(rand(0, 100000) . 'test@tiptop.com');
        $store->setPostalCode('12345');
        $store->setCity('City');
        $store->setCountry('Country');
        $store->setCapital(1000);
        $store->setStatus(true);
        $store->setSiren('123456789');

        $store->addUser($client);
        $client->addStore($store);

        $this->entityManager->persist($store);
        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $tickets = $this->ticketRepository->findTicketsRelatedToUser($client, '01/01/2021', '01/01/2022', $store, 'ROLE_CLIENT');

        $this->assertIsArray($tickets);

    }


    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws NotSupported
     */
    public function testFindTicketsRelatedToUserAdmin(): void
    {
        $client = new User();
        $client->setEmail('client' . rand(0, 100000) . '@tiptop.com');
        $client->setPassword($this->passwordEncoder->hashPassword($client, 'password'));
        $client->setIsActive(true);
        $client->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_ADMIN']));
        $client->setCreatedAt(new \DateTime());
        $client->setUpdatedAt(new \DateTime());
        $client->setDateOfBirth(new \DateTime());
        $client->setFirstName('Test');
        $client->setLastName('User');
        $client->setGender('Homme');
        $client->setPhone('123456789');
        $client->setStatus(true);

        $this->entityManager->persist($client);
        $this->entityManager->flush();



        $store = new Store();
        $store->setName('Store');
        $store->setAddress('Address');
        $store->setHeadquartersAddress('Headquarters Address');
        $store->setEmail(rand(0, 100000) . 'test@tiptop.com');
        $store->setPostalCode('12345');
        $store->setCity('City');
        $store->setCountry('Country');
        $store->setCapital(1000);
        $store->setStatus(true);
        $store->setSiren('123456789');

        $store->addUser($client);
        $client->addStore($store);

        $this->entityManager->persist($store);
        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $tickets = $this->ticketRepository->findTicketsRelatedToUser($client, '01/01/2021', '01/01/2022', $store, 'ROLE_CLIENT');

        $this->assertIsArray($tickets);

    }

}