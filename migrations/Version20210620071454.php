<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210620071454 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Edit (Tag, WorkComment) table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tag CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE work_comment CHANGE update_at update_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tag CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE work_comment CHANGE update_at update_at DATETIME NOT NULL');
    }
}
