<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241204122900 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add exercise procs and triggers';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE PROCEDURE GetRentedUnits(OUT count INT)
            BEGIN
                 SELECT count(u.id) INTO count
                 FROM unit u
                 WHERE u.id NOT IN (
                     SELECT ru.unit_id
                     FROM rental r
                         INNER JOIN rental_unit ru ON r.id = ru.rental_id
                     WHERE r.rental_end_date is null
                 );
            END'
        );

        $this->addSql('
            CREATE PROCEDURE GetOffersByPopularity()
            BEGIN
                SELECT o.id, COUNT(r.offer_id) AS count
                FROM offer o
                LEFT JOIN rental r ON o.id = r.offer_id
                GROUP BY o.id
                ORDER BY count DESC;
            END'
        );

        $this->addSql('
            CREATE PROCEDURE GetRentedUnitsByBay()
            BEGIN
                SELECT DISTINCT b.id
                FROM bay b
                         INNER JOIN unit JOIN unit u ON b.id = u.bay_id
                WHERE u.id NOT IN (
                    SELECT ru.unit_id
                    FROM rental r
                             INNER JOIN rental_unit ru ON r.id = ru.rental_id
                    WHERE r.rental_end_date is null
                );
            END'
        );

        $this->addSql('
            CREATE TRIGGER SingleBayRental
                BEFORE INSERT
                ON rental_unit
                FOR EACH ROW
            BEGIN
                IF (EXISTS (
                    SELECT COUNT(DISTINCT u.bay_id)
                    FROM rental_unit ru
                        INNER JOIN unit u ON ru.unit_id = u.id
                        INNER JOIN rental r ON new.rental_id = r.id
                    WHERE rental_id = new.rental_id
                    GROUP BY r.id
                    HAVING COUNT(DISTINCT u.bay_id) > 1
                ))
                THEN
                    SIGNAL SQLSTATE \'50000\' SET MESSAGE_TEXT = \'A rental must be in a single bay\';
                END IF;
            END'
        );

        $this->addSql('
            CREATE TRIGGER MaxRentalPerClient
                BEFORE INSERT
                ON rental
                FOR EACH ROW
            BEGIN
                IF (
                    SELECT COUNT(r.id)
                    FROM rental r
                    WHERE r.customer_id = new.customer_id
                ) >= 2
                THEN
                    SIGNAL SQLSTATE \'50000\' SET MESSAGE_TEXT = \'A customer cannot have more than 2 rentals\';
                END IF;
            END'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP PROCEDURE GetRentedUnits');
        $this->addSql('DROP PROCEDURE GetOffersByPopularity');
        $this->addSql('DROP PROCEDURE GetRentedUnitsByBay');
        $this->addSql('DROP TRIGGER SingleBayRental');
        $this->addSql('DROP TRIGGER MaxRentalPerClient');
    }
}
