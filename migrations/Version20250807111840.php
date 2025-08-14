<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250807111840 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fichier_tache DROP FOREIGN KEY FK_A4DE944DD2235D39');
        $this->addSql('ALTER TABLE fichier_tache ADD CONSTRAINT FK_A4DE944DD2235D39 FOREIGN KEY (tache_id) REFERENCES tache (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fichier_tache DROP FOREIGN KEY FK_A4DE944DD2235D39');
        $this->addSql('ALTER TABLE fichier_tache ADD CONSTRAINT FK_A4DE944DD2235D39 FOREIGN KEY (tache_id) REFERENCES tache (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
