<?php

namespace App\Tests\Feature\Controller\Api\ConnectionHistory;

use App\Entity\ConnectionHistory;
use App\Entity\Role;
use App\Entity\Store;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ConnectionHistoryControllerTest extends WebTestCase
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

    public function testGetConnectionsHistory(): void
    {


        $employee = new User();
        $employee->setEmail('employee' . rand(0, 1000000) . '@tiptop.com');
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
        $store->setEmail('email'.rand(0, 1000000).'@tiptop.com');
        $store->setPostalCode('12345');
        $store->setCity('City');
        $store->setCountry('Country');
        $store->setCapital(1000);
        $store->setStatus(true);
        $store->setSiren('123456789');

        $this->entityManager->persist($store);
        $this->entityManager->flush();

        $employee->addStore($store);
        $store->addUser($employee);

        $this->entityManager->persist($employee);
        $this->entityManager->persist($store);
        $this->entityManager->flush();

        $newConnectionHistory = new ConnectionHistory();
        $newConnectionHistory->setUser($employee);
        $newConnectionHistory->setDuration(100);
        $newConnectionHistory->setIsActive(false);
        $newConnectionHistory->setLoginTime(new \DateTime());
        $newConnectionHistory->setLogoutTime(new \DateTime());

        $this->entityManager->persist($newConnectionHistory);
        $this->entityManager->flush();

        $employee->addConnectionHistory($newConnectionHistory);
        $newConnectionHistory->setUser($employee);

        $this->entityManager->persist($employee);
        $this->entityManager->persist($newConnectionHistory);
        $this->entityManager->flush();


        $this->client->loginUser($employee);



        $this->client->request('GET', '/api/connection_history' , $params = [
            'user' => $employee->getId(),
            'store' => $store->getId(),
            'role' => 'ROLE_EMPLOYEE',
            'page' => 1,
            'limit' => 10,
            'start_date' => '01/01/2021',
            'end_date' => '01/01/2036',
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('connectionsHistory', $responseData);

        $this->assertIsArray($responseData['connectionsHistory']);

        $this->assertArrayHasKey('connectionsHistoryCount', $responseData);
    }


    public function testGetConnectionsHistory2(): void
    {
        $this->client->request('GET', '/api/connection_history' , $params = [
            'start_date' => '01/01/2021',
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('connectionsHistory', $responseData);

        $this->assertIsArray($responseData['connectionsHistory']);

        $this->assertArrayHasKey('connectionsHistoryCount', $responseData);
    }

    public function testGetConnectionsHistory3(): void
    {
        $this->client->request('GET', '/api/connection_history' , $params = [
            'end_date' => '01/01/2022',
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('connectionsHistory', $responseData);

        $this->assertIsArray($responseData['connectionsHistory']);

        $this->assertArrayHasKey('connectionsHistoryCount', $responseData);
    }

}
