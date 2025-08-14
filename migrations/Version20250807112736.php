<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250807112736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fichier_tache ADD tache_id INT NOT NULL');
        $this->addSql('ALTER TABLE fichier_tache ADD CONSTRAINT FK_A4DE944DD2235D39 FOREIGN KEY (tache_id) REFERENCES tache (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_A4DE944DD2235D39 ON fichier_tache (tache_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fichier_tache DROP FOREIGN KEY FK_A4DE944DD2235D39');
        $this->addSql('DROP INDEX IDX_A4DE944DD2235D39 ON fichier_tache');
        $this->addSql('ALTER TABLE fichier_tache DROP tache_id');
    }
}
