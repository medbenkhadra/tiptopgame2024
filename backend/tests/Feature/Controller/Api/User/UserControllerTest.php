<?php

namespace App\Tests\Feature\Controller\Api\User;

use App\Controller\Api\User\UserController;
use App\Entity\Role;
use App\Entity\Store;
use App\Entity\User;
use App\Entity\UserPersonalInfo;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserControllerTest extends WebTestCase
{
    private $client;

    private $passwordEncoder;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->passwordEncoder = $this->client->getContainer()->get(UserPasswordHasherInterface::class);
    }

    public function testGetUserProfileById(): void
    {
        $this->client->request('GET', '/api/user/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());


        $this->client->request('GET' , '/api/user/9999');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());


    }


    public function testUpdateUserProfileById(): void
    {
        $email= $this->generateUniqueEmail();
        $user = $this->createUser($email, 'password');

        $store = new Store();
        $store->setId(1);
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



        $role = new Role();
        $role->setName('ROLE_CLIENT');
        $role->setLabel('Client');
        $user->setRole($role);



        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        $store->addUser($user);
        $user->addStore($store);
        $entityManager->persist($store);
        $entityManager->persist($role);
        $entityManager->persist($user);
        $entityManager->flush();

        $storeManager = $this->createUser($this->generateUniqueEmail(), 'password');
        $storeManager->setRole($this->client->getContainer()->get('doctrine')->getRepository(Role::class)->findOneBy(['name' => 'ROLE_STOREMANAGER']));

        $entityManager->persist($storeManager);
        $entityManager->flush();



        $this->client->loginUser($storeManager);

        $email= $this->generateUniqueEmail();

        $this->client->request(
            'POST',
            '/api/user/' . $user->getId() . '/update',
            [],
            [],
            [],
            json_encode([
                'firstname' => 'Updated firstname',
                'lastname' => 'Updated lastname',
                'email' => $email,
                'phone' => '987654321',
                'status' => false,
                'gender' => 'Homme'
            ])
        );


        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $updatedUser = $entityManager->getRepository(User::class)->findOneBy(['id' => $user->getId()]);
        $this->assertEquals('Updated firstname', $updatedUser->getFirstName());
        $this->assertEquals('Updated lastname', $updatedUser->getLastName());
        $this->assertEquals($email, $updatedUser->getEmail());
        $this->assertEquals('987654321', $updatedUser->getPhone());
        $this->assertEquals("Homme", $updatedUser->getGender());
    }


    public function testUpdateUserProfileByIdErrorCase1(): void
    {
        $user = new User();
        $user->setFirstName('Amine');
        $user->setLastName('AMMAR');
        $user->setEmail('test@test.com');
        $user->setPhone('123456789');
        $user->setStatus(true);
        $user->setGender('Homme');
        $user->setPassword('password');
        $user->setIsActive(true);
        $user->setDateOfBirth(new \DateTime('1990-01-01'));
        $user->setCreatedAt(new \DateTime('2021-01-01'));
        $user->setUpdatedAt(new \DateTime('2021-01-01'));
        $role = new Role();
        $role->setName('ROLE_CLIENT');
        $role->setLabel('Client');
        $user->setRole($role);

        $store = new Store();
        $store->setId(1);
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

        $user->addStore($store);
        $store->addUser($user);





        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($role);
        $entityManager->persist($user);
        $entityManager->persist($store);
        $entityManager->flush();

        $email= $this->generateUniqueEmail();
        $this->client->loginUser($user);
        $this->client->request(
            'POST',
            '/api/user/' . $user->getId() . '/update',
            [],
            [],
            [],
            json_encode([
                'firstname' => 'Updated firstname',
                'lastname' => 'Updated lastname',
                'email' => $email,
                'phone' => '987654321',
                'status' => false,
                'gender' => 'Homme'
            ])
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $updatedUser = $entityManager->getRepository(User::class)->findOneBy(['id' => $user->getId()]);
        $this->assertEquals('Updated firstname', $updatedUser->getFirstName());
        $this->assertEquals('Updated lastname', $updatedUser->getLastName());
        $this->assertEquals($email, $updatedUser->getEmail());
        $this->assertEquals('987654321', $updatedUser->getPhone());
        $this->assertEquals("Homme", $updatedUser->getGender());
    }





    public function testGetClients(): void
    {
        $admin = new User();
        $admin->setEmail('admin@tiptop.com');
        $admin->setPassword('password');
        $admin->setIsActive(true);
        $admin->setRole($this->client->getContainer()->get('doctrine')->getRepository(Role::class)->findOneBy(['name' => 'ROLE_ADMIN']));
        $admin->setCreatedAt(new \DateTime('2021-01-01'));
        $admin->setUpdatedAt(new \DateTime('2021-01-01'));
        $admin->setDateOfBirth(new \DateTime('1990-01-01'));
        $admin->setFirstName('Admin');
        $admin->setLastName('Admin');
        $admin->setGender('Homme');
        $admin->setPhone('123456789');
        $admin->setStatus(true);


        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($admin);
        $entityManager->flush();

        $this->client->loginUser($admin);

        $params = [
            'firstname' => 'amine',
            'lastname' => 'ammar',
            'status' => true,
            'store' => 1,
            'page' => 1,
            'limit' => 10,
            'email' => 'test@test.com',
            'gender' => 'Homme',
        ];

        $url = '/api/admin/clients?' . http_build_query($params);
        $this->client->request('GET', $url);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }


    public function testGetStoreClients(): void
    {
        $store = new Store();
        $store->setName('Store');
        $store->setAddress('Address');
        $store->setHeadquartersAddress('Headquarters Address');
        $store->setEmail('store@store.com');
        $store->setPostalCode('12345');
        $store->setCity('City');
        $store->setCountry('Country');
        $store->setCapital(1000);
        $store->setStatus(true);
        $store->setSiren('123456789');



        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($store);
        $entityManager->flush();

        $user = new User();
        $user->setEmail('client@client.com');
        $user->setPassword('password');
        $user->setIsActive(true);
        $user->setCreatedAt(new \DateTime('2021-01-01'));
        $user->setUpdatedAt(new \DateTime('2021-01-01'));
        $user->setDateOfBirth(new \DateTime('1990-01-01'));
        $user->setFirstName('Client');
        $user->setLastName('Client');
        $user->setGender('Homme');
        $user->setPhone('123456789');
        $user->setStatus(true);

        $role = new Role();
        $role->setName('ROLE_CLIENT');
        $role->setLabel('Client');
        $user->setRole($role);
        $store->addUser($user);
        $user->addStore($store);

        $entityManager->persist($role);
        $entityManager->persist($store);

        $entityManager->persist($user);

        $storeId = $store->getId();




        $admin = new User();
        $admin->setEmail('admin@tiptop.com');
        $admin->setPassword('password');
        $admin->setIsActive(true);
        $admin->setRole($this->client->getContainer()->get('doctrine')->getRepository(Role::class)->findOneBy(['name' => 'ROLE_ADMIN']));
        $admin->setCreatedAt(new \DateTime('2021-01-01'));
        $admin->setUpdatedAt(new \DateTime('2021-01-01'));
        $admin->setDateOfBirth(new \DateTime('1990-01-01'));
        $admin->setFirstName('Admin');
        $admin->setLastName('Admin');
        $admin->setGender('Homme');
        $admin->setPhone('123456789');
        $admin->setStatus(true);

        $admin->addStore($store);
        $store->addUser($admin);
        $entityManager->persist($store);


        $entityManager->persist($admin);

        $entityManager->flush();


        $this->client->loginUser($admin);

        $this->client->request('GET', '/api/store/' . $storeId . '/clients');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

    }


    public function testGetStoreClientsStoreNotExists(): void
    {
        $store = new Store();
        $store->setName('Store');
        $store->setAddress('Address');
        $store->setHeadquartersAddress('Headquarters Address');
        $store->setEmail('store@store.com');
        $store->setPostalCode('12345');
        $store->setCity('City');
        $store->setCountry('Country');
        $store->setCapital(1000);
        $store->setStatus(true);
        $store->setSiren('123456789');



        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($store);
        $entityManager->flush();

        $user = new User();
        $user->setEmail('client@client.com');
        $user->setPassword('password');
        $user->setIsActive(true);
        $user->setCreatedAt(new \DateTime('2021-01-01'));
        $user->setUpdatedAt(new \DateTime('2021-01-01'));
        $user->setDateOfBirth(new \DateTime('1990-01-01'));
        $user->setFirstName('Client');
        $user->setLastName('Client');
        $user->setGender('Homme');
        $user->setPhone('123456789');
        $user->setStatus(true);

        $role = new Role();
        $role->setName('ROLE_CLIENT');
        $role->setLabel('Client');
        $user->setRole($role);
        $store->addUser($user);
        $user->addStore($store);

        $entityManager->persist($role);
        $entityManager->persist($store);

        $entityManager->persist($user);

        $storeId = $store->getId();




        $admin = new User();
        $admin->setEmail('admin@tiptop.com');
        $admin->setPassword('password');
        $admin->setIsActive(true);
        $admin->setRole($this->client->getContainer()->get('doctrine')->getRepository(Role::class)->findOneBy(['name' => 'ROLE_ADMIN']));
        $admin->setCreatedAt(new \DateTime('2021-01-01'));
        $admin->setUpdatedAt(new \DateTime('2021-01-01'));
        $admin->setDateOfBirth(new \DateTime('1990-01-01'));
        $admin->setFirstName('Admin');
        $admin->setLastName('Admin');
        $admin->setGender('Homme');
        $admin->setPhone('123456789');
        $admin->setStatus(true);

        $admin->addStore($store);
        $store->addUser($admin);
        $entityManager->persist($store);


        $entityManager->persist($admin);

        $entityManager->flush();


        $this->client->loginUser($admin);

        $this->client->request('GET', '/api/store/9999999/clients');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());

    }


    public function testGetParticipants(): void
    {

        $params = [
            'firstname' => 'Amine',
            'lastname' => 'AMMAR',
            'status' => true,
            'store' => 1,
            'page' => 1,
            'limit' => 10,
            'email' => 'test@test.com',
            'genre' => 'Homme',
        ];

        $admin = new User();
        $admin->setEmail('admin@tiptop.com');
        $admin->setPassword('password');
        $admin->setIsActive(true);
        $admin->setRole($this->client->getContainer()->get('doctrine')->getRepository(Role::class)->findOneBy(['name' => 'ROLE_ADMIN']));
        $admin->setCreatedAt(new \DateTime('2021-01-01'));
        $admin->setUpdatedAt(new \DateTime('2021-01-01'));
        $admin->setDateOfBirth(new \DateTime('1990-01-01'));
        $admin->setFirstName('Admin');
        $admin->setLastName('Admin');
        $admin->setGender('Homme');
        $admin->setPhone('123456789');
        $admin->setStatus(true);




        $url = '/api/admin/participants?' . http_build_query($params);

        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($admin);
        $entityManager->flush();

        $this->client->loginUser($admin);
        $this->client->request('GET', $url);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

    }



    public function testGetParticipantsList(): void
    {
        $params = [
            'store' => 1,
            'employee' => 2,
        ];

        $store = new Store();
        $store->setId(1);
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



        $employee = new User();
        $employee->setId(2);
        $employee->setEmail('employee@employee.com');
        $employee->setPassword('password');
        $employee->setIsActive(true);
        $employee->setRole($this->client->getContainer()->get('doctrine')->getRepository(Role::class)->findOneBy(['name' => 'ROLE_EMPLOYEE']));
        $employee->setCreatedAt(new \DateTime('2021-01-01'));
        $employee->setUpdatedAt(new \DateTime('2021-01-01'));
        $employee->setDateOfBirth(new \DateTime('1990-01-01'));
        $employee->setFirstName('Employee');
        $employee->setLastName('Employee');
        $employee->setGender('Homme');
        $employee->setPhone('123456789');
        $employee->setStatus(true);

        $store->addUser($employee);
        $employee->addStore($store);




        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($store);
        $entityManager->persist($employee);
        $entityManager->flush();

        $url = '/api/participants?' . http_build_query($params);

        $this->client->loginUser($employee);

        $this->client->request('GET', $url);



        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $content = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('users', $content);

    }


    public function testGetEmployeesList()
    {
        $params = [
            'store' => 1,
            'client' => 5,
        ];

        $store = new Store();
        $store->setId(1);
        $store->setName('Store');
        $store->setAddress('Address');
        $store->setHeadquartersAddress('Headquarters Address');
        $store->setEmail('store@store.com');
        $store->setPostalCode('12345');
        $store->setCity('City');
        $store->setCountry('Country');
        $store->setCapital(1000);
        $store->setStatus(true);
        $store->setSiren('123456789');


        $client = new User();
        $client->setId(5);
        $client->setEmail('client@client.com');
        $client->setPassword('password');
        $client->setIsActive(true);
        $client->setRole($this->client->getContainer()->get('doctrine')->getRepository(Role::class)->findOneBy(['name' => 'ROLE_CLIENT']));
        $client->setCreatedAt(new \DateTime('2021-01-01'));
        $client->setUpdatedAt(new \DateTime('2021-01-01'));
        $client->setDateOfBirth(new \DateTime('1990-01-01'));
        $client->setFirstName('Client');
        $client->setLastName('Client');
        $client->setGender('Homme');
        $client->setPhone('123456789');
        $client->setStatus(true);

        $store->addUser($client);
        $client->addStore($store);



        $employee = new User();
        $employee->setId(2);
        $employee->setEmail('employee@employee.com');
        $employee->setPassword('password');
        $employee->setIsActive(true);
        $employee->setRole($this->client->getContainer()->get('doctrine')->getRepository(Role::class)->findOneBy(['name' => 'ROLE_EMPLOYEE']));
        $employee->setCreatedAt(new \DateTime('2021-01-01'));
        $employee->setUpdatedAt(new \DateTime('2021-01-01'));
        $employee->setDateOfBirth(new \DateTime('1990-01-01'));
        $employee->setFirstName('Employee');
        $employee->setLastName('Employee');
        $employee->setGender('Homme');
        $employee->setPhone('123456789');
        $employee->setStatus(true);

        $employee2 = new User();
        $employee2->setId(3);
        $employee2->setEmail('employee2@employee.fr');
        $employee2->setPassword('password');
        $employee2->setIsActive(true);
        $employee2->setRole($this->client->getContainer()->get('doctrine')->getRepository(Role::class)->findOneBy(['name' => 'ROLE_EMPLOYEE']));
        $employee2->setCreatedAt(new \DateTime('2021-01-01'));
        $employee2->setUpdatedAt(new \DateTime('2021-01-01'));
        $employee2->setDateOfBirth(new \DateTime('1990-01-01'));
        $employee2->setFirstName('Employee2');
        $employee2->setLastName('Employee2');
        $employee2->setGender('Homme');
        $employee2->setPhone('123456789');
        $employee2->setStatus(true);

        $store->addUser($employee2);
        $employee2->addStore($store);



        $store->addUser($employee);
        $employee->addStore($store);

        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($store);
        $entityManager->persist($client);
        $entityManager->persist($employee);
        $entityManager->persist($employee2);
        $entityManager->flush();

        $url = '/api/employees?' . http_build_query($params);

        $this->client->loginUser($employee);

        $this->client->request('GET', $url);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }


    public function testGetUserPersonalInfoById(): void
    {
        $user = new User();
        $user->setFirstName('Amine');
        $user->setLastName('AMMAR');
        $user->setEmail('test@test.com');
        $user->setPhone('123456789');
        $user->setStatus(true);
        $user->setGender('Homme');
        $user->setPassword('password');
        $user->setIsActive(true);
        $user->setDateOfBirth(new \DateTime('1990-01-01'));
        $user->setCreatedAt(new \DateTime('2021-01-01'));
        $user->setUpdatedAt(new \DateTime('2021-01-01'));
        $role = new Role();
        $role->setName('ROLE_CLIENT');
        $role->setLabel('Client');
        $user->setRole($role);

        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($role);
        $entityManager->persist($user);
        $entityManager->flush();

        $this->client->request('GET', '/api/user/' . $user->getId() . '/personal_info');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', '/api/user/9999/personal_info');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());


    }


    public function testUpdateUserProfileInfo(): void
    {

        $user = new User();
        $user->setFirstName('Amine');
        $user->setLastName('AMMAR');
        $user->setEmail('test@test.com');
        $user->setPhone('123456789');
        $user->setStatus(true);
        $user->setGender('Homme');
        $user->setPassword('password');
        $user->setIsActive(true);
        $user->setDateOfBirth(new \DateTime('1990-01-01'));
        $user->setCreatedAt(new \DateTime('2021-01-01'));
        $user->setUpdatedAt(new \DateTime('2021-01-01'));
        $role = new Role();
        $role->setName('ROLE_CLIENT');
        $role->setLabel('Client');
        $user->setRole($role);

        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($role);
        $entityManager->persist($user);
        $entityManager->flush();

        $params = [
            'firstname' => 'Updated firstname',
            'lastname' => 'Updated lastname',
            'phone' => '987654321',
            'address' => 'Address',
            'postalCode' => '12345',
            'city' => 'City',
            'country' => 'Country'
        ];


        $this->client->request(
            'POST',
            '/api/user/' . $user->getId() . '/update_profile_info',
            [],
            [],
            [],
            json_encode($params)
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());


        $user = new User();
        $user->setFirstName('Amine');
        $user->setLastName('AMMAR');
        $user->setEmail('test@test.com');
        $user->setPhone('123456789');
        $user->setStatus(true);
        $user->setGender('Homme');
        $user->setPassword('password');
        $user->setIsActive(true);
        $user->setDateOfBirth(new \DateTime('1990-01-01'));
        $user->setCreatedAt(new \DateTime('2021-01-01'));
        $user->setUpdatedAt(new \DateTime('2021-01-01'));
        $role = new Role();
        $role->setName('ROLE_CLIENT');
        $role->setLabel('Client');
        $user->setRole($role);

        $entityManager->persist($role);
        $entityManager->persist($user);

        $userPersonalInfo = new UserPersonalInfo();
        $userPersonalInfo->setId(1);
        $userPersonalInfo->setAddress('Address');
        $userPersonalInfo->setPostalCode('12345');
        $userPersonalInfo->setCity('City');
        $userPersonalInfo->setCountry('Country');
        $user->setUserPersonalInfo($userPersonalInfo);


        $entityManager->persist($userPersonalInfo);
        $entityManager->flush();

        $this->client->request(
            'POST',
            '/api/user/' . $user->getId() . '/update_profile_info',
            [],
            [],
            [],
            json_encode($params)
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }


    public function testUpdateUserAvatar()
    {
        $client = new User();
        $client->setId(1);
        $client->setEmail('client@client.com');
        $client->setPassword('password');
        $client->setIsActive(true);
        $client->setRole($this->client->getContainer()->get('doctrine')->getRepository(Role::class)->findOneBy(['name' => 'ROLE_CLIENT']));
        $client->setCreatedAt(new \DateTime('2021-01-01'));
        $client->setUpdatedAt(new \DateTime('2021-01-01'));
        $client->setDateOfBirth(new \DateTime('1990-01-01'));
        $client->setFirstName('Client');
        $client->setLastName('Client');
        $client->setGender('Homme');
        $client->setPhone('123456789');
        $client->setStatus(true);

        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($client);
        $entityManager->flush();

        $this->client->loginUser($client);

        $file = tempnam(sys_get_temp_dir(), 'avatar');

        $this->client->request(
            'POST',
            '/api/user/' . $client->getId() . '/update_avatar',
            [],
            ['avatar_file' => new UploadedFile($file, 'test_avatar.png')]
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

    }

    public function testUpdateUserAvatarHandleErrors()
    {
        $client = new User();
        $client->setId(1);
        $client->setEmail('client@client.com');
        $client->setPassword('password');
        $client->setIsActive(true);
        $client->setRole($this->client->getContainer()->get('doctrine')->getRepository(Role::class)->findOneBy(['name' => 'ROLE_CLIENT']));
        $client->setCreatedAt(new \DateTime('2021-01-01'));
        $client->setUpdatedAt(new \DateTime('2021-01-01'));
        $client->setDateOfBirth(new \DateTime('1990-01-01'));
        $client->setFirstName('Client');
        $client->setLastName('Client');
        $client->setGender('Homme');
        $client->setPhone('123456789');
        $client->setStatus(true);

        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($client);
        $entityManager->flush();

        $this->client->loginUser($client);

        $this->client->request(
            'POST',
            '/api/user/' . $client->getId() . '/update_avatar',
            [],
            []
        );

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());

    }


    public function testDeleteAvatarFile(): void
    {


        $kernelProjectDir = $this->client->getContainer()->getParameter('kernel.project_dir');

        $avatarsUploadDir = $kernelProjectDir . '/public/avatars';

        $filePath = $avatarsUploadDir . '/test_avatar.png';

        if(!file_exists($avatarsUploadDir)){
            mkdir($avatarsUploadDir);
        }

        if(!file_exists($filePath)){
            $originalFile = $avatarsUploadDir . '/original_test.png';
            copy($originalFile, $filePath);
        }



        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $passwordEncoder = $this->client->getContainer()->get(UserPasswordHasherInterface::class);
        $userServices = $this->client->getContainer()->get('App\Service\User\UserService');

        $userController = new UserController($entityManager, $passwordEncoder, $userServices);

        $userController->setContainer($this->client->getContainer());

        $userController->deleteAvatarFile('/test_avatar.png');

        $this->assertFalse(file_exists($filePath));
    }


    public function testUpdateUserPasswordSuccess(): void
    {

        $user = $this->createUser('test@test.com', 'password');
        $requestData = [
            'current_password' => 'password',
            'new_password' => 'new_password',
            'new_password_confirm' => 'new_password',
        ];
        $this->sendUpdatePasswordRequest($this->client, $user->getId(), $requestData);
        $this->assertTrue($this->client->getResponse()->isSuccessful());


    }

    public function testUpdateUserPasswordErrorCase1(): void
    {

        $user = $this->createUser('test@test.com', 'password');


        // Test: Providing a new password less than 8 characters
        $requestData['current_password'] = 'password';
        $requestData['new_password'] = 'short';
        $requestData['new_password_confirm'] = 'short';
        $this->sendUpdatePasswordRequest($this->client, $user->getId(), $requestData);
        $this->assertSame(400, $this->client->getResponse()->getStatusCode());
    }

    public function testUpdateUserPasswordErrorCase2(): void
    {

        $user = $this->createUser('test@test.com', 'password');
        $requestData['current_password'] = 'incorrect_password';
        $requestData['new_password'] = 'new_password';
        $requestData['new_password_confirm'] = 'new_password';
        $this->sendUpdatePasswordRequest($this->client, $user->getId(), $requestData);
        $this->assertSame(400, $this->client->getResponse()->getStatusCode());
    }

    public function testUpdateUserPasswordErrorCase3(): void
    {

        $user = $this->createUser('test@test.com', 'password');
        $requestData['current_password'] = 'password';
        $requestData['new_password'] = 'password';
        $requestData['new_password_confirm'] = 'password';
        $this->sendUpdatePasswordRequest($this->client, $user->getId(), $requestData);
        $this->assertSame(400, $this->client->getResponse()->getStatusCode());
    }

    public function testUpdateUserPasswordErrorCase4(): void
    {
        $user = $this->createUser('test@test.com', 'password');

        $requestData['current_password'] = 'password';
        $requestData['new_password'] = 'new_password';
        $requestData['new_password_confirm'] = 'different_password';
        $this->sendUpdatePasswordRequest($this->client, $user->getId(), $requestData);
        $this->assertSame(400, $this->client->getResponse()->getStatusCode());
    }

    public function testUpdateUserPasswordErrorCase5(): void
    {
        $requestData['current_password'] = 'password';
        $requestData['new_password'] = 'new_password';
        $requestData['new_password_confirm'] = 'different_password';
        $this->sendUpdatePasswordRequest($this->client, 999999, $requestData);
        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }





        private function createUser(string $email, string $password): User
    {
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->passwordEncoder->hashPassword($user, $password));
        $user->setIsActive(true);
        $user->setRole($entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_CLIENT']));
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());
        $user->setDateOfBirth(new \DateTime());
        $user->setFirstName('Test');
        $user->setLastName('User');
        $user->setGender('Homme');
        $user->setPhone('123456789');
        $user->setStatus(true);
        $entityManager->persist($user);
        $entityManager->flush();
        return $user;
    }

    private function sendUpdatePasswordRequest($client, int $userId, array $requestData): void
    {
        $url = '/api/user/' . $userId . '/update_password';
        $client->request('POST', $url, [], [], [], json_encode($requestData));
    }


    //updateUserEmail test
    public function testUpdateUserEmailSuccess(): void
    {
        $user = $this->createUser($this->generateUniqueEmail(), 'password');
        $requestData = [
            'new_email' => $this->generateUniqueEmail(),
            'current_password' => 'password',
        ];

        $this->sendUpdateEmailRequest($this->client, $user->getId(), $requestData);
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testUpdateUserEmailErrorCase1(): void
    {
        $user = $this->createUser('test@test.com', 'password');
        $requestData = [
            'new_email' => 'new@email.com',
            'current_password' => 'incorrect_password',
        ];

        $this->sendUpdateEmailRequest($this->client, $user->getId(), $requestData);
        $this->assertSame(400, $this->client->getResponse()->getStatusCode());
    }


    public function testUpdateUserEmailErrorCase2(): void
    {
        $user = $this->createUser('test@test.com', 'password');
        $user2 = $this->createUser('new@email.com', 'password');

        $requestData = [
            'new_email' => 'new@email.com',
            'current_password' => 'password',
        ];

        $this->sendUpdateEmailRequest($this->client, $user->getId(), $requestData);
        $this->assertSame(400, $this->client->getResponse()->getStatusCode());
    }


    public function testUpdateUserEmailErrorCase3(): void
    {
        $requestData = [
            'new_email' => 'new@email.com',
            'current_password' => 'incorrect_password',
        ];

        $this->sendUpdateEmailRequest($this->client, 9999999, $requestData);
        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    private function sendUpdateEmailRequest($client, int $userId, array $requestData): void
    {
        $url = '/api/user/' . $userId . '/update_email';
        $client->request('POST', $url, [], [], [], json_encode($requestData));
    }

    private function generateUniqueEmail(): string
    {
        return 'test' . uniqid() . '@test.com';
    }


    public function testGetUsers(): void
    {
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();

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


        $role = new Role();
        $role->setName(Role::ROLE_CLIENT);
        $role->setLabel('Store Manager');
        $entityManager->persist($role);



        $user = $this->createUser($this->generateUniqueEmail(), 'password');
        $user->setFirstName('Amine');
        $user->setLastName('AMMAR');
        $user->setRole($role);


        $storeManagerRole = new Role();
        $storeManagerRole->setName(Role::ROLE_STOREMANAGER);
        $storeManagerRole->setLabel('Store Manager');
        $entityManager->persist($storeManagerRole);

        $storeManager = $this->createUser($this->generateUniqueEmail(), 'password');

        $storeManager->setRole($storeManagerRole);

        $store->addUser($storeManager);
        $storeManager->addStore($store);

        $store->addUser($user);
        $user->addStore($store);



        $entityManager->persist($storeManager);
        $entityManager->persist($store);
        $entityManager->persist($user);
        $entityManager->flush();

        $params = [
            'store' => $store->getId(),
            'role' => $role->getName(),
        ];

        $this->client->loginUser($storeManager);
        $this->client->request('GET', '/api/users', $params);


        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }



    //test saveUserProfile
    public function testSaveUserProfile(): void
    {
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();

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


        $email = $this->generateUniqueEmail();
        $user = $this->createUser($email, 'password');


        $user->addStore($store);
        $store->addUser($user);

        $entityManager->persist($store);
        $entityManager->persist($user);
        $entityManager->flush();

        $requestData = [
            'email' => $email,
            'dateOfBirth' => '01/01/1990',
            'lastname' => 'Doe',
            'firstname' => 'John',
            'phone' => '123456789',
            'gender' => 'Male'
        ];

        $this->client->request(
            'POST',
            '/api/user/save_profile',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $updatedUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        $this->assertEquals('01/01/1990', $updatedUser->getDateOfBirth()->format('d/m/Y'));
        $this->assertEquals('Doe', $updatedUser->getLastName());
        $this->assertEquals('John', $updatedUser->getFirstName());
        $this->assertEquals('123456789', $updatedUser->getPhone());
        $this->assertEquals('Male', $updatedUser->getGender());
    }

    public function testSaveUserProfileErrorCase1(): void
    {
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();

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


        $email = $this->generateUniqueEmail();
        $user = $this->createUser($email, 'password');


        $user->addStore($store);
        $store->addUser($user);

        $entityManager->persist($store);
        $entityManager->persist($user);
        $entityManager->flush();

        $requestData = [
            'email' => $this->generateUniqueEmail(),
            'dateOfBirth' => '01/01/1990',
            'lastname' => 'Doe',
            'firstname' => 'John',
            'phone' => '123456789',
            'gender' => 'Male'
        ];

        $this->client->request(
            'POST',
            '/api/user/save_profile',
            [],
            [],
            [],
            json_encode($requestData)
        );


        $this->assertEquals(500, $this->client->getResponse()->getStatusCode());


    }
}
