<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250807111138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fichier_tache CHANGE tache_id tache_id INT NOT NULL');
        $this->addSql('ALTER TABLE fichier_tache ADD CONSTRAINT FK_A4DE944DD2235D39 FOREIGN KEY (tache_id) REFERENCES tache (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fichier_tache DROP FOREIGN KEY FK_A4DE944DD2235D39');
        $this->addSql('ALTER TABLE fichier_tache CHANGE tache_id tache_id INT DEFAULT NULL');
    }
}
