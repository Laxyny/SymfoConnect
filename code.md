#[ORM\ManyToMany(targetEntity: User::class)]
#[ORM\JoinTable(name: 'post_likes')]
private Collection $likedBy;

Faire des requêtes JOIN FETCH au lieu de findAll() pour optimiser les performances.


EXO :
Ajouter des relations dans les entités existantes :
- Ajouter : following ManytoMany vers User
- Ajouter : author ManyToOne vers User
- Ajouter : likedBy ManytoMany vers User
Faire la migration et exécuter la migration.


EXO 2 :
- Mettre à jour l'entité User pour implémenter UserInterface 
php bin/console make:user

- Générer l'inscription
php bin/console make:registration-form

- Générer la connexion
php bin/console make:auth

- Mettre à jour les migrations
php bin/console make:migration
php bin/console doctrine:migrations:migrate

- Tester: créer un compte sur /register, se connecter sur /login