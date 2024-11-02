<?php

namespace App\Tests\Feature\Controller\Api\Badge;

use App\Entity\Badge;
use App\Entity\Role;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class BadgeControllerTest extends WebTestCase
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

    public function testGetAllBadges(): void
    {
        $this->client->request('GET', '/api/badges');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('badges', $responseData);
        $this->assertNotEmpty($responseData['badges']);
    }

    public function testGetBadgeById(): void
    {
        $badge = new Badge();
        $badge->setName('Test Badge');
        $badge->setDescription('Test Badge Description');

        $this->entityManager->persist($badge);
        $this->entityManager->flush();



        $this->client->request('GET', '/api/badge/' . $badge->getId());


        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('id', $responseData);
        $this->assertArrayHasKey('name', $responseData);
        $this->assertArrayHasKey('description', $responseData);


        $this->client->request('GET', '/api/badge/' . 999999);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);



    }

    public function testGetClientBadges(): void
    {
        $badge = new Badge();
        $badge->setName('Test Badge');
        $badge->setDescription('Test Badge Description');

        $this->entityManager->persist($badge);

        $badge2 = new Badge();
        $badge2->setName('Test Badge 2');
        $badge2->setDescription('Test Badge Description 2');

        $this->entityManager->persist($badge2);

        $user = new User();
        $user->setEmail('client@tiptop.com');
        $user->setPassword($this->passwordEncoder->hashPassword($user, 'password'));
        $user->setIsActive(true);
        $user->setRole($this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_CLIENT']));
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());
        $user->setDateOfBirth(new \DateTime());
        $user->setFirstName('Test');
        $user->setLastName('User');
        $user->setGender('Homme');
        $user->setPhone('123456789');
        $user->setStatus(true);
        $this->entityManager->persist($user);


        $user->addBadge($badge);
        $user->addBadge($badge2);

        $badge->addUser($user);
        $badge2->addUser($user);


        $this->entityManager->persist($badge);
        $this->entityManager->persist($user);
        $this->entityManager->flush();


        $userId = $user->getId();



        $this->client->request('GET', '/api/client/badges/' . $userId);

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);


        $this->assertArrayHasKey('badges', $responseData);
    }
}
