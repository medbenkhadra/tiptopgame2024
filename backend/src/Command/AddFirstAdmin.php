<?php
// src/Command/AddCompanyCommand.php

namespace App\Command;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AddFirstAdmin extends Command
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager , UserPasswordHasherInterface $passwordEncoder)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->setName('app:create-first-admin');
    }

    protected function configure():void
    {
        $this->setDescription('Create the first admin user.');
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $admin=$this->addAdminProfile();

        $output->writeln($admin->getLastname().' has been  added  to the user table. (admin)');

        return Command::SUCCESS;
    }


    private function addAdminProfile():User
    {
        $roleRepository = $this->entityManager->getRepository(Role::class);
        $adminRole = $roleRepository->findOneBy(['name' => Role::ROLE_ADMIN]);
        $admin = new User();
        $admin->setLastname('AMMAR');
        $admin->setFirstname('Amine');
        $admin->setGender('M'); 
        $admin->setEmail('amineammar20@icloud.com');
        $admin->setRole($adminRole);
        $admin->setDateOfBirth(new \DateTime('1996-11-08'));
        $plainPassword = 'admin'; 
        $hashedPassword = $this->passwordEncoder->hashPassword($admin, $plainPassword);
        $admin->setPassword($hashedPassword);

        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        return $admin;

    }
}
