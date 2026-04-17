<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260417132931 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE post_likes RENAME INDEX idx_44c6b1424b89032c TO IDX_DED1C2924B89032C');
        $this->addSql('ALTER TABLE post_likes RENAME INDEX idx_44c6b142a76ed395 TO IDX_DED1C292A76ED395');
        $this->addSql('ALTER TABLE user_follows RENAME INDEX idx_f7129a803ad8644e TO IDX_136E94793AD8644E');
        $this->addSql('ALTER TABLE user_follows RENAME INDEX idx_f7129a80233d34c1 TO IDX_136E9479233D34C1');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE post_likes RENAME INDEX idx_ded1c2924b89032c TO IDX_44C6B1424B89032C');
        $this->addSql('ALTER TABLE post_likes RENAME INDEX idx_ded1c292a76ed395 TO IDX_44C6B142A76ED395');
        $this->addSql('ALTER TABLE user_follows RENAME INDEX idx_136e9479233d34c1 TO IDX_F7129A80233D34C1');
        $this->addSql('ALTER TABLE user_follows RENAME INDEX idx_136e94793ad8644e TO IDX_F7129A803AD8644E');
    }
}
