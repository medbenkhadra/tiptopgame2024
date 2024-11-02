<?php


namespace App\Tests\Unit\Entity;


use App\Entity\ActionHistory;
use App\Entity\Avatar;
use App\Entity\Badge;
use App\Entity\ConnectionHistory;
use App\Entity\EmailingHistory;
use App\Entity\LoyaltyPoints;
use App\Entity\Role;
use App\Entity\SocialMediaAccount;
use App\Entity\Store;
use App\Entity\Ticket;
use App\Entity\TicketHistory;
use App\Entity\User;
use App\Entity\UserPersonalInfo;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testId()
    {
        $user = new User();
        $this->assertNull($user->getId());
    }

    public function testFirstname()
    {
        $user = new User();
        $user->setFirstname('Amine');
        $this->assertSame('Amine', $user->getFirstname());
    }

    public function testLastname()
    {
        $user = new User();
        $user->setLastname('AMMAR');
        $this->assertSame('AMMAR', $user->getLastname());
    }

    public function testGender()
    {
        $user = new User();
        $user->setGender('Homme');
        $this->assertSame('Homme', $user->getGender());
    }

    public function testEmail()
    {
        $user = new User();
        $user->setEmail('test@test.com');
        $this->assertSame('test@test.com', $user->getEmail());
    }

    public function testDateOfBirth()
    {
        $user = new User();
        $dateOfBirth = new \DateTime('1996-08-11');
        $user->setDateOfBirth($dateOfBirth);
        $this->assertSame($dateOfBirth, $user->getDateOfBirth());
    }

    public function testRole()
    {
        $user = new User();
        $role = new Role();
        $role->setName('ROLE_CLIENT');
        $user->setRole($role);
        $this->assertSame($role, $user->getRole());
    }

    public function testPassword()
    {
        $user = new User();
        $user->setPassword('password');
        $this->assertSame('password', $user->getPassword());
    }

    public function testTickets()
    {
        $user = new User();
        $this->assertInstanceOf(ArrayCollection::class, $user->getTickets());
    }

    public function testApiToken()
    {
        $user = new User();
        $user->setApiToken('token');
        $this->assertSame('token', $user->getApiToken());
    }

    public function testApiTokenCreatedAt()
    {
        $user = new User();
        $apiTokenCreatedAt = new \DateTime();
        $user->setApiTokenCreatedAt($apiTokenCreatedAt);
        $this->assertSame($apiTokenCreatedAt, $user->getApiTokenCreatedAt());
    }

    public function testPhone()
    {
        $user = new User();
        $user->setPhone('1234567890');
        $this->assertSame('1234567890', $user->getPhone());
    }

    public function testStatus()
    {
        $user = new User();
        $user->setStatus(User::STATUS_OPEN);
        $this->assertSame(User::STATUS_OPEN, $user->getStatus());
    }

    public function testStores()
    {
        $user = new User();
        $this->assertInstanceOf(ArrayCollection::class, $user->getStores());
    }

    public function testTicketsEmployee()
    {
        $user = new User();
        $this->assertInstanceOf(ArrayCollection::class, $user->getTicketsEmployee());
    }

    public function testTicketHistories()
    {
        $user = new User();
        $this->assertInstanceOf(ArrayCollection::class, $user->getTicketHistories());
    }

    public function testUserPersonalInfo()
    {
        $user = new User();
        $userPersonalInfo = new UserPersonalInfo();
        $userPersonalInfo->setAddress('123 Main St');
        $userPersonalInfo->setPostalCode('12345');
        $userPersonalInfo->setCity('City');
        $userPersonalInfo->setCountry('Country');

        $user->setUserPersonalInfo($userPersonalInfo);
        $this->assertSame($user, $userPersonalInfo->getUser());
        $this->assertSame('123 Main St', $userPersonalInfo->getAddress());
        $this->assertSame('12345', $userPersonalInfo->getPostalCode());
        $this->assertSame('City', $userPersonalInfo->getCity());
        $this->assertSame('Country', $userPersonalInfo->getCountry());
        $this->assertSame($userPersonalInfo, $user->getUserPersonalInfo());

        $user->setUserPersonalInfo(null);
        $this->assertNull($userPersonalInfo->getUser());

        $user->setUserPersonalInfo(null);
        $this->assertNull($userPersonalInfo->getUser());

        $user2 = new User();
        $userPersonalInfo->setUser($user2);
        $this->assertSame($user2, $userPersonalInfo->getUser());
    }


    public function testIsActive()
    {
        $user = new User();
        $user->setIsActive(true);
        $this->assertTrue($user->isIsActive());
    }

    public function testCreatedAt()
    {
        $user = new User();
        $createdAt = new \DateTime();
        $user->setCreatedAt($createdAt);
        $this->assertSame($createdAt, $user->getCreatedAt());
    }

    public function testActivitedAt()
    {
        $user = new User();
        $activitedAt = new \DateTime();
        $user->setActivitedAt($activitedAt);
        $this->assertSame($activitedAt, $user->getActivitedAt());
    }

    public function testUpdatedAt()
    {
        $user = new User();
        $updatedAt = new \DateTime();
        $user->setUpdatedAt($updatedAt);
        $this->assertSame($updatedAt, $user->getUpdatedAt());
    }

    public function testToken()
    {
        $user = new User();
        $user->setToken('token');
        $this->assertSame('token', $user->getToken());
    }

    public function testTokenExpiredAt()
    {
        $user = new User();
        $tokenExpiredAt = new \DateTime();
        $user->setTokenExpiredAt($tokenExpiredAt);
        $this->assertSame($tokenExpiredAt, $user->getTokenExpiredAt());
    }

    public function testAvatar()
    {
        $user = new User();
        $avatar = new Avatar();
        $user->setAvatar($avatar);
        $this->assertSame($avatar, $user->getAvatar());
    }

    public function testBadges()
    {
        $user = new User();
        $this->assertInstanceOf(ArrayCollection::class, $user->getBadges());
    }

    public function testLoyaltyPoints()
    {
        $user = new User();
        $this->assertInstanceOf(ArrayCollection::class, $user->getLoyaltyPoints());
    }

    public function testActionHistories()
    {
        $user = new User();
        $this->assertInstanceOf(ArrayCollection::class, $user->getActionHistories());
    }

    public function testConnectionHistories()
    {
        $user = new User();
        $this->assertInstanceOf(ArrayCollection::class, $user->getConnectionHistories());
    }

    public function testEmailingHistories()
    {
        $user = new User();
        $this->assertInstanceOf(ArrayCollection::class, $user->getEmailingHistories());
    }

    public function testSocialMediaAccount()
    {
        $user = new User();
        $socialMediaAccount = new SocialMediaAccount();
        $user->setSocialMediaAccount($socialMediaAccount);
        $this->assertSame($socialMediaAccount, $user->getSocialMediaAccount());
    }

    public function testFullName()
    {
        $user = new User();
        $user->setFirstname('John');
        $user->setLastname('Doe');
        $this->assertSame('John Doe', $user->getFullName());
    }

    public function testAge()
    {
        $user = new User();
        $dateOfBirth = new \DateTime('1990-01-01');
        $user->setDateOfBirth($dateOfBirth);
        $this->assertSame(34, $user->getAge());
    }

    // Add tests for other methods as needed...

    public function testUserStoresJson()
    {
        $user = new User();
        $store1 = new Store();
        $store1->setName('Store 1');
        $store2 = new Store();
        $store2->setName('Store 2');
        $user->addStore($store1);
        $user->addStore($store2);

        $expected = [
            [$store1->getStoreJson()],
            [$store2->getStoreJson()],
        ];

        $this->assertSame($expected, $user->getUserStoresJson());
    }

    public function testUserJson()
    {
        $user = new User();
        $user->setFirstname('Amine');
        $user->setLastname('AMMAR');
        $user->setGender('Homme');
        $user->setEmail('amineammar20@icloud.com');
        $dateOfBirth = new \DateTime('1996-08-11');
        $user->setDateOfBirth($dateOfBirth);
        $role = new Role();
        $role->setName('ROLE_CLIENT');
        $user->setRole($role);

        $store = new Store();
        $store->setName('Store 1');
        $user->addStore($store);

        $userPersonalInfo = new UserPersonalInfo();
        $userPersonalInfo->setAddress('123 Main St');
        $userPersonalInfo->setPostalCode('12345');
        $userPersonalInfo->setCity('City');
        $userPersonalInfo->setCountry('Country');
        $user->setUserPersonalInfo($userPersonalInfo);

        $createdAt = new \DateTime('2024-04-01 12:00:00');
        $updatedAt = new \DateTime('2024-04-01 13:00:00');
        $activatedAt = new \DateTime('2024-04-01 14:00:00');

        $user->setCreatedAt($createdAt);
        $user->setUpdatedAt($updatedAt);
        $user->setActivitedAt($activatedAt);

        // Set avatar properties...
        $avatar = new Avatar();
        $avatar->setFilename('avatar.jpg');
        $avatar->setPath('/path/to/avatar');
        $user->setAvatar($avatar);

        $expected = [
            'id' => null,
            'lastname' => 'AMMAR',
            'firstname' => 'Amine',
            'phone' => null,
            'email' => 'amineammar20@icloud.com',
            'status' => null,
            'role' => 'ROLE_CLIENT',
            'stores' => $user->getUserStoresJson(),
            'dateOfBirth' => '11/08/1996',
            'age' => 27,
            'gender' => 'Homme',
            'store' => $store->getStoreJson(),
            'address' => '123 Main St',
            'postalCode' => '12345',
            'city' => 'City',
            'country' => 'Country',
            'is_activated' => null,
            'created_at' => [
                'date' => '01/04/2024',
                'time' => '12:00'
            ],
            'activated_at' => [
                'date' => '01/04/2024',
                'time' => '14:00'
            ],
            'updated_at' => [
                'date' => '01/04/2024',
                'time' => '13:00'
            ],
            'avatar_image' => [
                'id' => null,
                'filename' => 'avatar.jpg',
                'path' => '/path/to/avatar',
                'user' => 'amineammar20@icloud.com',
                'avatarUrl' => '/path/to/avatar/avatar.jpg'
            ],
            'avatar' => '/path/to/avatar/avatar.jpg'
        ];

        $this->assertSame($expected, $user->getUserJson());
    }



    public function testUserPersonalInfoJson()
    {
        $user = new User();
        $userPersonalInfo = new UserPersonalInfo();
        $user->setUserPersonalInfo($userPersonalInfo);

        $userPersonalInfo->setAddress('123 Main St');
        $userPersonalInfo->setPostalCode('12345');
        $userPersonalInfo->setCity('City');
        $userPersonalInfo->setCountry('Country');

        $expected = [
            'id' => null,
            'address' => '123 Main St',
            'postal_code' => '12345',
            'city' => 'City',
            'country' => 'Country',
        ];

        $this->assertSame($expected, $userPersonalInfo->getUserPersonalInfoJson());

    }

    public function testAddTicket()
    {
        $user = new User();
        $ticket = new Ticket();

        $user->addTicket($ticket);
        $this->assertTrue($user->getTickets()->contains($ticket));
        $this->assertSame($user, $ticket->getUser());

        $user->addTicket($ticket);
        $this->assertCount(1, $user->getTickets());

        $ticket2 = new Ticket();
        $user->addTicket($ticket2);
        $this->assertTrue($user->getTickets()->contains($ticket2));
        $this->assertSame($user, $ticket2->getUser());
    }

    public function testRemoveTicket()
    {
        $user = new User();
        $ticket = new Ticket();
        $user->addTicket($ticket);

        $user->removeTicket($ticket);
        $this->assertFalse($user->getTickets()->contains($ticket));
        $this->assertNull($ticket->getUser());

        $user->removeTicket(new Ticket());
        $this->assertCount(0, $user->getTickets());
    }


    public function testGetSalt()
    {
        $user = new User();
        $this->assertNull($user->getSalt());
    }

    public function testEraseCredentials()
    {
        $user = new User();
        $user->eraseCredentials();
        $this->expectNotToPerformAssertions();
    }


    public function testGetRoles()
    {
        $user = new User();
        $role = new Role();
        $role->setName('ROLE_CLIENT');
        $user->setRole($role);
        $this->assertSame(['ROLE_CLIENT'], $user->getRoles());
    }


    public function testGetUserIdentifier()
    {
        $user = new User();
        $user->setEmail('test@test.com');
        $this->assertSame('test@test.com', $user->getUserIdentifier());


    }


    public function testRemoveStore()
    {
        $user = new User();
        $store = new Store();
        $user->addStore($store);

        $user->removeStore($store);
        $this->assertFalse($user->getStores()->contains($store));
        $this->assertFalse($store->getUsers()->contains($user));

        $user->removeStore(new Store());
        $this->assertCount(0, $user->getStores());
    }


    public function testGetUserPersonalInfoJson()
    {
        $user = new User();
        $user->setFirstname('Amine');
        $user->setLastname('AMMAR');
        $user->setGender('Homme');
        $user->setEmail('amineammar20@icloud.com');
        $dateOfBirth = new \DateTime('1996-08-11');
        $user->setDateOfBirth($dateOfBirth);
        $role = new Role();
        $role->setName('ROLE_CLIENT');
        $user->setRole($role);

        $userPersonalInfo = new UserPersonalInfo();
        $userPersonalInfo->setAddress('123 Main St');
        $userPersonalInfo->setPostalCode('12345');
        $userPersonalInfo->setCity('City');
        $userPersonalInfo->setCountry('Country');
        $user->setUserPersonalInfo($userPersonalInfo);

        $createdAt = new \DateTime('2024-04-01 12:00:00');
        $updatedAt = new \DateTime('2024-04-01 13:00:00');
        $activatedAt = new \DateTime('2024-04-01 14:00:00');

        $user->setCreatedAt($createdAt);
        $user->setUpdatedAt($updatedAt);
        $user->setActivitedAt($activatedAt);

        // Set avatar properties...
        $avatar = new Avatar();
        $avatar->setFilename('avatar.jpg');
        $avatar->setPath('/path/to/avatar');
        $user->setAvatar($avatar);

        $expected = [
            'id' => null,
            'lastname' => 'AMMAR',
            'firstname' => 'Amine',
            'phone' => null,
            'email' => 'amineammar20@icloud.com',
            'status' => null,
            'role' => 'ROLE_CLIENT',
            'stores' => [],
            'dateOfBirth' => '11/08/1996',
            'age' => 27,
            'gender' => 'Homme',
            'userPersonalInfo' => [
                'id' => null,
                'address' => '123 Main St',
                'postal_code' => '12345',
                'city' => 'City',
                'country' => 'Country',
            ],
            'is_activated' => null,
            'created_at' => [
                'date' => '01/04/2024',
                'time' => '12:00',
            ],
            'activated_at' => [
                'date' => '01/04/2024',
                'time' => '14:00',
            ],
            'avatar_image' => [
                'id' => null,
                'filename' => 'avatar.jpg',
                'path' => '/path/to/avatar',
                'user' => 'amineammar20@icloud.com',
                'avatarUrl' => '/path/to/avatar/avatar.jpg',
            ],
            'avatar' => '/path/to/avatar/avatar.jpg',
        ];

        $this->assertSame($expected, $user->getUserPersonalInfoJson());
    }



    public function testAddTicketsEmployee()
    {
        $user = new User();
        $ticket = new Ticket();

        $user->addTicketsEmployee($ticket);
        $this->assertTrue($user->getTicketsEmployee()->contains($ticket));
        $this->assertSame($user, $ticket->getEmployee());

        $user->addTicketsEmployee($ticket);
        $this->assertCount(1, $user->getTicketsEmployee());

        $ticket2 = new Ticket();
        $user->addTicketsEmployee($ticket2);
        $this->assertTrue($user->getTicketsEmployee()->contains($ticket2));
        $this->assertSame($user, $ticket2->getEmployee());
    }

    public function testRemoveTicketsEmployee()
    {
        $user = new User();
        $ticket = new Ticket();
        $user->addTicketsEmployee($ticket);

        $user->removeTicketsEmployee($ticket);
        $this->assertFalse($user->getTicketsEmployee()->contains($ticket));
        $this->assertNull($ticket->getEmployee());

        $user->removeTicketsEmployee(new Ticket());
        $this->assertCount(0, $user->getTicketsEmployee());
    }

    public function testAddTicketHistory()
    {
        $user = new User();
        $ticketHistory = new TicketHistory();

        $user->addTicketHistory($ticketHistory);
        $this->assertTrue($user->getTicketHistories()->contains($ticketHistory));
        $this->assertSame($user, $ticketHistory->getUser());

        $user->addTicketHistory($ticketHistory);
        $this->assertCount(1, $user->getTicketHistories());

        $ticketHistory2 = new TicketHistory();
        $user->addTicketHistory($ticketHistory2);
        $this->assertTrue($user->getTicketHistories()->contains($ticketHistory2));
        $this->assertSame($user, $ticketHistory2->getUser());
    }

    public function testRemoveTicketHistory()
    {
        $user = new User();
        $ticketHistory = new TicketHistory();
        $user->addTicketHistory($ticketHistory);

        $user->removeTicketHistory($ticketHistory);
        $this->assertFalse($user->getTicketHistories()->contains($ticketHistory));
        $this->assertNull($ticketHistory->getUser());

        $user->removeTicketHistory(new TicketHistory());
        $this->assertCount(0, $user->getTicketHistories());
    }


    public function testAddBadge()
    {
        $user = new User();
        $badge = new Badge();

        $user->addBadge($badge);
        $this->assertCount(1, $user->getBadges());

    }

    public function testRemoveBadge()
    {
        $user = new User();
        $badge = new Badge();
        $user->addBadge($badge);

        $user->removeBadge($badge);
        $this->assertFalse($user->getBadges()->contains($badge));
        $this->assertFalse($badge->getUsers()->contains($user));

        $user->removeBadge(new Badge());
        $this->assertCount(0, $user->getBadges());
    }


    public function testAddLoyaltyPoint()
    {
        $user = new User();
        $loyaltyPoint = new LoyaltyPoints();

        $user->addLoyaltyPoint($loyaltyPoint);
        $this->assertCount(1, $user->getLoyaltyPoints());

    }

    public function testRemoveLoyaltyPoint()
    {
        $user = new User();
        $loyaltyPoint = new LoyaltyPoints();
        $user->addLoyaltyPoint($loyaltyPoint);
        $this->assertCount(1, $user->getLoyaltyPoints());
        $user->removeLoyaltyPoint($loyaltyPoint);
        $this->assertCount(0, $user->getLoyaltyPoints());
    }


    public function testAddActionHistory()
    {
        $user = new User();
        $actionHistory = new ActionHistory();

        $user->addActionHistory($actionHistory);
        $this->assertCount(1, $user->getActionHistories());

    }

    public function testRemoveActionHistory()
    {
        $user = new User();
        $actionHistory = new ActionHistory();
        $user->addActionHistory($actionHistory);
        $this->assertCount(1, $user->getActionHistories());
        $user->removeActionHistory($actionHistory);
        $this->assertCount(0, $user->getActionHistories());
    }

    public function testAddConnectionHistory()
    {
        $user = new User();
        $connectionHistory = new ConnectionHistory();

        $user->addConnectionHistory($connectionHistory);
        $this->assertCount(1, $user->getConnectionHistories());

    }

    public function testRemoveConnectionHistory()
    {
        $user = new User();
        $connectionHistory = new ConnectionHistory();
        $user->addConnectionHistory($connectionHistory);
        $this->assertCount(1, $user->getConnectionHistories());
        $user->removeConnectionHistory($connectionHistory);
        $this->assertCount(0, $user->getConnectionHistories());
    }

    public function testAddEmailingHistory()
    {
        $user = new User();
        $emailingHistory = new EmailingHistory();

        $user->addEmailingHistory($emailingHistory);
        $this->assertCount(1, $user->getEmailingHistories());

    }

    public function testRemoveEmailingHistory()
    {
        $user = new User();
        $emailingHistory = new EmailingHistory();
        $user->addEmailingHistory($emailingHistory);
        $this->assertCount(1, $user->getEmailingHistories());
        $user->removeEmailingHistory($emailingHistory);
        $this->assertCount(0, $user->getEmailingHistories());
    }


    public function testSetId()
    {
        $user = new User();
        $user->setId(1);
        $this->assertSame(1, $user->getId());
    }
}
