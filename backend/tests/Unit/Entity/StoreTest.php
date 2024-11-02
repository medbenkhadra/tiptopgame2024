<?php


namespace App\Tests\Unit\Entity;

use App\Entity\ActionHistory;
use App\Entity\Store;
use App\Entity\Ticket;
use App\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StoreTest extends KernelTestCase
{
    public function testGettersAndSetters(): void
    {
        $store = new Store();

        $store->setName('Test Store')
            ->setAddress('123 Main St')
            ->setHeadquartersAddress('456 HQ St')
            ->setEmail('test@store.com')
            ->setPostalCode('12345')
            ->setCity('City')
            ->setCountry('Country')
            ->setCapital('1000.00')
            ->setStatus(Store::STATUS_OPEN)
            ->setOpeningDate(new DateTime())
            ->setPhone('1234567890')
            ->setSiren('123456789');

        $this->assertSame('Test Store', $store->getName());
        $this->assertSame('123 Main St', $store->getAddress());
        $this->assertSame('456 HQ St', $store->getHeadquartersAddress());
        $this->assertSame('test@store.com', $store->getEmail());
        $this->assertSame('12345', $store->getPostalCode());
        $this->assertSame('City', $store->getCity());
        $this->assertSame('Country', $store->getCountry());
        $this->assertSame('1000.00', $store->getCapital());
        $this->assertSame(Store::STATUS_OPEN, $store->getStatus());
        $this->assertInstanceOf(DateTime::class, $store->getOpeningDate());
        $this->assertSame('1234567890', $store->getPhone());
        $this->assertSame('123456789', $store->getSiren());
    }

    public function testAddAndRemoveTicket(): void
    {
        $store = new Store();
        $ticket = new Ticket();

        $store->addTicket($ticket);
        $this->assertCount(1, $store->getTickets());
        $this->assertSame($store, $ticket->getStore());

        $store->removeTicket($ticket);
        $this->assertCount(0, $store->getTickets());
        $this->assertNull($ticket->getStore());
    }

    public function testAddAndRemoveActionHistory(): void
    {
        $store = new Store();
        $actionHistory = new ActionHistory();

        $store->addActionHistory($actionHistory);
        $this->assertCount(1, $store->getActionHistories());
        $this->assertSame($store, $actionHistory->getStore());

        $store->removeActionHistory($actionHistory);
        $this->assertCount(0, $store->getActionHistories());
        $this->assertNull($actionHistory->getStore());
    }

    public function testJsonOutput(): void
    {
        $store = new Store();
        $store->setName('Test Store')
            ->setAddress('123 Main St')
            ->setHeadquartersAddress('456 HQ St')
            ->setEmail('test@store.com')
            ->setPostalCode('12345')
            ->setCity('City')
            ->setCountry('Country')
            ->setCapital('1000.00')
            ->setStatus(Store::STATUS_OPEN)
            ->setOpeningDate(new DateTime())
            ->setPhone('1234567890')
            ->setSiren('123456789');

        $jsonOutput = $store->getStoreJson();

        $this->assertArrayHasKey('id', $jsonOutput);
        $this->assertArrayHasKey('name', $jsonOutput);
        $this->assertArrayHasKey('address', $jsonOutput);
        $this->assertArrayHasKey('headquarters_address', $jsonOutput);
        $this->assertArrayHasKey('email', $jsonOutput);
        $this->assertArrayHasKey('postal_code', $jsonOutput);
        $this->assertArrayHasKey('city', $jsonOutput);
        $this->assertArrayHasKey('country', $jsonOutput);
        $this->assertArrayHasKey('capital', $jsonOutput);
        $this->assertArrayHasKey('status', $jsonOutput);
        $this->assertArrayHasKey('opening_date', $jsonOutput);
        $this->assertArrayHasKey('phone', $jsonOutput);
        $this->assertArrayHasKey('siren', $jsonOutput);
    }



    public function testAddUser(): void
    {
        $store = new Store();
        $user = new User();

        $store->addUser($user);

        $this->assertCount(1, $store->getUsers());
        $this->assertTrue($store->getUsers()->contains($user));
    }

    public function testRemoveUser(): void
    {
        $store = new Store();
        $user = new User();

        $store->addUser($user);
        $store->removeUser($user);

        $this->assertCount(0, $store->getUsers());
        $this->assertFalse($store->getUsers()->contains($user));
    }


    public function testGetId(): void
    {
        $store = new Store();
        $this->assertNull($store->getId());
    }

}