<?php

namespace App\Tests\Repository;

use App\Entity\Role;
use App\Entity\User;
use App\Entity\UserPersonalInfo;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPersonalInfoRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;

    private $userPersonalInfoRepository;

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

        $this->userPersonalInfoRepository = $this->entityManager->getRepository(UserPersonalInfo::class);
    }

    public function testFindAll(): void
    {
        $userPersonalInfos = $this->userPersonalInfoRepository->findAll();

        $this->assertIsArray($userPersonalInfos);
        foreach ($userPersonalInfos as $userPersonalInfo) {
            $this->assertInstanceOf(UserPersonalInfo::class, $userPersonalInfo);
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

        $newUserPersonalInfo = new UserPersonalInfo();
        $newUserPersonalInfo->setUser($client);
        $newUserPersonalInfo->setAddress('123 rue de la paix');
        $newUserPersonalInfo->setCity('Paris');
        $newUserPersonalInfo->setCountry('France');
        $newUserPersonalInfo->setPostalCode('75000');
        $this->entityManager->persist($newUserPersonalInfo);
        $this->entityManager->flush();

        $criteria = ['id' => $newUserPersonalInfo->getId()];
        $userPersonalInfo = $this->userPersonalInfoRepository->findOneBy($criteria);
        $this->assertInstanceOf(UserPersonalInfo::class, $userPersonalInfo);
    }

}