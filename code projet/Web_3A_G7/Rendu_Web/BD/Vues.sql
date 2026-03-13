CREATE OR REPLACE VIEW Vue_Liste_Etudiants_Par_Groupe AS
SELECT 
    g.libelle_groupe,
    g.type_groupe,
    e.numero_etudiant,
    e.nom_etudiant,
    e.prenom_etudiant,
    e.email_etudiant
FROM Groupe g
INNER JOIN Affectation a ON g.id_groupe = a.id_groupe
INNER JOIN Etudiant e ON a.id_etudiant = e.id_etudiant
WHERE a.affectation_courante = TRUE
ORDER BY g.libelle_groupe, e.nom_etudiant;

CREATE OR REPLACE VIEW Vue_Moyenne_Etudiant AS
SELECT 
    e.id_etudiant,
    e.nom_etudiant,
    e.prenom_etudiant,
    COUNT(n.id_note) as nombre_notes,
    ROUND(AVG(n.valeur_note), 2) as moyenne_generale
FROM Etudiant e
LEFT JOIN Note n ON e.id_etudiant = n.id_etudiant
GROUP BY e.id_etudiant, e.nom_etudiant, e.prenom_etudiant;

CREATE OR REPLACE VIEW Vue_Etudiants_Sans_Groupe AS
SELECT 
    e.id_etudiant,
    e.nom_etudiant,
    e.prenom_etudiant,
    e.parcours_etudiant
FROM Etudiant e
LEFT JOIN Affectation a ON e.id_etudiant = a.id_etudiant
WHERE a.id_affectation IS NULL OR a.affectation_courante = FALSE;

CREATE OR REPLACE VIEW Vue_Repartion_Geographique AS
SELECT 
    code_postal_etudiant,
    ville_etudiant,
    COUNT(id_etudiant) as nombre_etudiants
FROM Etudiant
GROUP BY code_postal_etudiant, ville_etudiant
ORDER BY nombre_etudiants DESC;

CREATE OR REPLACE VIEW Vue_Resultats_Sondage AS
SELECT 
    s.critere_sondage,
    r.valeur_reponse,
    COUNT(r.id_reponse) as nombre_votes
FROM Sondage s
INNER JOIN ReponseSondage r ON s.id_sondage = r.id_sondage
GROUP BY s.critere_sondage, r.valeur_reponse;

CREATE OR REPLACE VIEW Vue_Alerte_Surcharge_Groupe AS
SELECT 
    g.libelle_groupe,
    g.capacite_max_groupe,
    COUNT(a.id_etudiant) as nb_inscrits,
    (COUNT(a.id_etudiant) - g.capacite_max_groupe) as depassement
FROM Groupe g
INNER JOIN Affectation a ON g.id_groupe = a.id_groupe
WHERE a.affectation_courante = TRUE
GROUP BY g.id_groupe, g.libelle_groupe, g.capacite_max_groupe
HAVING COUNT(a.id_etudiant) > g.capacite_max_groupe;