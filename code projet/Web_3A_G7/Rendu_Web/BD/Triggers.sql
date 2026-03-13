DELIMITER //

CREATE TRIGGER trigger_check_note
BEFORE INSERT ON Note
FOR EACH ROW
BEGIN
    DECLARE msg VARCHAR(255);
    IF NEW.valeur_note < 0 OR NEW.valeur_note > 20 THEN
        SET msg = CONCAT('Erreur : Note invalide (', NEW.valeur_note, '). Doit être entre 0 et 20.');
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = msg;
    END IF;
END //

CREATE TRIGGER trigger_check_note_update
BEFORE UPDATE ON Note
FOR EACH ROW
BEGIN
    DECLARE msg VARCHAR(255);
    IF NEW.valeur_note < 0 OR NEW.valeur_note > 20 THEN
        SET msg = CONCAT('Erreur : Note invalide (', NEW.valeur_note, '). Doit être entre 0 et 20.');
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = msg;
    END IF;
END //

CREATE TRIGGER trigger_check_unique_sondage
BEFORE INSERT ON ReponseSondage
FOR EACH ROW
BEGIN
    DECLARE deja_repondu INT;
    DECLARE msg VARCHAR(255);

    SELECT COUNT(*) INTO deja_repondu
    FROM ReponseSondage
    WHERE id_etudiant = NEW.id_etudiant AND id_sondage = NEW.id_sondage;

    IF deja_repondu > 0 THEN
        SET msg = CONCAT('Erreur : L étudiant ', NEW.id_etudiant, ' a déjà répondu au sondage ', NEW.id_sondage);
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = msg;
    END IF;
END //