<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260415140405 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY `FK_5A8A6C8DA76ED395`');
        $this->addSql('DROP INDEX IDX_5A8A6C8DA76ED395 ON post');
        $this->addSql('ALTER TABLE post ADD content LONGTEXT DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL, CHANGE user_id author_id INT NOT NULL');
        $this->addSql('UPDATE post SET content = COALESCE(description, \'\'), created_at = NOW() WHERE content IS NULL OR created_at IS NULL');
        $this->addSql('ALTER TABLE post DROP description, DROP image, DROP location');
        $this->addSql('ALTER TABLE post MODIFY content LONGTEXT NOT NULL, MODIFY created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DF675F31B ON post (author_id)');
        $this->addSql('ALTER TABLE user ADD bio LONGTEXT DEFAULT NULL, ADD avatar_url VARCHAR(255) DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL, CHANGE username username VARCHAR(50) DEFAULT NULL');
        $this->addSql('UPDATE user SET created_at = NOW() WHERE created_at IS NULL');
        $this->addSql('UPDATE user SET username = CONCAT(\'user\', id) WHERE username IS NULL OR username = \'\'');
        $this->addSql('ALTER TABLE user MODIFY username VARCHAR(50) NOT NULL, MODIFY created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user RENAME INDEX uniq_identifier_email TO UNIQ_8D93D649E7927C74');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DF675F31B');
        $this->addSql('DROP INDEX IDX_5A8A6C8DF675F31B ON post');
        $this->addSql('ALTER TABLE post ADD description LONGTEXT DEFAULT NULL, ADD image VARCHAR(255) DEFAULT NULL, ADD location VARCHAR(255) DEFAULT NULL, DROP content, DROP created_at, CHANGE author_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT `FK_5A8A6C8DA76ED395` FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DA76ED395 ON post (user_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677 ON user');
        $this->addSql('ALTER TABLE user DROP bio, DROP avatar_url, DROP created_at, CHANGE username username VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user RENAME INDEX uniq_8d93d649e7927c74 TO UNIQ_IDENTIFIER_EMAIL');
    }
}
