<?php

namespace App\Tests\Feature\Controller\Api\Store;

use App\Entity\Role;
use App\Entity\Store;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class StoreControllerTest extends WebTestCase
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


    private function generateFakeStores(User $manager): void
    {
        for ($i = 0; $i < 10; $i++) {
            $store = new Store();
            $store->setName('Store ' . $i);
            $store->setAddress('Address ' . $i);
            $store->setHeadquartersAddress('Headquarters Address ' . $i);
            $store->setEmail('store' . $i . '@tiptop.com');
            $store->setPostalCode('12345');
            $store->setCity('City ' . $i);
            $store->setCountry('Country ' . $i);
            $store->setCapital(1000);
            $store->setStatus(true);
            $store->setSiren('123456789');

            $store->addUser($manager);
            $manager->addStore($store);

            $this->entityManager->persist($manager);

            $this->entityManager->persist($store);
        }

        $this->entityManager->flush();
    }

    private function generateUniqueEmail(): string
    {
        return 'store' . rand(0, 100000) . '@tiptop.com';
    }


    public function testGetStoreForStoreManager(): void
    {
        $manager = new User();
        $manager->setEmail('manager@tiptop.com');
        $manager->setPassword($this->passwordEncoder->hashPassword($manager, 'password'));
        $manager->setIsActive(true);
        $manager->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_STOREMANAGER']));
        $manager->setCreatedAt(new \DateTime());
        $manager->setUpdatedAt(new \DateTime());
        $manager->setDateOfBirth(new \DateTime());
        $manager->setFirstName('Test');
        $manager->setLastName('User');
        $manager->setGender('Homme');

        $manager->setStatus(true);

        $this->entityManager->persist($manager);
        $this->entityManager->flush();

        $this->generateFakeStores($manager);

        $this->client->loginUser($manager);

        $this->client->request('GET', '/api/storemanager/' . $manager->getId() . '/store');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }


    public function testGetStoresForAdmin(): void
    {
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
        $this->entityManager->persist($admin);

        $this->entityManager->flush();

        $this->generateFakeStores($admin);

        $this->client->loginUser($admin);


        $this->client->request('GET', '/api/admin/stores');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }


    public function testGetStoreById(): void
    {

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
        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $this->generateFakeStores($admin);

        $store = $admin->getStores()[0];

        $this->client->loginUser($admin);

        $storeId = $store->getId();

        $this->client->request('GET', '/api/store/' . $storeId);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testGetStoreByIdError(): void
    {

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
        $this->entityManager->persist($admin);
        $this->entityManager->flush();


        $storeId = 9999999;

        $this->client->loginUser($admin);

        $this->client->request('GET', '/api/store/' . $storeId);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testGetStoreByIdManager(): void
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

        $this->generateFakeStores($admin);

        $store = $admin->getStores()[0];

        $this->client->loginUser($admin);

        $storeId = $store->getId();

        $this->client->request('GET', '/api/store/' . $storeId);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }


    public function testGetStoreByIdEmployee(): void
    {

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
        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $this->generateFakeStores($admin);

        $store = $admin->getStores()[0];

        $this->client->loginUser($admin);

        $storeId = $store->getId();

        $this->client->request('GET', '/api/store/' . $storeId);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }


    public function testGetStoreByIdClient(): void
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


        $client = new User();
        $client->setEmail('client@tiptop.com');
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

        $this->generateFakeStores($admin);

        $store = $admin->getStores()[0];

        $store->addUser($client);
        $client->addStore($store);


        $this->entityManager->persist($store);
        $this->entityManager->persist($client);
        $this->entityManager->flush();


        $this->client->loginUser($admin);

        $storeId = $store->getId();

        $this->client->request('GET', '/api/store/' . $storeId);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }


    public function testAddNewStoreByAdmin(): void
    {
        // Create and persist the ROLE_ADMIN role
        $adminRole = new Role();
        $adminRole->setName('ROLE_ADMIN');
        $adminRole->setLabel('Admin');
        $this->entityManager->persist($adminRole);
        $this->entityManager->flush();

        // Create and persist the admin user
        $admin = new User();
        $admin->setEmail('admin@tiptop.com');
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'password'));
        $admin->setIsActive(true);
        $admin->setCreatedAt(new \DateTime());
        $admin->setUpdatedAt(new \DateTime());
        $admin->setDateOfBirth(new \DateTime());
        $admin->setFirstName('Test');
        $admin->setLastName('User');
        $admin->setGender('Homme');
        $admin->setPhone('123456789');
        $admin->setStatus(true);
        $admin->setRole($adminRole);
        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        // Login the admin user
        $this->client->loginUser($admin);


        // Make the request to add a new store
        $this->client->request('POST', '/api/admin/store/add', [], [], [], json_encode([
            'name' => 'Store',
            'address' => 'Address',
            'headquarters_address' => 'Headquarters Address',
            'email' => $this->generateUniqueEmail(),
            'postal_code' => '12345',
            'city' => 'City',
            'country' => 'Country',
            'capital' => 1000,
            'status' => true,
            'siren' => '123456789',
            'opening_date' => '01/01/2021',
            'phone_number' => '123456789',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertArrayHasKey('storeResponse', $responseData);
        $this->assertArrayHasKey('id', $responseData['storeResponse']);
    }

    public function testAddNewStoreByAdminAux(): void
    {
        $adminRole = new Role();
        $adminRole->setName('ROLE_ADMIN');
        $adminRole->setLabel('Admin');
        $this->entityManager->persist($adminRole);
        $this->entityManager->flush();

        $admin = new User();
        $admin->setEmail('admin@tiptop.com');
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'password'));
        $admin->setIsActive(true);
        $admin->setCreatedAt(new \DateTime());
        $admin->setUpdatedAt(new \DateTime());
        $admin->setDateOfBirth(new \DateTime());
        $admin->setFirstName('Test');
        $admin->setLastName('User');
        $admin->setGender('Homme');
        $admin->setPhone('123456789');
        $admin->setStatus(true);
        $admin->setRole($adminRole);
        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $this->client->loginUser($admin);


        $this->client->request('POST', '/api/admin/store/add', [], [], [], json_encode([
            'name' => 'Store',
            'address' => 'Address',
            'headquarters_address' => 'Headquarters Address',
            'email' => $this->generateUniqueEmail(),
            'postal_code' => '12345',
            'city' => 'City',
            'country' => 'Country',
            'capital' => 1000,
            'status' => true,
            'siren' => '123456789',
            'opening_date' => '',
            'phone_number' => '123456789',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertArrayHasKey('storeResponse', $responseData);
        $this->assertArrayHasKey('id', $responseData['storeResponse']);
    }

    public function testAddNewStoreByAdminError(): void
    {
        $adminRole = new Role();
        $adminRole->setName('ROLE_ADMIN');
        $adminRole->setLabel('Admin');
        $this->entityManager->persist($adminRole);
        $this->entityManager->flush();

        $admin = new User();
        $admin->setEmail('admin@tiptop.com');
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'password'));
        $admin->setIsActive(true);
        $admin->setCreatedAt(new \DateTime());
        $admin->setUpdatedAt(new \DateTime());
        $admin->setDateOfBirth(new \DateTime());
        $admin->setFirstName('Test');
        $admin->setLastName('User');
        $admin->setGender('Homme');
        $admin->setPhone('123456789');
        $admin->setStatus(true);
        $admin->setRole($adminRole);
        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $this->client->loginUser($admin);


        $this->client->request('POST', '/api/admin/store/add');
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
    }


    public function testUpdateStoreByIdForStoreManager(): void
    {
        $adminRole = new Role();
        $adminRole->setName('ROLE_STOREMANAGER');
        $adminRole->setLabel('Admin');
        $this->entityManager->persist($adminRole);
        $this->entityManager->flush();

        $admin = new User();
        $admin->setEmail('admin@tiptop.com');
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'password'));
        $admin->setIsActive(true);
        $admin->setCreatedAt(new \DateTime());
        $admin->setUpdatedAt(new \DateTime());
        $admin->setDateOfBirth(new \DateTime());
        $admin->setFirstName('Test');
        $admin->setLastName('User');
        $admin->setGender('Homme');
        $admin->setPhone('123456789');
        $admin->setStatus(true);
        $admin->setRole($adminRole);
        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $this->client->loginUser($admin);


        $storeId = 1;

        $this->client->request('POST', '/api/admin/store/update/' . $storeId, [], [], [], json_encode([
            'name' => 'Store Updated',
            'address' => 'Address Updated',
            'headquarters_address' => 'Headquarters Address Updated',
            'email' => $this->generateUniqueEmail(),
            'postal_code' => '12345',
            'city' => 'City Updated',
            'country' => 'Country Updated',
            'capital' => 1000,
            'status' => true,
            'siren' => '123456789',
            'opening_date' => '01/01/2021',
            'phone_number' => '123456789',
        ]));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testUpdateStoreByIdForAdmin(): void
    {
        $adminRole = new Role();
        $adminRole->setName('ROLE_ADMIN');
        $adminRole->setLabel('Admin');
        $this->entityManager->persist($adminRole);
        $this->entityManager->flush();

        $admin = new User();
        $admin->setEmail('admin@tiptop.com');
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'password'));
        $admin->setIsActive(true);
        $admin->setCreatedAt(new \DateTime());
        $admin->setUpdatedAt(new \DateTime());
        $admin->setDateOfBirth(new \DateTime());
        $admin->setFirstName('Test');
        $admin->setLastName('User');
        $admin->setGender('Homme');
        $admin->setPhone('123456789');
        $admin->setStatus(true);
        $admin->setRole($adminRole);
        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $this->client->loginUser($admin);


        $storeId = 1;

        $this->client->request('POST', '/api/admin/store/update/' . $storeId, [], [], [], json_encode([
            'name' => 'Store Updated',
            'address' => 'Address Updated',
            'headquarters_address' => 'Headquarters Address Updated',
            'email' => $this->generateUniqueEmail(),
            'postal_code' => '12345',
            'city' => 'City Updated',
            'country' => 'Country Updated',
            'capital' => 1000,
            'status' => true,
            'siren' => '123456789',
            'opening_date' => '01/01/2021',
            'phone_number' => '123456789',
        ]));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testUpdateStoreByIdForAdminError1(): void
    {
        $adminRole = new Role();
        $adminRole->setName('ROLE_ADMIN');
        $adminRole->setLabel('Admin');
        $this->entityManager->persist($adminRole);
        $this->entityManager->flush();

        $admin = new User();
        $admin->setEmail('admin@tiptop.com');
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'password'));
        $admin->setIsActive(true);
        $admin->setCreatedAt(new \DateTime());
        $admin->setUpdatedAt(new \DateTime());
        $admin->setDateOfBirth(new \DateTime());
        $admin->setFirstName('Test');
        $admin->setLastName('User');
        $admin->setGender('Homme');
        $admin->setPhone('123456789');
        $admin->setStatus(true);
        $admin->setRole($adminRole);
        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $this->client->loginUser($admin);


        $this->client->request('POST', '/api/admin/store/update/99999999', [], [], [], json_encode([
            'name' => 'Store Updated',
            'address' => 'Address Updated',
            'headquarters_address' => 'Headquarters Address Updated',
            'email' => $this->generateUniqueEmail(),
            'postal_code' => '12345',
            'city' => 'City Updated',
            'country' => 'Country Updated',
            'capital' => 1000,
            'status' => true,
            'siren' => '123456789',
            'opening_date' => '01/01/2021',
            'phone_number' => '123456789',
        ]));
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }


    public function testUpdateStoreByIdForAdminError2(): void
    {
        $adminRole = new Role();
        $adminRole->setName('ROLE_ADMIN');
        $adminRole->setLabel('Admin');
        $this->entityManager->persist($adminRole);
        $this->entityManager->flush();

        $admin = new User();
        $admin->setEmail('admin@tiptop.com');
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'password'));
        $admin->setIsActive(true);
        $admin->setCreatedAt(new \DateTime());
        $admin->setUpdatedAt(new \DateTime());
        $admin->setDateOfBirth(new \DateTime());
        $admin->setFirstName('Test');
        $admin->setLastName('User');
        $admin->setGender('Homme');
        $admin->setPhone('123456789');
        $admin->setStatus(true);
        $admin->setRole($adminRole);
        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $this->client->loginUser($admin);


        $storeId = 1;

        $this->client->request('POST', '/api/admin/store/update/' . $storeId, [], [], [], json_encode([
            'siren' => '123456789',
            'opening_date' => '01/01/2021',
            'phone_number' => '123456789',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }


    public function testDeleteStoreById(): void
    {
        $adminRole = new Role();
        $adminRole->setName('ROLE_ADMIN');
        $adminRole->setLabel('Admin');
        $this->entityManager->persist($adminRole);
        $this->entityManager->flush();

        $admin = new User();
        $admin->setEmail('admin@tiptop.com');
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'password'));
        $admin->setIsActive(true);
        $admin->setCreatedAt(new \DateTime());
        $admin->setUpdatedAt(new \DateTime());
        $admin->setDateOfBirth(new \DateTime());
        $admin->setFirstName('Test');
        $admin->setLastName('User');
        $admin->setGender('Homme');
        $admin->setPhone('123456789');
        $admin->setStatus(true);
        $admin->setRole($adminRole);
        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $this->client->loginUser($admin);

        $storeId = 1;

        $this->client->request('DELETE', '/api/admin/store/delete/' . $storeId);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testDeleteStoreByIdNewStore(): void
    {
        $adminRole = new Role();
        $adminRole->setName('ROLE_ADMIN');
        $adminRole->setLabel('Admin');
        $this->entityManager->persist($adminRole);
        $this->entityManager->flush();

        $admin = new User();
        $admin->setEmail('admin@tiptop.com');
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'password'));
        $admin->setIsActive(true);
        $admin->setCreatedAt(new \DateTime());
        $admin->setUpdatedAt(new \DateTime());
        $admin->setDateOfBirth(new \DateTime());
        $admin->setFirstName('Test');
        $admin->setLastName('User');
        $admin->setGender('Homme');
        $admin->setPhone('123456789');
        $admin->setStatus(true);
        $admin->setRole($adminRole);
        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $this->client->loginUser($admin);

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

        $storeId = $store->getId();

        $this->client->request('DELETE', '/api/admin/store/delete/' . $storeId);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }


    public function testDeleteStoreByIdError(): void
    {
        $adminRole = new Role();
        $adminRole->setName('ROLE_ADMIN');
        $adminRole->setLabel('Admin');
        $this->entityManager->persist($adminRole);
        $this->entityManager->flush();

        $admin = new User();
        $admin->setEmail('admin@tiptop.com');
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'password'));
        $admin->setIsActive(true);
        $admin->setCreatedAt(new \DateTime());
        $admin->setUpdatedAt(new \DateTime());
        $admin->setDateOfBirth(new \DateTime());
        $admin->setFirstName('Test');
        $admin->setLastName('User');
        $admin->setGender('Homme');
        $admin->setPhone('123456789');
        $admin->setStatus(true);
        $admin->setRole($adminRole);
        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $this->client->loginUser($admin);

        $storeId = 999999999;

        $this->client->request('DELETE', '/api/admin/store/delete/' . $storeId);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }


    public function testGetStoresForClient(): void
    {
        $client = new User();
        $client->setEmail('client@tiptop.com');
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

        $this->generateFakeStores($client);

        $this->client->loginUser($client);

        $params = [
            'search' => 't',
            'page' => 1,
            'limit' => 10,

        ];

        $this->client->request('GET', '/api/client/stores?' . http_build_query($params));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testAssociateClientToStoreMissingId(): void
    {
        $client = new User();
        $client->setEmail('client@tiptop.com');
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

        $this->generateFakeStores($client);

        $this->client->loginUser($client);

        $this->client->request('POST', '/api/client/store/associate', [], [], [], json_encode([

        ]));
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testAssociateClientToStore(): void
    {
        $client = new User();
        $client->setEmail('client@tiptop.com');
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

        $this->generateFakeStores($client);

        $this->client->loginUser($client);


        $this->client->request('POST', '/api/client/store/associate', [], [], [], json_encode([
            'storeId' => $client->getStores()[0]->getId()
        ]));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }


}
