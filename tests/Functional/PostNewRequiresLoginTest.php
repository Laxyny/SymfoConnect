<?php

namespace App\Tests\Functional;

use App\Tests\Support\DatabaseResetTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PostNewRequiresLoginTest extends WebTestCase
{
    use DatabaseResetTrait;

    public function testNewPostRedirectsToLoginWhenAnonymous(): void
    {
        $client = self::createClient();
        $this->resetDatabase();
        $client->request('GET', '/post/nouveau');

        self::assertResponseRedirects('/login');
    }
}

