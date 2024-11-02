<?php


namespace App\Tests\Unit\Entity;


use App\Entity\Prize;
use App\Entity\Ticket;
use PHPUnit\Framework\TestCase;

class PrizeTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $prize = new Prize();

        $prize->setLabel('Grand Prize');
        $this->assertEquals('Grand Prize', $prize->getLabel());

        $prize->setName('Car');
        $this->assertEquals('Car', $prize->getName());

        $prize->setType('Physical');
        $this->assertEquals('Physical', $prize->getType());

        $prize->setPrizeValue('Luxury Car');
        $this->assertEquals('Luxury Car', $prize->getPrizeValue());

        $prize->setWinningRate(0.05);
        $this->assertEquals(0.05, $prize->getWinningRate());

        $prize->setPrice('10000.00');
        $this->assertEquals('10000.00', $prize->getPrice());
    }

    public function testAddRemoveTicket(): void
    {
        $prize = new Prize();
        $ticket = new Ticket();

        $prize->addTicket($ticket);
        $this->assertContains($ticket, $prize->getTickets());

        $prize->removeTicket($ticket);
        $this->assertNotContains($ticket, $prize->getTickets());
    }

    public function testPrizeJson(): void
    {
        $prize = new Prize();
        $prize->setLabel('Grand Prize');
        $prize->setName('Car');
        $prize->setType('Physical');
        $prize->setPrizeValue('Luxury Car');
        $prize->setWinningRate(0.05);
        $prize->setPrice('10000.00');

        $expectedJson = [
            'id' => null,
            'label' => 'Grand Prize',
            'name' => 'Car',
            'type' => 'Physical',
            'prize_value' => 'Luxury Car',
            'winning_rate' => 0.05,
        ];

        $this->assertEquals($expectedJson, $prize->getPrizeJson());
    }
}
