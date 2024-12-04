USE `wt-app`;

-- Récupérer les baies actuellement réservées

DROP PROCEDURE IF EXISTS GetRentedUnits;
DELIMITER $$
CREATE PROCEDURE GetRentedUnits(OUT count INT)
BEGIN
    SELECT count(u.id) INTO count
    FROM unit u
        INNER JOIN rental_unit ru ON u.id = ru.unit_id
        INNER JOIN rental r ON ru.rental_id = r.id
    WHERE r.rental_end_date IS NULL;
END $$
DELIMITER ;

CALL GetRentedUnits(@count);
SELECT @count;

-- Lister les packs par ordre de popularité

DROP PROCEDURE IF EXISTS GetOffersByPopularity;
DELIMITER $$
CREATE PROCEDURE GetOffersByPopularity()
BEGIN
    SELECT o.id, COUNT(r.offer_id) AS count
    FROM offer o
    LEFT JOIN rental r ON o.id = r.offer_id
    GROUP BY o.id
    ORDER BY count DESC;
END $$
DELIMITER ;

CALL GetOffersByPopularity();

-- Récupérer les baies ayant des unités libres

DROP PROCEDURE IF EXISTS GetRentedUnitsByBay;
DELIMITER $$
CREATE PROCEDURE GetRentedUnitsByBay()
BEGIN
    SELECT DISTINCT b.id, COUNT(DISTINCT u.id) AS count
    FROM bay b
        INNER JOIN unit JOIN unit u ON b.id = u.bay_id
    WHERE u.id IN (
        SELECT ru.unit_id
        FROM rental r
            INNER JOIN rental_unit ru ON r.id = ru.rental_id
        WHERE r.rental_end_date is null
    )
    GROUP BY b.id;
END $$
DELIMITER ;

CALL GetRentedUnitsByBay();