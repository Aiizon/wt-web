<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241119084823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial migration from diagram';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bay (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE billing_type (id INT AUTO_INCREMENT NOT NULL, months SMALLINT NOT NULL, discount_over_monthly DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discount (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, amount DOUBLE PRECISION NOT NULL, is_percentage TINYINT(1) NOT NULL, is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE intervention (id INT AUTO_INCREMENT NOT NULL, comment LONGTEXT NOT NULL, start_date DATETIME NOT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', end_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, rental_id INT NOT NULL, total_rent_price DOUBLE PRECISION NOT NULL, billing_address VARCHAR(511) NOT NULL, INDEX IDX_90651744A7CF2329 (rental_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, max_units SMALLINT NOT NULL, availability VARCHAR(255) NOT NULL, monthly_rent_price DOUBLE PRECISION NOT NULL, bandwidth VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rental (id INT AUTO_INCREMENT NOT NULL, billing_type_id INT NOT NULL, offer_id INT NOT NULL, customer_id INT NOT NULL, discount_id INT DEFAULT NULL, monthly_rent_price DOUBLE PRECISION NOT NULL, do_renew TINYINT(1) NOT NULL, first_rental_date DATETIME NOT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', rental_end_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', INDEX IDX_1619C27DAE620744 (billing_type_id), INDEX IDX_1619C27D53C674EE (offer_id), INDEX IDX_1619C27D9395C3F3 (customer_id), INDEX IDX_1619C27D4C7C611F (discount_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rental_unit (rental_id INT NOT NULL, unit_id INT NOT NULL, INDEX IDX_53F75393A7CF2329 (rental_id), INDEX IDX_53F75393F8BD700D (unit_id), PRIMARY KEY(rental_id, unit_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE setting (id INT AUTO_INCREMENT NOT NULL, `key` VARCHAR(255) NOT NULL, value LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unit (id INT AUTO_INCREMENT NOT NULL, unit_usage_id INT DEFAULT NULL, bay_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_DCBB0C53546E0C08 (unit_usage_id), INDEX IDX_DCBB0C53DF9BA23B (bay_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unit_intervention (unit_id INT NOT NULL, intervention_id INT NOT NULL, INDEX IDX_38EF63CF8BD700D (unit_id), INDEX IDX_38EF63C8EAE3863 (intervention_id), PRIMARY KEY(unit_id, intervention_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unit_usage (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(7) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, address VARCHAR(2047) DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744A7CF2329 FOREIGN KEY (rental_id) REFERENCES rental (id)');
        $this->addSql('ALTER TABLE rental ADD CONSTRAINT FK_1619C27DAE620744 FOREIGN KEY (billing_type_id) REFERENCES billing_type (id)');
        $this->addSql('ALTER TABLE rental ADD CONSTRAINT FK_1619C27D53C674EE FOREIGN KEY (offer_id) REFERENCES offer (id)');
        $this->addSql('ALTER TABLE rental ADD CONSTRAINT FK_1619C27D9395C3F3 FOREIGN KEY (customer_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE rental ADD CONSTRAINT FK_1619C27D4C7C611F FOREIGN KEY (discount_id) REFERENCES discount (id)');
        $this->addSql('ALTER TABLE rental_unit ADD CONSTRAINT FK_53F75393A7CF2329 FOREIGN KEY (rental_id) REFERENCES rental (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rental_unit ADD CONSTRAINT FK_53F75393F8BD700D FOREIGN KEY (unit_id) REFERENCES unit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE unit ADD CONSTRAINT FK_DCBB0C53546E0C08 FOREIGN KEY (unit_usage_id) REFERENCES unit_usage (id)');
        $this->addSql('ALTER TABLE unit ADD CONSTRAINT FK_DCBB0C53DF9BA23B FOREIGN KEY (bay_id) REFERENCES bay (id)');
        $this->addSql('ALTER TABLE unit_intervention ADD CONSTRAINT FK_38EF63CF8BD700D FOREIGN KEY (unit_id) REFERENCES unit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE unit_intervention ADD CONSTRAINT FK_38EF63C8EAE3863 FOREIGN KEY (intervention_id) REFERENCES intervention (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_90651744A7CF2329');
        $this->addSql('ALTER TABLE rental DROP FOREIGN KEY FK_1619C27DAE620744');
        $this->addSql('ALTER TABLE rental DROP FOREIGN KEY FK_1619C27D53C674EE');
        $this->addSql('ALTER TABLE rental DROP FOREIGN KEY FK_1619C27D9395C3F3');
        $this->addSql('ALTER TABLE rental DROP FOREIGN KEY FK_1619C27D4C7C611F');
        $this->addSql('ALTER TABLE rental_unit DROP FOREIGN KEY FK_53F75393A7CF2329');
        $this->addSql('ALTER TABLE rental_unit DROP FOREIGN KEY FK_53F75393F8BD700D');
        $this->addSql('ALTER TABLE unit DROP FOREIGN KEY FK_DCBB0C53546E0C08');
        $this->addSql('ALTER TABLE unit DROP FOREIGN KEY FK_DCBB0C53DF9BA23B');
        $this->addSql('ALTER TABLE unit_intervention DROP FOREIGN KEY FK_38EF63CF8BD700D');
        $this->addSql('ALTER TABLE unit_intervention DROP FOREIGN KEY FK_38EF63C8EAE3863');
        $this->addSql('DROP TABLE bay');
        $this->addSql('DROP TABLE billing_type');
        $this->addSql('DROP TABLE discount');
        $this->addSql('DROP TABLE intervention');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE offer');
        $this->addSql('DROP TABLE rental');
        $this->addSql('DROP TABLE rental_unit');
        $this->addSql('DROP TABLE setting');
        $this->addSql('DROP TABLE unit');
        $this->addSql('DROP TABLE unit_intervention');
        $this->addSql('DROP TABLE unit_usage');
        $this->addSql('DROP TABLE `user`');
    }
}
