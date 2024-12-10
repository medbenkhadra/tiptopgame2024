<?php

namespace App\Tests\Feature\Controller\Api\User;

use App\Controller\Api\User\UserController;
use App\Entity\Store;
use App\Entity\User;
use App\Entity\Role;
use App\Entity\UserPersonalInfo;
use App\Repository\UserRepository;
use App\Repository\StoreRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserAuthControllerTest extends WebTestCase
{
    private $client;

    private $passwordEncoder;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->passwordEncoder = $this->client->getContainer()->get(UserPasswordHasherInterface::class);
    }



    private function generateUniqueEmail(): string
    {
        return 'test' . uniqid() . '@test.com';
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
    public function testCheckLoginAdmin(): void
    {
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        $email = $this->generateUniqueEmail();
        $admin = $this->createUser($email, 'password');

        $admin->setRole($entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_ADMIN']));
        $entityManager->persist($admin);
        $entityManager->flush();



        $requestData = [
            'email' => $email,
            'password' => 'password',
        ];

        $this->client->request(
            'POST',
            '/api/login_check_admin',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('success', $responseData['status']);
        $this->assertArrayHasKey('userJson', $responseData);
        $this->assertArrayHasKey('token', $responseData);

        $this->assertEquals($admin->getId(), $responseData['userJson']['id']);
        $this->assertEquals($admin->getEmail(), $responseData['userJson']['email']);
        $this->assertEquals($admin->getLastname(), $responseData['userJson']['lastname']);
        $this->assertEquals($admin->getFirstname(), $responseData['userJson']['firstname']);
        $this->assertEquals($admin->getGender(), $responseData['userJson']['gender']);

        $this->assertNotNull($responseData['token']);
    }


    public function testCheckLoginAdminAsClient(): void
    {
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        $email = $this->generateUniqueEmail();
        $admin = $this->createUser($email, 'password');

        $admin->setRole($entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_CLIENT']));
        $entityManager->persist($admin);
        $entityManager->flush();



        $requestData = [
            'email' => $email,
            'password' => 'password',
        ];

        $this->client->request(
            'POST',
            '/api/login_check_admin',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
        $this->assertArrayHasKey('message', $responseData);

        $this->assertEquals('Authentication failed', $responseData['error']);
        $this->assertEquals('user is not an admin', $responseData['message']);
    }

    public function testCheckLoginAdminError(): void
    {
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        $email = $this->generateUniqueEmail();
        $admin = $this->createUser($email, 'password');

        $admin->setRole($entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_ADMIN']));
        $entityManager->persist($admin);
        $entityManager->flush();



        $requestData = [
            'email' => $email,
            'password' => 'incorrect',
        ];

        $this->client->request(
            'POST',
            '/api/login_check_admin',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
        $this->assertArrayHasKey('credentials', $responseData);


        $requestData = [
            'email' => "incorrect_email",
            'password' => 'incorrect',
        ];

        $this->client->request(
            'POST',
            '/api/login_check_admin',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
        $this->assertArrayHasKey('message', $responseData);


        $this->client->request(
            'POST',
            '/api/login_check_admin',
            [],
            [],
            [],
            json_encode(['invalid_data'])
        );


        $this->assertEquals(500, $this->client->getResponse()->getStatusCode());
    }


    /*
    public function testCheckLoginClient(): void
    {
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        $email = $this->generateUniqueEmail();
        $client = $this->createUser($email, 'password');

        $client->setRole($entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_CLIENT']));
        $entityManager->persist($client);
        $entityManager->flush();

        $requestData = [
            'email' => $email,
            'password' => 'password',
        ];

        $this->client->request(
            'POST',
            '/api/login_check_client',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('success', $responseData['status']);
        $this->assertArrayHasKey('userJson', $responseData);
        $this->assertArrayHasKey('token', $responseData);


    }

    public function testCheckLoginClientAsAdmin(): void
    {
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        $email = $this->generateUniqueEmail();
        $client = $this->createUser($email, 'password');

        $client->setRole($entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_ADMIN']));
        $entityManager->persist($client);
        $entityManager->flush();

        $requestData = [
            'email' => $email,
            'password' => 'password',
        ];

        $this->client->request(
            'POST',
            '/api/login_check_client',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
        $this->assertArrayHasKey('message', $responseData);

        $this->assertEquals('Authentication failed', $responseData['error']);
        $this->assertEquals('user is not a client', $responseData['message']);



    }

    public function testCheckLoginClientError(): void
    {
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        $email = $this->generateUniqueEmail();
        $client = $this->createUser($email, 'password');

        $client->setRole($entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_CLIENT']));
        $entityManager->persist($client);
        $entityManager->flush();

        $requestData = [
            'email' => $email,
            'password' => 'incorrect',
        ];

        $this->client->request(
            'POST',
            '/api/login_check_client',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);

        $requestData = [
            'email' => "incorrect_email",
            'password' => 'incorrect',
        ];

        $this->client->request(
            'POST',
            '/api/login_check_client',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());


        $this->client->request(
            'POST',
            '/api/login_check_client',
            [],
            [],
            [],
            json_encode(['invalid_data'])
        );

        $this->assertEquals(500, $this->client->getResponse()->getStatusCode());

    }
        */


    public function testRegisterValid(): void
    {
        $requestData = [
            'email' => $this->generateUniqueEmail(),
            'password' => 'testpassword',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'gender' => 'Male',
            'role' => 'ROLE_CLIENT',
            'dateOfBirth' => '01/01/1990',
        ];

        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('success', $responseData['status']);
        $this->assertEquals('User registered successfully', $responseData['message']);
    }

    public function testRegisterSameEmailError(): void
    {
        $email = $this->generateUniqueEmail();
        $this->createUser($email, 'password');

        $requestData = [
            'email' => $email,
            'password' => 'testpassword',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'gender'=> 'Homme',
            'role' => 'ROLE_CLIENT',
            'dateOfBirth' => '01/01/1990',
            'phone' => '123456789',
            'status' => '1',
            ];

        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
        $this->assertEquals('Email already registered', $responseData['error']);


    }

    public function testRegisterMissingFields(): void
    {
        $requestData = [
            'email' => $this->generateUniqueEmail(),
            'password' => '',
            'firstname' => '',
            'lastname' => '',
            'gender' => '',
        ];

        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testRegisterExistingEmail(): void
    {
        $email = $this->generateUniqueEmail();
        $existingUser = $this->createUser($email, 'password');

        $requestData = [
            'email' => $email,
            'password' => 'testpassword',
            'firstname' => 'John',
            'lastname' => 'Doe',
        ];

        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }



    public function testResetPasswordRequestExistingUser(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password');
        $user->setIsActive(true);
        $user->setRole($this->client->getContainer()->get('doctrine')->getRepository(Role::class)->findOneBy(['name' => 'ROLE_CLIENT']));
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());
        $user->setDateOfBirth(new \DateTime());
        $user->setFirstName('Test');
        $user->setLastName('User');
        $user->setGender('Homme');
        $user->setPhone('123456789');
        $user->setStatus(true);
        $this->client->getContainer()->get('doctrine')->getManager()->persist($user);
        $this->client->getContainer()->get('doctrine')->getManager()->flush();

        $requestData = [
            'email' => 'test@example.com',
        ];

        $this->client->request(
            'POST',
            '/api/reset_password_request',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('success', $responseData['status']);
        $this->assertEquals('Reset password email sent', $responseData['message']);

    }

    public function testResetPasswordRequestNonExistingUser(): void
    {
        $requestData = [
            'email' => 'nonexistent@example.com',
        ];

        $this->client->request(
            'POST',
            '/api/reset_password_request',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('failed', $responseData['status']);
        $this->assertEquals('User not found', $responseData['message']);
    }

    public function testResetPasswordRequestMissingFields(): void
    {
        $requestData = [

        ];

        $this->client->request(
            'POST',
            '/api/reset_password_request',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }




}
