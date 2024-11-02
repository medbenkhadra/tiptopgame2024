<?php

namespace App\Tests\Feature\Controller\Api\EmailService;

use App\Entity\Role;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EmailServiceControllerTest extends WebTestCase
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

    public function testGetEmailServices(): void
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

        $this->client->request('GET', '/api/admin/correspondence_services');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $this->assertIsArray(json_decode($this->client->getResponse()->getContent(), true));

        $this->assertArrayHasKey('id', json_decode($this->client->getResponse()->getContent(), true)[0]);
        $this->assertArrayHasKey('name', json_decode($this->client->getResponse()->getContent(), true)[0]);
        $this->assertArrayHasKey('label', json_decode($this->client->getResponse()->getContent(), true)[0]);
        $this->assertArrayHasKey('description', json_decode($this->client->getResponse()->getContent(), true)[0]);
        $this->assertArrayHasKey('templates', json_decode($this->client->getResponse()->getContent(), true)[0]);

        $this->assertGreaterThan(0, count(json_decode($this->client->getResponse()->getContent(), true)));
    }
}
