<?php

namespace App\Tests\Functional;

use App\Tests\Support\DatabaseResetTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ApiPostsTest extends WebTestCase
{
    use DatabaseResetTrait;

    public function testApiPostsReturnsValidJson(): void
    {
        $client = self::createClient();
        $this->resetDatabase();
        $client->request('GET', '/api/posts', server: [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('application/json', (string) $client->getResponse()->headers->get('content-type'));

        $data = json_decode((string) $client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($data);
    }
}

