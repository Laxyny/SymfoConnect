<?php

namespace App\Tests\Functional;

use App\Tests\Support\DatabaseResetTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class HomePageTest extends WebTestCase
{
    use DatabaseResetTrait;

    public function testHomePageResponds200(): void
    {
        $client = self::createClient();
        $this->resetDatabase();
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
    }
}

