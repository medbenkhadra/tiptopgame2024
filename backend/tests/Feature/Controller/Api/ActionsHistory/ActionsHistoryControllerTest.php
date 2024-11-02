<?php

namespace App\Tests\Feature\Controller\Api\ActionsHistory;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ActionsHistoryControllerTest extends WebTestCase
{
    private $client;

    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
    }

    public function testGetActionsHistory(): void
    {
        $params = [
            'store' => 'store',
            'role' => 'role',
            'page' => 1,
            'limit' => 10,
            'start_date' => '01/01/2021',
            'end_date' => '01/01/2022',
        ];

        $this->client->request('GET', '/api/actions_history', $params);

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('actionsHistory', $responseData);

        $this->assertIsArray($responseData['actionsHistory']);

        $this->assertArrayHasKey('actionsHistoryCount', $responseData);
    }


    public function testGetActionsHistory2(): void
    {
        $params = [
            'start_date' => '01/01/2021',
        ];

        $this->client->request('GET', '/api/actions_history', $params);

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('actionsHistory', $responseData);

        $this->assertIsArray($responseData['actionsHistory']);

        $this->assertArrayHasKey('actionsHistoryCount', $responseData);
    }


    public function testGetActionsHistory3(): void
    {
        $params = [
            'end_date' => '01/01/2022',
        ];

        $this->client->request('GET', '/api/actions_history', $params);

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('actionsHistory', $responseData);

        $this->assertIsArray($responseData['actionsHistory']);

        $this->assertArrayHasKey('actionsHistoryCount', $responseData);
    }



}
