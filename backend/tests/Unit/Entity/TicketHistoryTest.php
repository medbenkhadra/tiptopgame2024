<?php


namespace App\Tests\Unit\Entity;

use App\Entity\Role;
use App\Entity\Ticket;
use App\Entity\TicketHistory;
use App\Entity\User;
use App\Entity\UserPersonalInfo;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TicketHistoryTest extends KernelTestCase
{
    public function testGettersAndSetters(): void
    {
        $ticketHistory = new TicketHistory();

        $user = new User();
        $ticket = new Ticket();
        $employee = new User();

        $ticketHistory->setUser($user)
            ->setStatus('Pending')
            ->setEmployee($employee)
            ->setTicket($ticket);

        $this->assertSame($user, $ticketHistory->getUser());
        $this->assertSame('Pending', $ticketHistory->getStatus());
        $this->assertSame($employee, $ticketHistory->getEmployee());
        $this->assertSame($ticket, $ticketHistory->getTicket());

        $updatedAt = new DateTime();
        $ticketHistory->setUpdatedAt($updatedAt);

        $this->assertSame($updatedAt, $ticketHistory->getUpdatedAt());
    }

    public function testGetUpdatedAtJson()
    {
        $ticketHistory = new TicketHistory();
        $updatedAt = new \DateTime('2024-04-01 13:00:00');
        $ticketHistory->setUpdatedAt($updatedAt);

        $expected = [
            'date' => '01/04/2024',
            'time' => '13:00',
        ];

        $this->assertSame($expected, $ticketHistory->getUpdatedAtJson());
    }

    public function testGetTicketHistoryJson()
    {
        $ticketHistory = new TicketHistory();
        $ticketHistory->setId(1);
        $user = new User();
        $user->setEmail('test@test.com');
        $user->setFirstName('Amine');
        $user->setLastName('AMMAR');
        $user->setDateOfBirth(new DateTime('1990-01-01'));
        $user->setCreatedAt(new DateTime('2024-04-01 13:00:00'));
        $user->setUpdatedAt(new DateTime('2024-04-01 14:00:00'));
        $userPersonalInfo = new UserPersonalInfo();
        $userPersonalInfo->setAddress('123 rue de la rue');
        $userPersonalInfo->setPostalCode('12345');
        $userPersonalInfo->setCity('Paris');
        $userPersonalInfo->setCountry('France');
        $user->setUserPersonalInfo($userPersonalInfo);

        $role  = new Role();
        $role->setName('ROLE_CLIENT');
        $user->setRole($role);

        $ticketHistory->setUser($user);
        $ticketHistory->setStatus('Pending');
        $ticketHistory->setUpdatedAt(new DateTime('2024-04-01 13:00:00'));

        $expected = [
            'id' => 1,
            'user' => $user->getUserJson(),
            'status' => 'Pending',
            'employee' => null,
            'ticket' => null,
            'updated_at' => [
                'date' => '01/04/2024',
                'time' => '13:00',
            ],
        ];

        $this->assertSame($expected, $ticketHistory->getTicketHistoryJson());
    }
}