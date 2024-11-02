<?php


namespace App\Tests\Unit\Entity;

use App\Entity\Role;
use App\Entity\Ticket;
use App\Entity\TicketHistory;
use App\Entity\User;
use App\Entity\Prize;
use App\Entity\Store;
use App\Entity\UserPersonalInfo;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class TicketTest extends TestCase
{
    public function testGetTicketJson()
    {
        $ticket = new Ticket();
        $ticket->setTicketCode('ABC123');
        $winDate = new \DateTime('2024-04-01 10:00:00');
        $ticket->setWinDate($winDate);

        $user = new User();
        $user->setLastName('AMMAR');
        $user->setFirstName('Amine');
        $user->setDateOfBirth(new \DateTime('1990-01-01'));
        $user->setCreatedAt(new \DateTime('2024-04-01 13:00:00'));
        $user->setUpdatedAt(new \DateTime('2024-04-01 14:00:00'));
        $userPersonalInfo = new UserPersonalInfo();
        $userPersonalInfo->setAddress('123 rue de la rue');
        $userPersonalInfo->setPostalCode('12345');
        $userPersonalInfo->setCity('Paris');
        $userPersonalInfo->setCountry('France');
        $user->setUserPersonalInfo($userPersonalInfo);
        $role = new Role();
        $role->setName('ROLE_CLIENT');
        $user->setRole($role);

        $ticket->setUser($user);

        $prize = new Prize();
        $prize->setName('Prize 1');
        $prize->setLabel('Label 1');
        $prize->setType('Type 1');
        $prize->setPrizeValue('Value 1');
        $prize->setWinningRate(1.0);
        $prize->setPrice('10.00');
        $ticket->setPrize($prize);

        $ticket->setStatus(Ticket::STATUS_GENERATED);

        $printedAt = new \DateTime('2024-04-01 11:00:00');
        $ticket->setTicketPrintedAt($printedAt);

        $generatedAt = new \DateTime('2024-04-01 12:00:00');
        $ticket->setTicketGeneratedAt($generatedAt);

        $employee = new User();
        $employee->setLastName('AMMAR');
        $employee->setFirstName('Amine');
        $employee->setDateOfBirth(new \DateTime('1990-01-01'));
        $employee->setCreatedAt(new \DateTime('2024-04-01 13:00:00'));
        $employee->setUpdatedAt(new \DateTime('2024-04-01 14:00:00'));
        $employeePersonalInfo = new UserPersonalInfo();
        $employeePersonalInfo->setAddress('123 rue de la rue');
        $employeePersonalInfo->setPostalCode('12345');
        $employeePersonalInfo->setCity('Paris');
        $employeePersonalInfo->setCountry('France');
        $employee->setUserPersonalInfo($employeePersonalInfo);
        $role = new Role();
        $role->setName('ROLE_EMPLOYEE');
        $employee->setRole($role);
        $ticket->setEmployee($employee);

        $store = new Store();
        $store->setName('Store 1');
        $store->setAddress('123 rue de la rue');
        $store->setPostalCode('12345');
        $store->setCity('Paris');
        $store->setCountry('France');
        $ticket->setStore($store);

        $updatedAt = new \DateTime('2024-04-01 13:00:00');
        $ticket->setUpdatedAt($updatedAt);

        $expected = [
            'id' => null,
            'ticket_code' => 'ABC123',
            'win_date' => [
                'date' => '01/04/2024',
                'time' => '10:00',
            ],
            'user' => $user->getUserJson(),
            'prize' => $prize->getPrizeJson(),
            'status' => Ticket::STATUS_GENERATED,
            'ticket_printed_at' => [
                'date' => '01/04/2024',
                'time' => '11:00',
            ],
            'ticket_generated_at' => [
                'date' => '01/04/2024',
                'time' => '12:00',
            ],
            'employee' => $employee->getUserJson(),
            'store' => $store->getStoreJson(),
            'updated_at' => [
                'date' => '01/04/2024',
                'time' => '13:00',
            ],
        ];

        $this->assertSame($expected, $ticket->getTicketJson());
    }

    public function testGetWinDateJson()
    {
        $ticket = new Ticket();
        $winDate = new \DateTime('2024-04-01 10:00:00');
        $ticket->setWinDate($winDate);

        $expected = [
            'date' => '01/04/2024',
            'time' => '10:00',
        ];

        $this->assertSame($expected, $ticket->getWinDateJson());
    }

    public function testGetTicketPrintedAtJson()
    {
        $ticket = new Ticket();
        $printedAt = new \DateTime('2024-04-01 11:00:00');
        $ticket->setTicketPrintedAt($printedAt);

        $expected = [
            'date' => '01/04/2024',
            'time' => '11:00',
        ];

        $this->assertSame($expected, $ticket->getTicketPrintedAtJson());
    }

    public function testGetTicketGeneratedAtJson()
    {
        $ticket = new Ticket();
        $generatedAt = new \DateTime('2024-04-01 12:00:00');
        $ticket->setTicketGeneratedAt($generatedAt);

        $expected = [
            'date' => '01/04/2024',
            'time' => '12:00',
        ];

        $this->assertSame($expected, $ticket->getTicketGeneratedAtJson());
    }

    public function testGetUpdatedAtJson()
    {
        $ticket = new Ticket();
        $updatedAt = new \DateTime('2024-04-01 13:00:00');
        $ticket->setUpdatedAt($updatedAt);

        $expected = [
            'date' => '01/04/2024',
            'time' => '13:00',
        ];

        $this->assertSame($expected, $ticket->getUpdatedAtJson());
    }


    public function testAddTicketHistory()
    {
        $ticket = new Ticket();
        $ticketHistory = new TicketHistory();
        $ticket->addTicketHistory($ticketHistory);
        $this->assertSame($ticket, $ticketHistory->getTicket());
    }

    public function testRemoveTicketHistory()
    {
        $ticket = new Ticket();
        $ticketHistory = new TicketHistory();
        $ticket->addTicketHistory($ticketHistory);
        $ticket->removeTicketHistory($ticketHistory);
        $this->assertNull($ticketHistory->getTicket());
    }

    public function testGetTicketHistories()
    {
        $ticket = new Ticket();
        $ticketHistory = new TicketHistory();
        $ticket->addTicketHistory($ticketHistory);
        $this->assertInstanceOf(ArrayCollection::class, $ticket->getTicketHistories());
        $this->assertSame([$ticketHistory], $ticket->getTicketHistories()->toArray());
    }



}
