<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class NotificationController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/notifications', name: 'app_notifications')]
    public function index(NotificationRepository $notificationRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $notifications = $notificationRepository->findForRecipient($user);

        $changed = false;
        foreach ($notifications as $notification) {
            if (!$notification->isRead()) {
                $notification->setIsRead(true);
                $changed = true;
            }
        }

        if ($changed) {
            $entityManager->flush();
        }

        return $this->render('notifications/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/notifications/{id}/supprimer', name: 'app_notification_delete', methods: ['POST'])]
    public function delete(Notification $notification, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        if ($notification->getRecipient()?->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid('delete_notification' . $notification->getId(), (string) $request->request->get('_csrf_token'))) {
            throw $this->createAccessDeniedException();
        }

        $entityManager->remove($notification);
        $entityManager->flush();

        $this->addFlash('success', 'Notification supprimée.');

        return $this->redirectToRoute('app_notifications');
    }
}

