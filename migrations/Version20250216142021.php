<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250216142021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add date, content and needs_generation columns to invoice';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice ADD date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', ADD content LONGBLOB DEFAULT NULL, ADD needs_generation TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice DROP date, DROP content, DROP needs_generation');
    }
}
