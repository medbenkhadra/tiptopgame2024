<?php

namespace App\Tests\Unit\Entity;

use App\Entity\EmailingHistory;
use App\Entity\EmailService;
use App\Entity\Role;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class EmailingHistoryTest extends TestCase
{
    public function testGetId(): void
    {
        $emailingHistory = new EmailingHistory();
        $this->assertNull($emailingHistory->getId());
    }

    public function testGetSetService(): void
    {
        $emailingHistory = new EmailingHistory();
        $service = new EmailService();
        $emailingHistory->setService($service);
        $this->assertSame($service, $emailingHistory->getService());
    }

    public function testGetSetSentAt(): void
    {
        $emailingHistory = new EmailingHistory();
        $sentAt = new \DateTime();
        $emailingHistory->setSentAt($sentAt);
        $this->assertSame($sentAt, $emailingHistory->getSentAt());
    }

    public function testGetSetReceiver(): void
    {
        $emailingHistory = new EmailingHistory();
        $receiver = new User();
        $emailingHistory->setReceiver($receiver);
        $this->assertSame($receiver, $emailingHistory->getReceiver());
    }

    public function testGetEmailingHistoryJson(): void
    {
        $emailingHistory = new EmailingHistory();

        $service = new EmailService();
        $service->setName('Service Name');
        $service->setLabel('Service Label');
        $service->setDescription('Service Description');

        $receiver = new User();
        $receiver->setLastname('Receiver');
        $receiver->setFirstname('Receiver');
        $receiver->setEmail('receiver@example.com');
        $receiver->setDateOfBirth(new \DateTime());
        $role = new Role();
        $role->setName('ROLE_CLIENT');
        $receiver->setRole($role);

        $createdAt = new \DateTime();
        $receiver->setCreatedAt($createdAt);
        $receiver->setUpdatedAt($createdAt);

        $sentAt = new \DateTime();

        $emailingHistory->setService($service);
        $emailingHistory->setReceiver($receiver);
        $emailingHistory->setSentAt($sentAt);

        $expectedJson = [
            'id' => null,
            'service' => $service->getEmailServiceJson(),
            'receiver' => $receiver->getUserJson(),
            'sent_at' => [
                'date' => $sentAt->format('d-m-Y'),
                'time' => $sentAt->format('H:i'),
            ],
        ];


        $this->assertEquals($expectedJson, $emailingHistory->getEmailingHistoryJson());
    }
}
