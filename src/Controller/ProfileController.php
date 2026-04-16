<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\User;
use App\Form\ProfileEditType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ProfileController extends AbstractController
{
    #[Route('/profil/{username}', name: 'app_profile_show', methods: ['GET', 'POST'])]
    public function show(string $username, UserRepository $userRepository, PostRepository $postRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->findOneBy(['username' => $username]);

        if ($user === null) {
            throw $this->createNotFoundException();
        }

        $posts = $postRepository->findBy(['author' => $user], ['createdAt' => 'DESC']);

        $viewer = $this->getUser();
        $isFollowing = false;
        $editFormView = null;
        if ($viewer instanceof User) {
            $isFollowing = $viewer->getFollowing()->contains($user);
            if ($viewer->getId() === $user->getId()) {
                $form = $this->createForm(ProfileEditType::class, $viewer);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $entityManager->flush();
                    $this->addFlash('success', 'Profil mis à jour.');

                    return $this->redirectToRoute('app_profile_show', ['username' => $viewer->getUsername()]);
                }

                $editFormView = $form->createView();
            }
        }

        return $this->render('profile/show.html.twig', [
            'profileUser' => $user,
            'posts' => $posts,
            'followersCount' => $user->getFollowers()->count(),
            'followingCount' => $user->getFollowing()->count(),
            'isFollowing' => $isFollowing,
            'editForm' => $editFormView,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/profil/{username}/follow', name: 'app_profile_follow', methods: ['POST'])]
    public function follow(string $username, UserRepository $userRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $target = $userRepository->findOneBy(['username' => $username]);
        if (!$target instanceof User) {
            throw $this->createNotFoundException();
        }

        $viewer = $this->getUser();
        if (!$viewer instanceof User) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid('follow' . $target->getId(), (string) $request->request->get('_csrf_token'))) {
            throw $this->createAccessDeniedException();
        }

        if ($viewer->getId() === $target->getId()) {
            $this->addFlash('danger', 'Vous ne pouvez pas vous suivre vous-même.');
            return $this->redirectToRoute('app_profile_show', ['username' => $username]);
        }

        if (!$viewer->getFollowing()->contains($target)) {
            $viewer->addFollowing($target);

            $notification = new Notification();
            $notification->setRecipient($target);
            $notification->setType('follow');
            $notification->setContent($viewer->getUsername() . ' a commencé à vous suivre.');

            $entityManager->persist($notification);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_profile_show', ['username' => $username]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/profil/{username}/unfollow', name: 'app_profile_unfollow', methods: ['POST'])]
    public function unfollow(string $username, UserRepository $userRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $target = $userRepository->findOneBy(['username' => $username]);
        if (!$target instanceof User) {
            throw $this->createNotFoundException();
        }

        $viewer = $this->getUser();
        if (!$viewer instanceof User) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid('unfollow' . $target->getId(), (string) $request->request->get('_csrf_token'))) {
            throw $this->createAccessDeniedException();
        }

        if ($viewer->getFollowing()->contains($target)) {
            $viewer->removeFollowing($target);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_profile_show', ['username' => $username]);
    }
}

