<?php

namespace App\Tests\Feature\Controller\Api\Store;

use App\Entity\Role;
use App\Entity\Store;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class StoreUserControllerTest extends WebTestCase
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


    private function generateFakeUsersForStore(Store $store): void
    {
        $roles = [
            'ROLE_STOREMANAGER',
            'ROLE_EMPLOYEE',
            'ROLE_CLIENT'
        ];

        $count = 20;

        for ($i = 0; $i < $count; $i++) {
            $user = new User();
            $user->setEmail('user' . $i . uniqid() . '@tiptop.com');
            $user->setPassword($this->passwordEncoder->hashPassword($user, 'password'));
            $user->setIsActive(true);
            $user->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => $roles[$i % 3]]));
            $user->setCreatedAt(new \DateTime());
            $user->setUpdatedAt(new \DateTime());
            $user->setDateOfBirth(new \DateTime());
            $user->setFirstName('Test');
            $user->setLastName('User');
            $user->setGender('Homme');
            $user->setPhone('123456789');
            $user->setStatus(true);
            $store->addUser($user);
            $user->addStore($store);
            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();

    }

   public function testGetStoreUsersForAdmin(): void
   {
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

       $this->entityManager->persist($store);
       $this->entityManager->flush();

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
       $store->addUser($admin);
       $admin->addStore($store);


       $this->entityManager->persist($store);
       $this->entityManager->persist($admin);

       $this->entityManager->flush();


       $this->generateFakeUsersForStore($store);

       $this->client->loginUser($admin);




       $this->client->request('GET', '/api/admin/store/' . $store->getId() . '/users');

       $this->assertResponseStatusCodeSame(Response::HTTP_OK);

       $this->assertJson($this->client->getResponse()->getContent());

       $responseData = json_decode($this->client->getResponse()->getContent(), true);
       $this->assertArrayHasKey('storeManagerUsers', $responseData);
       $this->assertArrayHasKey('storeEmployeeUsers', $responseData);
       $this->assertArrayHasKey('storeClientUsers', $responseData);
       $this->assertArrayHasKey('storeManagerUsersCount', $responseData);
       $this->assertArrayHasKey('storeEmployeeUsersCount', $responseData);
       $this->assertArrayHasKey('storeClientUsersCount', $responseData);
   }

   public function testGetStoreUsersForAdminError(): void
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


       $this->client->loginUser($admin);




       $this->client->request('GET', '/api/admin/store/' . 99999999 . '/users');
       $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
       $this->assertJson($this->client->getResponse()->getContent());
       $responseData = json_decode($this->client->getResponse()->getContent(), true);
       $this->assertArrayHasKey('error', $responseData);

   }


    public function testGetStoreUsersByRole(): void
    {
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

        $this->entityManager->persist($store);
        $this->entityManager->flush();

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
        $store->addUser($admin);
        $admin->addStore($store);


        $this->entityManager->persist($store);
        $this->entityManager->persist($admin);

        $this->entityManager->flush();

        $this->client->loginUser($admin);


        $this->client->request('POST', '/api/store/' . $store->getId() . '/users', [], [], [], json_encode([
            'column' => ['dataIndex' => 'age'],
            'order' => 'asc',
            'role' => 'ROLE_ADMIN',
            'pagination' => ['current' => 1, 'pageSize' => 10],
            'filters' => ['status' => [1], 'gender' => ['Homme']],
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($this->client->getResponse()->getContent());
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('storeManagerUsers', $responseData);
        $this->assertArrayHasKey('totalCount', $responseData);
    }

    public function testGetStoreUsersByRoleAux(): void
    {
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

        $this->entityManager->persist($store);
        $this->entityManager->flush();

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
        $store->addUser($admin);
        $admin->addStore($store);


        $this->entityManager->persist($store);
        $this->entityManager->persist($admin);

        $this->entityManager->flush();

        $this->client->loginUser($admin);


        $this->client->request('POST', '/api/store/' . $store->getId() . '/users', [], [], [], json_encode([
            'column' => ['dataIndex' => 'id'],
            'order' => 'asc',
            'role' => 'ROLE_ADMIN',
            'pagination' => ['current' => 1, 'pageSize' => 10],
            'filters' => ['status' => [1], 'gender' => ['Homme']],
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($this->client->getResponse()->getContent());
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('storeManagerUsers', $responseData);
        $this->assertArrayHasKey('totalCount', $responseData);
    }



    public function testGetStoreUsersByRoleStoreNotFound(): void
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

        $this->client->loginUser($admin);


        $this->client->request('POST', '/api/store/' . 99999999 . '/users', [], [], [], json_encode([
            'column' => ['dataIndex' => 'id'],
            'order' => 'asc',
            'role' => 'ROLE_ADMIN',
            'pagination' => ['current' => 1, 'pageSize' => 10],
            'filters' => ['status' => [1], 'gender' => ['Homme']],
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertJson($this->client->getResponse()->getContent());
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
    }





    public function testAddNewUserToStore(): void
    {
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

        $this->entityManager->persist($store);
        $this->entityManager->flush();

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
        $store->addUser($admin);
        $admin->addStore($store);


        $this->entityManager->persist($store);
        $this->entityManager->persist($admin);

        $this->entityManager->flush();

        $this->client->loginUser($admin);


        $this->client->request('POST', '/api/store/' . $store->getId() . '/user/add', [], [], [], json_encode([
            'email' => $this->generateUniqueEmail(),
            'firstname' => 'Test',
            'lastname' => 'User',
            'phone' => '123456789',
            'gender' => 'Homme',
            'dateOfBirth' => '01/01/2000',
            'role' => 'ROLE_EMPLOYEE',
            'status' => true
            ]));



        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($this->client->getResponse()->getContent());
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('user', $responseData);


    }

    public function testAddNewUserToStore2(): void
    {
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

        $this->entityManager->persist($store);
        $this->entityManager->flush();

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
        $store->addUser($admin);
        $admin->addStore($store);


        $this->entityManager->persist($store);
        $this->entityManager->persist($admin);

        $this->entityManager->flush();

        $this->client->loginUser($admin);


        $this->client->request('POST', '/api/store/' . $store->getId() . '/user/add', [], [], [], json_encode([
            'email' => $this->generateUniqueEmail(),
            'firstname' => 't',
            'lastname' => 'r',
            'phone' => '123456789',
            'gender' => 'Homme',
            'dateOfBirth' => '01/01/2000',
            'role' => 'ROLE_STOREMANAGER',
            'status' => true
        ]));



        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($this->client->getResponse()->getContent());
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('user', $responseData);


    }

    public function testAddNewUserToStoreError2(): void
    {
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

        $this->entityManager->persist($store);
        $this->entityManager->flush();

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
        $store->addUser($admin);
        $admin->addStore($store);


        $this->entityManager->persist($store);
        $this->entityManager->persist($admin);

        $this->entityManager->flush();



        $this->client->loginUser($admin);


        $this->client->request('POST', '/api/store/' . 9999999 . '/user/add', [], [], [], json_encode([
            'email' => $this->generateUniqueEmail(),
            'firstname' => 'st',
            'lastname' => 'Ur',
            'phone' => '123456789',
            'gender' => 'Homme',
            'dateOfBirth' => '01/01/2000',
            'role' => 'ROLE_STOREMANAGER',
        ]));



        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertJson($this->client->getResponse()->getContent());
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('error', $responseData);

    }

    public function testAddNewUserToStoreError3(): void
    {
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

        $this->entityManager->persist($store);
        $this->entityManager->flush();

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
        $store->addUser($admin);
        $admin->addStore($store);


        $this->entityManager->persist($store);
        $this->entityManager->persist($admin);

        $this->entityManager->flush();



        $this->client->loginUser($admin);


        $this->client->request('POST', '/api/store/' . $store->getId() . '/user/add', [], [], [], json_encode([
            'email' =>"admin@tiptop.com",
            'firstname' => 'st',
            'lastname' => 'Ur',
            'phone' => '123456789',
            'gender' => 'Homme',
            'dateOfBirth' => '01/01/2000',
            'role' => 'ROLE_STOREMANAGER',
        ]));



        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJson($this->client->getResponse()->getContent());
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('error', $responseData);

    }

    public function testAddNewUserToStoreError1(): void
    {
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

        $this->entityManager->persist($store);
        $this->entityManager->flush();

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
        $store->addUser($admin);
        $admin->addStore($store);


        $this->entityManager->persist($store);
        $this->entityManager->persist($admin);

        $this->entityManager->flush();

        $this->client->loginUser($admin);


        $this->client->request('POST', '/api/store/' . $store->getId() . '/user/add', [], [], [], json_encode([
            'email' => $this->generateUniqueEmail(),
            'firstname' => 'Test',
            'lastname' => 'User',
            'phone' => '123456789',
            'gender' => 'Homme',
            'dateOfBirth' => '01/01/2000',
            'status' => true
        ]));



        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertJson($this->client->getResponse()->getContent());
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('error', $responseData);


    }


    private function generateUniqueEmail(): string
    {
        return 'usertest' . uniqid() . '@tiptop.com';
    }


}
