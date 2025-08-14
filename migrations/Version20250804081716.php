<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250804081716 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notifiation (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, tache_id INT DEFAULT NULL, message VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, is_read TINYINT(1) NOT NULL, INDEX IDX_A3D9FF85A76ED395 (user_id), INDEX IDX_A3D9FF85D2235D39 (tache_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notifiation ADD CONSTRAINT FK_A3D9FF85A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notifiation ADD CONSTRAINT FK_A3D9FF85D2235D39 FOREIGN KEY (tache_id) REFERENCES tache (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notifiation DROP FOREIGN KEY FK_A3D9FF85A76ED395');
        $this->addSql('ALTER TABLE notifiation DROP FOREIGN KEY FK_A3D9FF85D2235D39');
        $this->addSql('DROP TABLE notifiation');
    }
}
