<?php

namespace App\Tests\Feature\Controller\Api\EmailTemplate;

use App\Entity\EmailService;
use App\Entity\EmailTemplate;
use App\Entity\Role;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EmailTemplateControllerTest extends WebTestCase
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

    public function testGetEmailTemplates(): void
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
        $this->client->request('GET', '/api/admin/correspondence_templates');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $this->assertIsArray(json_decode($this->client->getResponse()->getContent(), true));

        $this->assertArrayHasKey('id', json_decode($this->client->getResponse()->getContent(), true)[0]);
        $this->assertArrayHasKey('title', json_decode($this->client->getResponse()->getContent(), true)[0]);
        $this->assertArrayHasKey('subject', json_decode($this->client->getResponse()->getContent(), true)[0]);
        $this->assertArrayHasKey('content', json_decode($this->client->getResponse()->getContent(), true)[0]);
        $this->assertArrayHasKey('type', json_decode($this->client->getResponse()->getContent(), true)[0]);
        $this->assertArrayHasKey('service', json_decode($this->client->getResponse()->getContent(), true)[0]);
        $this->assertArrayHasKey('required', json_decode($this->client->getResponse()->getContent(), true)[0]);
        $this->assertArrayHasKey('description', json_decode($this->client->getResponse()->getContent(), true)[0]);
        $this->assertArrayHasKey('name', json_decode($this->client->getResponse()->getContent(), true)[0]);
        $this->assertArrayHasKey('variables', json_decode($this->client->getResponse()->getContent(), true)[0]);

        $this->assertGreaterThan(0, count(json_decode($this->client->getResponse()->getContent(), true)));
    }


    public function testGetEmailTemplateById(): void
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
        $emailTemplate = $this->entityManager->getRepository(EmailTemplate::class)->findOneBy([]);

        $this->client->request('GET', '/api/admin/correspondence_template/' . $emailTemplate->getId());

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'id' => $emailTemplate->getId(),
                'title' => $emailTemplate->getTitle(),
                'subject' => $emailTemplate->getSubject(),
                'content' => $emailTemplate->getContent(),
                'type' => $emailTemplate->getType(),
                'service' => $emailTemplate->getService()->getId(),
                'required' => $emailTemplate->getRequired(),
                'description' => $emailTemplate->getDescription(),
                'name' => $emailTemplate->getName(),
                'variables' => $emailTemplate->getService()->getVariablesJson(),
            ]),
            $this->client->getResponse()->getContent()
        );
    }

    public function testGetEmailTemplateByIdError(): void
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

        $this->client->request('GET', '/api/admin/correspondence_template/99999999');


        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        $this->assertJsonStringEqualsJsonString(
            json_encode(['status' => 'EmailTemplate not found']),
            $this->client->getResponse()->getContent()
        );

    }


    public function testCreateEmailTemplate(): void
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
        $this->client->request(
            'POST',
            '/api/admin/correspondence_template/create',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'title' => 'Test Template',
                'name' => 'Test Name',
                'description' => 'Test Description',
                'type' => 'Test Type',
                'service' => $this->entityManager->getRepository(EmailService::class)->findOneBy([])->getId(),
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Template created successfully']),
            $this->client->getResponse()->getContent()
        );
    }


    public function testUpdateEmailTemplateById(): void
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

        $emailTemplate = $this->entityManager->getRepository(EmailTemplate::class)->findOneBy([]);

        $this->client->request(
            'POST',
            '/api/admin/correspondence_template/' . $emailTemplate->getId() . '/update',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'title' => 'Updated Test Template',
                'name' => 'Updated Test Name',
                'description' => 'Updated Test Description',
                'type' => 'Updated Test Type',
                'service' => $this->entityManager->getRepository(EmailService::class)->findOneBy([])->getId(),
                'subject' => 'Updated Test Subject',
                'content' => 'Updated Test Content',
                'required' => false,
            ])
        );

        $this->assertResponseIsSuccessful();

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertContains($responseContent['message'], [
            "Template a été mis à jour avec succès",
            "Template a été mis à jour avec succès. Il y a d'autres templates avec ce service, ils ont été mis à jour pour être non requis"
        ]);

        if ($responseContent['message'] === "Template a été mis à jour avec succès") {
            $this->assertSame($responseContent['statusCode'], 200);
        } else {
            $this->assertSame($responseContent['statusCode'], 201);
        }


    }


    public function testUpdateEmailTemplateById2(): void
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



        $this->client->request(
            'POST',
            '/api/admin/correspondence_template/99999999/update',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'title' => 'Updated Test Template',
                'name' => 'Updated Test Name',
                'description' => 'Updated Test Description',
                'type' => 'Updated Test Type',
                'service' => $this->entityManager->getRepository(EmailService::class)->findOneBy([])->getId(),
                'subject' => 'Updated Test Subject',
                'content' => 'Updated Test Content',
                'required' => false,
            ])
        );

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertSame($responseContent['status'], 'Template not found');

    }



    public function testDeleteTemplate(): void
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


        $emailTemplate = new EmailTemplate();
        $emailTemplate->setContent('Test Content');
        $emailTemplate->setSubject('Test Subject');
        $emailTemplate->setTitle('Test Title');
        $emailTemplate->setType('Test Type');
        $emailTemplate->setName('Test Name');
        $emailTemplate->setDescription('Test Description');
        $emailTemplate->setRequired(false);
        $emailTemplate->setService($this->entityManager->getRepository(EmailService::class)->findOneBy([]));



        $this->entityManager->persist($emailTemplate);
        $this->entityManager->flush();

        $emailTemplateId = $emailTemplate->getId();



        $this->client->request(
            'DELETE',
            '/api/admin/correspondence_template/delete/' . $emailTemplateId
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        $deletedTemplate = $this->entityManager->getRepository(EmailTemplate::class)->find($emailTemplateId);
        $this->assertNull($deletedTemplate);
    }

    public function testDeleteTemplate2(): void
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



        $this->client->request(
            'DELETE',
            '/api/admin/correspondence_template/delete/' . 99999999
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        $deletedTemplate = $this->entityManager->getRepository(EmailTemplate::class)->find(99999999);
        $this->assertNull($deletedTemplate);
    }
}
