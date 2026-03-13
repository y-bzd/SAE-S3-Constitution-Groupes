DELIMITER //

DROP FUNCTION IF EXISTS calculer_moyenne //
CREATE FUNCTION calculer_moyenne(p_id_etudiant INT) 
RETURNS DECIMAL(5,2)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_moyenne DECIMAL(5,2);
    SELECT AVG(valeur_note) INTO v_moyenne FROM Note WHERE id_etudiant = p_id_etudiant;
    IF v_moyenne IS NULL THEN RETURN 0.00; END IF;
    RETURN v_moyenne;
END //

DROP FUNCTION IF EXISTS get_places_restantes //
CREATE FUNCTION get_places_restantes(p_id_groupe INT) 
RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_capacite INT;
    DECLARE v_inscrits INT;
    SELECT capacite_max_groupe INTO v_capacite FROM Groupe WHERE id_groupe = p_id_groupe;
    SELECT COUNT(*) INTO v_inscrits FROM Affectation WHERE id_groupe = p_id_groupe AND affectation_courante = TRUE;
    RETURN (v_capacite - v_inscrits);
END //

DROP PROCEDURE IF EXISTS inscrire_etudiant //
CREATE PROCEDURE inscrire_etudiant(IN p_id_etudiant INT, IN p_id_groupe INT)
BEGIN
    DECLARE v_places INT;
    SELECT get_places_restantes(p_id_groupe) INTO v_places;
    IF v_places <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Impossible : Le groupe est complet.';
    ELSE
        INSERT INTO Affectation (date_affectation, affectation_courante, id_groupe, id_etudiant)
        VALUES (NOW(), TRUE, p_id_groupe, p_id_etudiant);
    END IF;
END //

DELIMITER ;