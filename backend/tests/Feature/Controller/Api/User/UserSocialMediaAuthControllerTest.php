<?php

namespace App\Tests\Feature\Controller\Api\User;

use App\Controller\Api\User\UserSocialMediaAuthController;
use App\Entity\Role;
use App\Entity\User;
use League\OAuth2\Client\Provider\Google;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserSocialMediaAuthControllerTest extends WebTestCase
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

    public function testGoogleCallbackNewUserFailed(): void
    {
        $this->client->request('GET', '/api/oauth/google/callback', ['code' => 'test']);
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testGenerateRandomPassword(): void
    {
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $controller = new UserSocialMediaAuthController($entityManager , $this->passwordEncoder);
        $password = $controller->generateRandomPassword();

        $this->assertIsString($password);
        $this->assertEquals(8, strlen($password));
    }





}
