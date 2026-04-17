<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Component\Mime\Email;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class MessageController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/messages', name: 'app_messages')]
    public function index(MessageRepository $messageRepository): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $messages = $messageRepository->findLatestForUser($user);
        $conversations = [];

        foreach ($messages as $message) {
            if (!$message instanceof Message) {
                continue;
            }

            $other = $message->getSender()?->getId() === $user->getId()
                ? $message->getRecipient()
                : $message->getSender();

            if (!$other instanceof User) {
                continue;
            }

            $key = (string) $other->getId();
            if (!isset($conversations[$key])) {
                $conversations[$key] = [
                    'user' => $other,
                    'lastMessage' => $message,
                ];
            }
        }

        return $this->render('messages/index.html.twig', [
            'conversations' => array_values($conversations),
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/messages/{username}', name: 'app_messages_thread', methods: ['GET', 'POST'])]
    public function thread(
        string $username,
        UserRepository $userRepository,
        MessageRepository $messageRepository,
        EntityManagerInterface $entityManager,
        Request $request,
        MessageBusInterface $bus
    ): Response {
        $me = $this->getUser();
        if (!$me instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $other = $userRepository->findOneBy(['username' => $username]);
        if (!$other instanceof User) {
            throw $this->createNotFoundException();
        }

        if ($other->getId() === $me->getId()) {
            throw $this->createNotFoundException();
        }

        $thread = $messageRepository->findThread($me, $other);

        $changed = false;
        foreach ($thread as $m) {
            if ($m instanceof Message && $m->getRecipient()?->getId() === $me->getId() && !$m->isRead()) {
                $m->setIsRead(true);
                $changed = true;
            }
        }
        if ($changed) {
            $entityManager->flush();
        }

        $message = new Message();
        $message->setSender($me);
        $message->setRecipient($other);

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($message);
            $entityManager->flush();

            $email = (new Email())
                ->from('no-reply@symfoconnect.local')
                ->to((string) $other->getEmail())
                ->subject('Nouveau message privé')
                ->text($me->getUsername() . ' t’a envoyé un message : ' . $message->getContent());

            $bus->dispatch(new SendEmailMessage($email));

            return $this->redirectToRoute('app_messages_thread', ['username' => $other->getUsername()]);
        }

        return $this->render('messages/thread.html.twig', [
            'other' => $other,
            'thread' => $thread,
            'form' => $form->createView(),
        ]);
    }
}

