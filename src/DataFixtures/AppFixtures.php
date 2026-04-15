<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $usersData = [
            [
                'email' => 'admin@symfoconnect.local',
                'username' => 'admin',
                'roles' => ['ROLE_ADMIN'],
                'bio' => 'Compte administrateur',
            ],
            [
                'email' => 'alice@symfoconnect.local',
                'username' => 'alice',
                'roles' => [],
                'bio' => 'Développeuse web',
            ],
            [
                'email' => 'bob@symfoconnect.local',
                'username' => 'bob',
                'roles' => [],
                'bio' => 'Amateur de café et de projets',
            ],
        ];

        $users = [];

        foreach ($usersData as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setUsername($userData['username']);
            $user->setRoles($userData['roles']);
            $user->setBio($userData['bio']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));

            $manager->persist($user);
            $users[$userData['username']] = $user;
        }

        $postsData = [
            [
                'author' => 'admin',
                'content' => 'Bienvenue sur SymfoConnect. Ceci est le premier post de démonstration.',
            ],
            [
                'author' => 'alice',
                'content' => 'Déjeuner en terrasse après une matinée bien remplie.',
            ],
            [
                'author' => 'alice',
                'content' => 'Nouvelle randonnée, la lumière était incroyable au sommet.',
            ],
            [
                'author' => 'bob',
                'content' => 'Soirée entre amis autour d’un café et de quelques idées de projets.',
            ],
        ];

        foreach ($postsData as $postData) {
            $post = new Post();
            $post->setContent($postData['content']);
            $post->setAuthor($users[$postData['author']]);

            $manager->persist($post);
        }

        $manager->flush();
    }
}
