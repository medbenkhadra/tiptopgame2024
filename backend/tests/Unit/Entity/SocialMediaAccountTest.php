<?php


namespace App\Tests\Unit\Entity;

use App\Entity\SocialMediaAccount;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class SocialMediaAccountTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $socialMediaAccount = new SocialMediaAccount();
        $user = new User();

        $socialMediaAccount->setGoogleId('123456');
        $this->assertEquals('123456', $socialMediaAccount->getGoogleId());

        $socialMediaAccount->setFacebookId('789012');
        $this->assertEquals('789012', $socialMediaAccount->getFacebookId());

        $socialMediaAccount->setUser($user);
        $this->assertSame($user, $socialMediaAccount->getUser());
    }

    public function testGetId(): void
    {
        $socialMediaAccount = new SocialMediaAccount();
        $this->assertNull($socialMediaAccount->getId());
    }

    public function testGetGoogleId(): void
    {
        $socialMediaAccount = new SocialMediaAccount();
        $this->assertNull($socialMediaAccount->getGoogleId());
    }

    public function testSetGoogleId(): void
    {
        $socialMediaAccount = new SocialMediaAccount();
        $socialMediaAccount->setGoogleId('123456');
        $this->assertSame('123456', $socialMediaAccount->getGoogleId());

    }
}