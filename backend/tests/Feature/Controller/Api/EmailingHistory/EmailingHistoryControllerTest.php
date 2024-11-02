<?php


namespace App\Tests\Feature\Controller\Api\EmailingHistory;


use App\Entity\EmailingHistory;
use App\Entity\EmailService;
use App\Entity\Role;
use App\Entity\Store;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EmailingHistoryControllerTest extends WebTestCase
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

    public function testGetEmailingHistory(): void
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


        $store = new Store();
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


        $this->entityManager->persist($store);
        $this->entityManager->flush();

        $admin->addStore($store);
        $store->addUser($admin);

        $this->entityManager->persist($admin);
        $this->entityManager->persist($store);
        $this->entityManager->flush();

        $service = $this->entityManager->getRepository(EmailService::class)->findOneBy([]);

        if(!$service) {
            $service = new EmailService();
            $service->setName('Service');
            $service->setDescription('Description');
            $service->setLabel('Label');

            $this->entityManager->persist($service);
            $this->entityManager->flush();
        }

        $emailingHistory = new EmailingHistory();
        $emailingHistory->setReceiver($admin);
        $emailingHistory->setService($service);
        $emailingHistory->setSentAt(new \DateTime());
        $this->entityManager->persist($emailingHistory);
        $this->entityManager->flush();

        $admin->addEmailingHistory($emailingHistory);


        $this->entityManager->persist($admin);
        $this->entityManager->flush();




        $this->client->request('GET', '/api/emailing_history', [
            'store' => $store->getId(),
            'role' => $admin->getRole()->getId(),
            'user' => $admin->getId(),
            'page' => 1,
            'limit' => 10,
            'start_date' => '01/01/2021',
            'end_date' => '01/01/2026'
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testGetEmailingHistoryAux(): void
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


        $store = new Store();
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


        $this->entityManager->persist($store);
        $this->entityManager->flush();

        $admin->addStore($store);
        $store->addUser($admin);

        $this->entityManager->persist($admin);
        $this->entityManager->persist($store);
        $this->entityManager->flush();

        $service = $this->entityManager->getRepository(EmailService::class)->findOneBy([]);

        if(!$service) {
            $service = new EmailService();
            $service->setName('Service');
            $service->setDescription('Description');
            $service->setLabel('Label');

            $this->entityManager->persist($service);
            $this->entityManager->flush();
        }

        $emailingHistory = new EmailingHistory();
        $emailingHistory->setReceiver($admin);
        $emailingHistory->setService($service);
        $emailingHistory->setSentAt(new \DateTime());
        $this->entityManager->persist($emailingHistory);
        $this->entityManager->flush();

        $admin->addEmailingHistory($emailingHistory);


        $this->entityManager->persist($admin);
        $this->entityManager->flush();




        $this->client->request('GET', '/api/emailing_history', [
            'store' => $store->getId(),
            'page' => 1,
            'limit' => 10,
            'start_date' => '01/01/2021',
            'end_date' => '01/01/2026'
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testGetEmailingHistory2(): void
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


        $store = new Store();
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

        $this->entityManager->persist($store);
        $this->entityManager->flush();



        $this->client->request('GET', '/api/emailing_history', [
            'store' => $store->getId(),
            'role' => $this->entityManager->getRepository(Role::class)->findOneBy([])->getName(),
            'user' => $this->entityManager->getRepository(User::class)->findOneBy([])->getId(),
            'page' => 1,
            'limit' => 10,
            'start_date' => '01/01/2021',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testGetEmailingHistory3(): void
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


        $store = new Store();
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

        $this->entityManager->persist($store);
        $this->entityManager->flush();



        $this->client->request('GET', '/api/emailing_history', [
            'store' => $store->getId(),
            'role' => $this->entityManager->getRepository(Role::class)->findOneBy([])->getName(),
            'user' => $this->entityManager->getRepository(User::class)->findOneBy([])->getId(),
            'page' => 1,
            'limit' => 10,
            'end_date' => '01/01/2025'
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
