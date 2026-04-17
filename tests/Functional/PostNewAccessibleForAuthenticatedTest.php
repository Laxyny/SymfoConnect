<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Tests\Support\DatabaseResetTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PostNewAccessibleForAuthenticatedTest extends WebTestCase
{
    use DatabaseResetTrait;

    public function testAuthenticatedUserCanAccessNewPostForm(): void
    {
        $client = self::createClient();
        $entityManager = $this->resetDatabase();

        $user = (new User())
            ->setEmail('Test789@test.com')
            ->setUsername('Test789')
            ->setPassword('motdepasse');

        $entityManager->persist($user);
        $entityManager->flush();

        $client->loginUser($user);
        $client->request('GET', '/post/nouveau');

        self::assertResponseIsSuccessful();
    }
}

