<?php

namespace App\Tests\Feature\Controller\Api\Prize;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PrizeControllerTest extends WebTestCase
{
    private $client;

    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
    }

    public function testGetAllPrizes(): void
    {
        $this->client->request('GET', '/api/prizes');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('status', $responseData);

        $this->assertEquals('success', $responseData['status']);

        $this->assertArrayHasKey('prizes', $responseData);

        $this->assertIsArray($responseData['prizes']);

        foreach ($responseData['prizes'] as $prize) {
            $this->assertArrayHasKey('id', $prize);
            $this->assertArrayHasKey('label', $prize);
            $this->assertArrayHasKey('name', $prize);
            $this->assertArrayHasKey('type', $prize);
            $this->assertArrayHasKey('prize_value', $prize);
            $this->assertArrayHasKey('winning_rate', $prize);
            $this->assertArrayHasKey('totalCount', $prize);
            $this->assertArrayHasKey('percentage', $prize);

            $this->assertGreaterThanOrEqual(0, $prize['percentage']);
            $this->assertLessThanOrEqual(100, $prize['percentage']);
        }
    }
}
