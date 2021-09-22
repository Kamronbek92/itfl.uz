<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210910063237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'update work_comment table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE work_comment CHANGE is_deleted is_deleted TINYINT(1) DEFAULT NULL, CHANGE update_at updated_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE work_comment CHANGE is_deleted is_deleted TINYINT(1) NOT NULL, CHANGE updated_at update_at DATETIME DEFAULT NULL');
    }
}
