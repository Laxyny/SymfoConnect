<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260417132802 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('RENAME TABLE post_user TO post_likes');
        $this->addSql('RENAME TABLE user_user TO user_follows');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('RENAME TABLE post_likes TO post_user');
        $this->addSql('RENAME TABLE user_follows TO user_user');
    }
}
