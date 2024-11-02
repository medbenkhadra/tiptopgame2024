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

class UserRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;

    private $userRepository;

    private $passwordEncoder;

    /**
     * @throws NotSupported
     */
    protected function setUp(): void
    {
        $this->passwordEncoder = $this->createMock(UserPasswordHasherInterface::class);

        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    public function testFindAll(): void
    {
        $users = $this->userRepository->findAll();

        $this->assertIsArray($users);
        foreach ($users as $user) {
            $this->assertInstanceOf(User::class, $user);
        }
    }

    public function testFindOneBy(): void
    {
        $criteria = ['id' => 1];
        $user = $this->userRepository->findOneBy($criteria);
        $this->assertInstanceOf(User::class, $user);
    }



    //findUsersOnRole

    /**
     * @throws OptimisticLockException
     * @throws NotSupported
     * @throws ORMException
     */
    public function testFindUsersOnRole(): void
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
        $store->setName('Store' . rand(0, 100000));
        $store->setAddress('Address' . rand(0, 100000));
        $store->setCity('City' . rand(0, 100000));
        $store->setCountry('Country' . rand(0, 100000));
        $store->setPhone('Phone' . rand(0, 100000));
        $store->setEmail('Email' . rand(0, 100000));
        $store->setHeadquartersAddress('HeadquartersAddress' . rand(0, 100000));
        $store->setCapital(1000);
        $store->setPostalCode('PostalCode' . rand(0, 100000));
        $store->setSiren('Siren' . rand(0, 100000));
        $store->setStatus(true);
        $this->entityManager->persist($store);
        $this->entityManager->flush();


        $employee->addStore($store);
        $store->addUser($employee);
        $this->entityManager->persist($employee);
        $this->entityManager->persist($store);
        $this->entityManager->flush();


        $users = $this->userRepository->findUsersOnRole($employee , $store->getId());
        $this->assertIsArray($users);
        foreach ($users as $user) {
            $this->assertInstanceOf(User::class, $user);
        }
    }


    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws NotSupported
     */
    public function testFindUsersOnRoleAdmin(): void
    {
        $employee = new User();
        $employee->setEmail('employee' . rand(0, 100000) . '@tiptop.com');
        $employee->setPassword($this->passwordEncoder->hashPassword($employee, 'password'));
        $employee->setIsActive(true);
        $employee->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_ADMIN']));
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
        $store->setName('Store' . rand(0, 100000));
        $store->setAddress('Address' . rand(0, 100000));
        $store->setCity('City' . rand(0, 100000));
        $store->setCountry('Country' . rand(0, 100000));
        $store->setPhone('Phone' . rand(0, 100000));
        $store->setEmail('Email' . rand(0, 100000));
        $store->setHeadquartersAddress('HeadquartersAddress' . rand(0, 100000));
        $store->setCapital(1000);
        $store->setPostalCode('PostalCode' . rand(0, 100000));
        $store->setSiren('Siren' . rand(0, 100000));
        $store->setStatus(true);
        $this->entityManager->persist($store);
        $this->entityManager->flush();


        $employee->addStore($store);
        $store->addUser($employee);
        $this->entityManager->persist($employee);
        $this->entityManager->persist($store);
        $this->entityManager->flush();


        $users = $this->userRepository->findUsersOnRole($employee , $store->getId());
        $this->assertIsArray($users);
        foreach ($users as $user) {
            $this->assertInstanceOf(User::class, $user);
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws NotSupported
     */
    public function testFindUsersOnRoleStoreManager(): void
    {
        $employee = new User();
        $employee->setEmail('employee' . rand(0, 100000) . '@tiptop.com');
        $employee->setPassword($this->passwordEncoder->hashPassword($employee, 'password'));
        $employee->setIsActive(true);
        $employee->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_STOREMANAGER']));
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
        $store->setName('Store' . rand(0, 100000));
        $store->setAddress('Address' . rand(0, 100000));
        $store->setCity('City' . rand(0, 100000));
        $store->setCountry('Country' . rand(0, 100000));
        $store->setPhone('Phone' . rand(0, 100000));
        $store->setEmail('Email' . rand(0, 100000));
        $store->setHeadquartersAddress('HeadquartersAddress' . rand(0, 100000));
        $store->setCapital(1000);
        $store->setPostalCode('PostalCode' . rand(0, 100000));
        $store->setSiren('Siren' . rand(0, 100000));
        $store->setStatus(true);
        $this->entityManager->persist($store);
        $this->entityManager->flush();


        $employee->addStore($store);
        $store->addUser($employee);
        $this->entityManager->persist($employee);
        $this->entityManager->persist($store);
        $this->entityManager->flush();


        $users = $this->userRepository->findUsersOnRole($employee , $store->getId());
        $this->assertIsArray($users);
        foreach ($users as $user) {
            $this->assertInstanceOf(User::class, $user);
        }
    }

    /**
     * @throws NotSupported
     * @throws ORMException
     */
    public function testCheckClientActivationTokenValidity(): void
    {
        $client = new User();
        $client->setEmail('email' . rand(0, 100000) . '@tiptop.com');
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
        $client->setToken('token');
        $client->setTokenExpiredAt(new \DateTime('+1 day'));

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $email = $client->getEmail();

        $token = $client->getToken();

        $result = $this->userRepository->checkClientActivationTokenValidity($email, $token);

        $this->assertTrue($result);
    }


    /**
     * @throws NotSupported
     * @throws ORMException
     */
    public function testCheckClientActivationTokenValidityCase2(): void
    {
        $client = new User();
        $client->setEmail('email' . rand(0, 100000) . '@tiptop.com');
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
        $client->setToken('token');
        $client->setTokenExpiredAt(new \DateTime('-1 day'));

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $email = $client->getEmail();

        $token = 'invalid token';

        $result = $this->userRepository->checkClientActivationTokenValidity($email, $token);

        $this->assertFalse($result);
    }


    /**
     * @throws OptimisticLockException
     * @throws NotSupported
     * @throws ORMException
     */
    public function testFindUniqueParticipants(): void
    {

        $store = new Store();
        $store->setName('Store');
        $store->setAddress('Address');
        $store->setHeadquartersAddress('Headquarters Address');
        $store->setEmail(rand(0, 100000) . '_99@tiptop.com');
        $store->setPostalCode('12345');
        $store->setCity('City');
        $store->setCountry('Country');
        $store->setCapital(1000);
        $store->setStatus(true);
        $store->setSiren('123456789');

        $this->entityManager->persist($store);
        $this->entityManager->flush();

        $client = new User();
        $client->setEmail('email' . rand(0, 100000) . '@tiptop.com');
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

        $ticket = new Ticket();
        $ticket->setTicketCode(uniqid());
        $ticket->setUser($client);
        $ticket->setEmployee($client);
        $ticket->setUpdatedAt(new \DateTime());
        $ticket->setTicketGeneratedAt(new \DateTime());
        $ticket->setTicketPrintedAt(new \DateTime());
        $ticket->setStore($store);
        $ticket->setStatus(Ticket::STATUS_PRINTED);
        $ticket->setPrize($prize);
        $client->addStore($store);
        $store->addUser($client);

        $this->entityManager->persist($ticket);
        $this->entityManager->flush();

        $users = $this->userRepository->findUniqueParticipants();
        $this->assertIsArray($users);
        foreach ($users as $user) {
            $this->assertInstanceOf(User::class, $user);
        }

    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws NotSupported
     */
    public function testActivateUserAccount(): void
    {
        $client = new User();
        $client->setEmail('email' . rand(0, 100000) . '@tiptop.com');
        $client->setPassword($this->passwordEncoder->hashPassword($client, 'password'));
        $client->setIsActive(false);
        $client->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_CLIENT']));
        $client->setCreatedAt(new \DateTime());
        $client->setUpdatedAt(new \DateTime());
        $client->setDateOfBirth(new \DateTime());
        $client->setFirstName('Test');
        $client->setLastName('User');
        $client->setGender('Homme');
        $client->setPhone('123456789');
        $client->setStatus(true);
        $client->setToken('token');
        $client->setTokenExpiredAt(new \DateTime('+1 day'));

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $email = $client->getEmail();

        $this->userRepository->activateUserAccount($email);

        $this->assertNull($client->getToken());
        $this->assertNull($client->getTokenExpiredAt());
    }



}