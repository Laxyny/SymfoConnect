<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PostController extends AbstractController
{
    #[Route('/post/nouveau', name: 'app_post_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $author = $this->getUser();

        if (!$author instanceof \App\Entity\User) {
            $author = $userRepository->findOneBy([]);
        }

        if (!$author instanceof \App\Entity\User) {
            $this->addFlash('danger', 'Aucun utilisateur en base. Crée un utilisateur puis réessaie.');

            return $this->redirectToRoute('app_home');
        }

        $post = new Post();
        $post->setAuthor($author);

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', 'Post créé.');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('post/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

