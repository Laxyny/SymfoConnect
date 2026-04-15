<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profil/{username}', name: 'app_profile_show')]
    public function show(string $username, UserRepository $userRepository, PostRepository $postRepository): Response
    {
        $user = $userRepository->findOneBy(['username' => $username]);

        if ($user === null) {
            throw $this->createNotFoundException();
        }

        $posts = $postRepository->findBy(['author' => $user], ['createdAt' => 'DESC']);

        return $this->render('profile/show.html.twig', [
            'profileUser' => $user,
            'posts' => $posts,
        ]);
    }
}

