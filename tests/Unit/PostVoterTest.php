<?php

namespace App\Tests\Unit;

use App\Entity\Post;
use App\Entity\User;
use App\Security\Voter\PostVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class PostVoterTest extends TestCase
{
    public function testDeleteGrantedOnlyForAuthor(): void
    {
        $author = (new User())
            ->setEmail('Test123@test.com')
            ->setUsername('Test123')
            ->setPassword('motdepasse');

        $other = (new User())
            ->setEmail('Test456@test.com')
            ->setUsername('Test456')
            ->setPassword('motdepasse');

        $authorId = new \ReflectionProperty(User::class, 'id');
        $authorId->setAccessible(true);
        $authorId->setValue($author, 1);

        $otherId = new \ReflectionProperty(User::class, 'id');
        $otherId->setAccessible(true);
        $otherId->setValue($other, 2);

        $post = (new Post())
            ->setAuthor($author)
            ->setContent('salut');

        $voter = new PostVoter();

        $authorToken = $this->createMock(TokenInterface::class);
        $authorToken->method('getUser')->willReturn($author);

        $otherToken = $this->createMock(TokenInterface::class);
        $otherToken->method('getUser')->willReturn($other);

        self::assertSame(1, $voter->vote($authorToken, $post, [PostVoter::DELETE]));
        self::assertSame(-1, $voter->vote($otherToken, $post, [PostVoter::DELETE]));
    }
}

