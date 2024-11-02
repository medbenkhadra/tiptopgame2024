<?php

// src/Command/CreateDefaultRoles.php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Role;
use Doctrine\DBAL\Connection;

class CreateDefaultRoles extends Command
{

    private $entityManager;

    private $connection;

    public function __construct(EntityManagerInterface $entityManager , Connection $connection)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->connection = $connection;
        $this->setName('app:create-default-role');
        $this->setDescription('Generate default roles');
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->connection->executeQuery('SET SQL_SAFE_UPDATES = 0');
        $this->connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');
        $this->connection->executeQuery('DELETE FROM role');
        $this->connection->executeQuery('ALTER TABLE role AUTO_INCREMENT = 1');
        $this->connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');
        $this->connection->executeQuery('SET SQL_SAFE_UPDATES = 1');

        $roles = [
            ['name' => 'ROLE_ADMIN', 'label' => 'Administrateur'],
            ['name' => 'ROLE_STOREMANAGER', 'label' => 'Gestionnaire de Magasin'],
            ['name' => 'ROLE_EMPLOYEE', 'label' => 'Employé'],
            ['name' => 'ROLE_CLIENT', 'label' => 'Client'],
            ['name' => 'ROLE_BAILIFF', 'label' => 'Huissier'],
            ['name' => 'ROLE_ANONYMOUS', 'label' => 'Anonyme'],
        ];

        foreach ($roles as $roleData) {
            $role = new Role();
            $role->setName($roleData['name']);
            $role->setLabel($roleData['label']);

            $this->entityManager->persist($role);
        }

        $this->entityManager->flush();

        $output->writeln('Roles added to the role table.');

        return Command::SUCCESS;
    }
}
