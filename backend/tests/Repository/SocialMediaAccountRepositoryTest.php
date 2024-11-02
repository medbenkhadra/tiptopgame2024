<?php

namespace App\Tests\Repository;

use App\Entity\Role;
use App\Entity\SocialMediaAccount;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SocialMediaAccountRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;

    private $socialMediaAccountRepository;

    private $passwordEncoder;

    /**
     * @throws NotSupported
     */
    protected function setUp(): void
    {
        $this->passwordEncoder = $this->createMock(UserPasswordHasherInterface::class);

        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->socialMediaAccountRepository = $this->entityManager->getRepository(SocialMediaAccount::class);
    }

    public function testFindAll(): void
    {
        $socialMediaAccounts = $this->socialMediaAccountRepository->findAll();

        $this->assertIsArray($socialMediaAccounts);
        foreach ($socialMediaAccounts as $socialMediaAccount) {
            $this->assertInstanceOf(SocialMediaAccount::class, $socialMediaAccount);
        }
    }

    /**
     * @throws OptimisticLockException
     * @throws NotSupported
     * @throws ORMException
     */
    public function testFindOneBy(): void
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


        $socialMediaAccount = new SocialMediaAccount();
        $socialMediaAccount->setGoogleId(1);
        $socialMediaAccount->setFacebookId(1);
        $socialMediaAccount->setUser($client);
        $this->entityManager->persist($socialMediaAccount);
        $this->entityManager->flush();



        $criteria = ['id' => $socialMediaAccount->getId()];
        $socialMediaAccount = $this->socialMediaAccountRepository->findOneBy($criteria);
        $this->assertInstanceOf(SocialMediaAccount::class, $socialMediaAccount);
    }
}