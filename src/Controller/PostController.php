<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class PostController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/post/nouveau', name: 'app_post_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $author = $this->getUser();

        if (!$author instanceof User) {
            throw $this->createAccessDeniedException();
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

    #[IsGranted('ROLE_USER')]
    #[Route('/post/{id}/like', name: 'app_post_like_toggle', methods: ['POST'])]
    public function toggleLike(Post $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid('like' . $post->getId(), (string) $request->request->get('_csrf_token'))) {
            throw $this->createAccessDeniedException();
        }

        if ($post->getLikedBy()->contains($user)) {
            $post->removeLikedBy($user);
        } else {
            $post->addLikedBy($user);
        }

        $entityManager->flush();

        return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('app_home'));
    }

    #[Route('/post/{id}/supprimer', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Post $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete' . $post->getId(), (string) $request->request->get('_csrf_token'))) {
            throw $this->createAccessDeniedException();
        }

        $this->denyAccessUnlessGranted('POST_DELETE', $post);

        $entityManager->remove($post);
        $entityManager->flush();

        $this->addFlash('success', 'Post supprimé.');

        return $this->redirectToRoute('app_home');
    }
}

