<?php

namespace App\Tests\Feature\Controller\Api\PostManMailer;

use App\Entity\EmailService;
use App\Entity\Role;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PostManMailerControllerTest extends WebTestCase
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

      public function testSendActivationEmail(): void
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


        $this->client->request('POST', '/api/user/'.$admin->getId().'/send_activation_email', []);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }



    public function testSendActivationEmail2(): void
    {
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


        $this->client->request('POST', '/api/user/'.$admin->getId().'/send_activation_email', []);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testSendActivationEmailError(): void
    {
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


        $this->client->request('POST', '/api/user/999999999/send_activation_email', []);

        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);
    }


    public function testCheckClientActivationTokenValidity(): void
    {
        $email = $this->generateUniqueEmail();

        $admin = new User();
        $admin->setEmail($email);
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
        $admin->setToken('token');
        $tomorrow = new \DateTime();
        $tomorrow->modify('+1 day');
        $admin->setTokenExpiredAt($tomorrow);
        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $data = [
            'email' => $admin->getEmail(),
            'token' => $admin->getToken(),
        ];


        $this->client->request('POST', '/api/client/check_activation_token_validity', [], [], [], json_encode($data));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }


    public function testCheckClientActivationTokenValidity2(): void
    {
        $admin = new User();
        $admin->setEmail('clienttest@tiptop.com');
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
        $admin->setToken('token');
        $tomorrow = new \DateTime();
        $tomorrow->modify('+1 day');
        $admin->setTokenExpiredAt($tomorrow);
        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $data = [
            'email' => $admin->getEmail(),
            'token' => 'incorrect_token',
        ];

        $this->client->request('POST', '/api/client/check_activation_token_validity', [], [], [], json_encode($data));
        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    private function generateUniqueEmail(): string
    {
        return 'clienttest' . rand(0, 999999) . '@tiptop.com';
    }

}
