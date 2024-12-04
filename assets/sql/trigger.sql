USE `wt-app`;

-- Faire en sorte qu'une commande (achat) d'unité se fasse toujours dans une et une seule baie

DROP TRIGGER IF EXISTS SingleBayRental;
DELIMITER $$
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
        SIGNAL SQLSTATE '50000' SET MESSAGE_TEXT = 'A rental must be in a single bay';
    END IF;
END $$
DELIMITER ;

START TRANSACTION;
INSERT INTO rental_unit (rental_id, unit_id) VALUES  (1, 2820);
ROLLBACK;

-- Un client ne peut avoir plus de 2 commandes en cours

DROP TRIGGER IF EXISTS MaxRentalPerClient;
DELIMITER $$
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
        SIGNAL SQLSTATE '50000' SET MESSAGE_TEXT = 'A customer cannot have more than 2 rentals';
    END IF;
END $$
DELIMITER ;

START TRANSACTION;
# DELETE FROM rental WHERE customer_id = 5;
INSERT INTO rental (customer_id, offer_id, first_rental_date, billing_type_id, monthly_rent_price, do_renew)
VALUES (5, 11, '2021-01-01', 9, 100, 1);
ROLLBACK;