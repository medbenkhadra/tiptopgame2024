<?php

namespace App\Command;

use App\Entity\Role;
use App\Entity\Store;
use App\Entity\UserPersonalInfo;
use App\Entity\User;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AddTipTopCompany extends Command
{

    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordEncoder;
    private Connection $connection;

    public function __construct(EntityManagerInterface $entityManager , UserPasswordHasherInterface $passwordEncoder , Connection $connection)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->connection = $connection;
        $this->setName('app:create-default-tiptop-company');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Add the first store "Thé Tip Top" and the profile for the role ROLE_STOREMANAGER.')
            ->setHelp('This command allows you to add the first store and role profile.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $store=$this->addCompany();
        $this->entityManager->flush();

        $this->generateBailiffProfile($this->entityManager , $output );
        $this->generateAnonymousProfile($this->entityManager , $output );


        $admin=$this->addAdminProfile('eric.bourdon@gmail.com');
        $this->addStoreManagerRelationShipWithAllStores($this->entityManager, $admin, $store);

        $admin=$this->addAdminProfile('admin@dsp5-archi-f23-15m-g7.fr');
        $this->addStoreManagerRelationShipWithAllStores($this->entityManager, $admin, $store);

        $this->addDefaultProfiles();



        $this->entityManager->flush();

        $output->writeln('Default company and profiles added  to the role table.');


        return Command::SUCCESS;
    }

    public function addStoreManagerRelationShipWithAllStores(EntityManagerInterface $entityManager, User $userManager, Store $store): void
    {
        $storeRepository = $entityManager->getRepository(Store::class);
        $stores = $storeRepository->findAll();

        if(!$stores) {
            return;
        }

        foreach ($stores as $store) {
            $userManager->addStore($store);
            $store->addUser($userManager);
            $this->entityManager->persist($userManager);
            $this->entityManager->persist($store);
        }
    }

    private function addCompany():Store
    {
        $store = new Store();
        $store->setName('Thé Tip Top');
        $store->setAddress('18 rue Léon Frot');
        $store->setPostalCode('75011');
        $store->setCity('Paris');
        $store->setCountry('France');
        $store->setEmail('the.tiptop@contact.com');
        $store->setHeadquartersAddress('18 rue Léon Frot, 75011 Paris');
        $store->setPhone('+33 1 43 55 55 55');

        $store->setSiren('94354353435');


        $store->setCapital('150000.00');
        $store->setStatus(Store::STATUS_OPEN);
        $store->setOpeningDate(new \DateTime());

        $this->entityManager->persist($store);

        return $store;
    }


    private function addAdminProfile(string $email):User
    {
        $storesAdminRole = $this->entityManager->getRepository(Role::class)->findOneBy(['name' => Role::ROLE_ADMIN]);
        $userManager = new User();
        $userManager->setLastname('Bourdon');
        $userManager->setFirstname('Eric');
        $userManager->setGender('Homme');
        $userManager->setEmail($email);
        $userManager->setRole($storesAdminRole);
        $userManager->setStatus(User::STATUS_OPEN);
        $userManager->setCreatedAt(new \DateTime());
        $userManager->setIsActive(true);
        $userManager->setActivitedAt(new \DateTime());
        $userManager->setDateOfBirth(new \DateTime('1980-01-06'));
        $plainPassword = 'Mohamed6759F@';
        $hashedPassword = $this->passwordEncoder->hashPassword($userManager, $plainPassword);
        $userManager->setPassword($hashedPassword);

        $userPersonalInfo = new UserPersonalInfo();
        $userPersonalInfo->setUser($userManager);
        $userPersonalInfo->setAddress('18 rue Léon Frot');
        $userPersonalInfo->setPostalCode('75011');
        $userPersonalInfo->setCity('Paris');
        $userPersonalInfo->setCountry('France');

        $this->entityManager->persist($userPersonalInfo);

        $this->entityManager->persist($userManager);


        return $userManager;

    }

    private function addDefaultProfiles():void
    {

        $defaultStore = $this->entityManager->getRepository(Store::class)->findOneBy(['name' => 'Thé Tip Top']);

        $storesAdminRole = $this->entityManager->getRepository(Role::class)->findOneBy(['name' => Role::ROLE_STOREMANAGER]);
        $userManager = new User();
        $userManager->setLastname('Quentin');
        $userManager->setFirstname('Dupont');
        $userManager->setGender('Homme');
        $userManager->setEmail('manager@dsp5-archi-f23-15m-g7.fr');
        $userManager->setRole($storesAdminRole);
        $userManager->setStatus(User::STATUS_OPEN);
        $userManager->setCreatedAt(new \DateTime());
        $userManager->setIsActive(true);
        $userManager->setActivitedAt(new \DateTime());
        $userManager->setDateOfBirth(new \DateTime('1980-01-06'));
        $plainPassword = 'Mohamed6759F@';
        $hashedPassword = $this->passwordEncoder->hashPassword($userManager, $plainPassword);
        $userManager->setPassword($hashedPassword);

        $userPersonalInfo = new UserPersonalInfo();
        $userPersonalInfo->setUser($userManager);
        $userPersonalInfo->setAddress('123 Avenue de la République');
        $userPersonalInfo->setPostalCode('93100');
        $userPersonalInfo->setCity('Montreuil');
        $userPersonalInfo->setCountry('France');

        $this->entityManager->persist($userPersonalInfo);
        $this->entityManager->persist($userManager);

        $userManager->addStore($defaultStore);
        $defaultStore->addUser($userManager);
        $this->entityManager->persist($userManager);
        $this->entityManager->persist($defaultStore);

        $storesAdminRole = $this->entityManager->getRepository(Role::class)->findOneBy(['name' => Role::ROLE_EMPLOYEE]);
        $userManager = new User();
        $userManager->setLastname('Jean');
        $userManager->setFirstname('Dupont');
        $userManager->setGender('Homme');
        $userManager->setEmail('employee@dsp5-archi-f23-15m-g7.fr');
        $userManager->setRole($storesAdminRole);
        $userManager->setStatus(User::STATUS_OPEN);
        $userManager->setCreatedAt(new \DateTime());
        $userManager->setIsActive(true);
        $userManager->setActivitedAt(new \DateTime());
        $userManager->setDateOfBirth(new \DateTime('1980-01-06'));
        $plainPassword = 'Mohamed6759F@';
        $hashedPassword = $this->passwordEncoder->hashPassword($userManager, $plainPassword);
        $userManager->setPassword($hashedPassword);

        $userPersonalInfo = new UserPersonalInfo();
        $userPersonalInfo->setUser($userManager);
        $userPersonalInfo->setAddress('23 Avenue de la République');
        $userPersonalInfo->setPostalCode('93100');
        $userPersonalInfo->setCity('Montreuil');
        $userPersonalInfo->setCountry('France');

        $this->entityManager->persist($userPersonalInfo);
        $this->entityManager->persist($userManager);

        $userManager->addStore($defaultStore);
        $defaultStore->addUser($userManager);
        $this->entityManager->persist($userManager);
        $this->entityManager->persist($defaultStore);



    }

    private function generateAnonymousProfile(EntityManagerInterface $entityManager):void
    {
        $anonymousRole = $this->entityManager->getRepository(Role::class)->findOneBy(['name' => Role::ROLE_ANONYMOUS]);
        $anonymousUser = new User();
        $anonymousUser->setLastname('Anonymous');
        $anonymousUser->setFirstname('Anonymous');
        $anonymousUser->setGender('Homme');
        $anonymousUser->setEmail('anonymous@anonymous.fr');
        $anonymousUser->setRole($anonymousRole);

        $anonymousUser->setStatus(User::STATUS_OPEN);
        $anonymousUser->setCreatedAt(new \DateTime());
        $anonymousUser->setIsActive(true);
        $anonymousUser->setActivitedAt(new \DateTime());

        $anonymousUser->setDateOfBirth(new \DateTime('1980-01-06'));

        $plainedPassword = 'anonymous';
        $hashedPassword = $this->passwordEncoder->hashPassword($anonymousUser, $plainedPassword);
        $anonymousUser->setPassword($hashedPassword);


        $userPersonalInfo = new UserPersonalInfo();
        $userPersonalInfo->setUser($anonymousUser);
        $userPersonalInfo->setAddress('18 rue Léon Frot');
        $userPersonalInfo->setPostalCode('75011');
        $userPersonalInfo->setCity('Paris');
        $userPersonalInfo->setCountry('France');

        $entityManager->persist($userPersonalInfo);

        $entityManager->persist($anonymousUser);

    }

    private function generateBailiffProfile(EntityManagerInterface $entityManager):void
    {
        $bailiffRole = $this->entityManager->getRepository(Role::class)->findOneBy(['name' => Role::ROLE_BAILIFF]);
        $bailiffUser = new User();
        $bailiffUser->setLastname('Rick');
        $bailiffUser->setFirstname('Arnaud');
        $bailiffUser->setGender('Homme');
        $bailiffUser->setEmail('rick.arnaud@dsp5-archi-f23-15m-g7.fr');

        $bailiffUser->setRole($bailiffRole);
        $bailiffUser->setStatus(User::STATUS_OPEN);
        $bailiffUser->setCreatedAt(new \DateTime());
        $bailiffUser->setIsActive(true);
        $bailiffUser->setActivitedAt(new \DateTime());
        $bailiffUser->setDateOfBirth(new \DateTime('1980-01-06'));
        $plainedPassword = 'Mohamed6759F@';
        $hashedPassword = $this->passwordEncoder->hashPassword($bailiffUser, $plainedPassword);
        $bailiffUser->setPassword($hashedPassword);

        $userPersonalInfo = new UserPersonalInfo();
        $userPersonalInfo->setUser($bailiffUser);
        $userPersonalInfo->setAddress('18 rue Léon Frot');
        $userPersonalInfo->setPostalCode('75011');
        $userPersonalInfo->setCity('Paris');
        $userPersonalInfo->setCountry('France');

        $entityManager->persist($userPersonalInfo);

        $entityManager->persist($bailiffUser);


        $bailiffRole = $this->entityManager->getRepository(Role::class)->findOneBy(['name' => Role::ROLE_BAILIFF]);
        $bailiffUser = new User();
        $bailiffUser->setLastname('Rick');
        $bailiffUser->setFirstname('Arnaud');
        $bailiffUser->setGender('Homme');
        $bailiffUser->setEmail('bailiff@dsp5-archi-f23-15m-g7.fr');

        $bailiffUser->setRole($bailiffRole);
        $bailiffUser->setStatus(User::STATUS_OPEN);
        $bailiffUser->setCreatedAt(new \DateTime());
        $bailiffUser->setIsActive(true);
        $bailiffUser->setActivitedAt(new \DateTime());
        $bailiffUser->setDateOfBirth(new \DateTime('1980-01-06'));
        $plainedPassword = 'Mohamed6759F@';
        $hashedPassword = $this->passwordEncoder->hashPassword($bailiffUser, $plainedPassword);
        $bailiffUser->setPassword($hashedPassword);

        $userPersonalInfo = new UserPersonalInfo();
        $userPersonalInfo->setUser($bailiffUser);
        $userPersonalInfo->setAddress('18 rue Léon Frot');
        $userPersonalInfo->setPostalCode('75011');
        $userPersonalInfo->setCity('Paris');
        $userPersonalInfo->setCountry('France');

        $entityManager->persist($userPersonalInfo);

        $entityManager->persist($bailiffUser);



    }
}
