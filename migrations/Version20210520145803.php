<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210520145803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auth_user_token (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, access_token VARCHAR(150) NOT NULL, refresh_token VARCHAR(150) NOT NULL, access_token_expires_at DATETIME NOT NULL, refresh_token_expires_at DATETIME NOT NULL, invalid TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_A3F057D7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE auth_users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(60) NOT NULL, name VARCHAR(60) DEFAULT NULL, email VARCHAR(60) NOT NULL, salt VARCHAR(6) NOT NULL, password VARCHAR(64) NOT NULL, blocked TINYINT(1) NOT NULL, user_roles JSON NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_D8A1F49CF85E0677 (username), UNIQUE INDEX UNIQ_D8A1F49CE7927C74 (email), INDEX user_username_idx (username), INDEX user_email_email_address_idx (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE auth_user_token ADD CONSTRAINT FK_A3F057D7A76ED395 FOREIGN KEY (user_id) REFERENCES auth_users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auth_user_token DROP FOREIGN KEY FK_A3F057D7A76ED395');
        $this->addSql('DROP TABLE auth_user_token');
        $this->addSql('DROP TABLE auth_users');
    }
}
