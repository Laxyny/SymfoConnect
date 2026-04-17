<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PostRepository;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class FeedController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/feed', name: 'app_feed')]
    public function index(PostRepository $postRepository, CacheInterface $cache): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $version = $cache->get('feed_version', static fn (ItemInterface $item) => 1);
        $key = 'feed_' . $user->getId() . '_' . $version;

        $posts = $cache->get($key, static function (ItemInterface $item) use ($postRepository, $user) {
            $item->expiresAfter(300);

            return $postRepository->findFeedForUser($user);
        });

        return $this->render('feed/index.html.twig', [
            'posts' => $posts,
            'followingCount' => $user->getFollowing()->count(),
        ]);
    }
}

