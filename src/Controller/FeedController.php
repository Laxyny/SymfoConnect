<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class FeedController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/feed', name: 'app_feed')]
    public function index(PostRepository $postRepository): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $posts = $postRepository->findFeedForUser($user);

        return $this->render('feed/index.html.twig', [
            'posts' => $posts,
            'followingCount' => $user->getFollowing()->count(),
        ]);
    }
}

