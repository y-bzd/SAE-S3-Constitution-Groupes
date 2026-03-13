<?php
require_once("config/connexion.php");

class Promotion {
    public function __construct($db = null) {
    }

    public function getEtudiants($idPromo) {
        $db = Connexion::pdo();
        
        $sql = "SELECT DISTINCT
                    id_etudiant, 
                    numero_etudiant AS numeroEtudiant, 
                    nom_etudiant AS nom, 
                    prenom_etudiant AS prenom, 
                    type_bac AS typeBac,
                    email_etudiant AS email,
                    genre_etudiant AS sexe
                FROM Etudiant 
                ORDER BY nom_etudiant ASC, prenom_etudiant ASC"; 
        
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($etudiants as &$etu) {
            $sqlNotes = "SELECT libelle_note, valeur_note FROM Note WHERE id_etudiant = :id";
            $stmtNotes = $db->prepare($sqlNotes);
            $stmtNotes->execute([':id' => $etu['id_etudiant']]);
            $etu['notes'] = $stmtNotes->fetchAll(PDO::FETCH_ASSOC);
            $etu['id_groupe'] = null;
        }

        return $etudiants;
    }

    public function getGroupes($idPromo, $uniquementPublics = false) {
        $db = Connexion::pdo();
        
        $sql = "SELECT g.id_groupe, g.libelle_groupe, g.type_groupe, g.capacite_max_groupe, g.groupe_rendu_public
                FROM Groupe g
                INNER JOIN Contient c ON g.id_groupe = c.id_groupe
                WHERE c.id_promotion = :idPromo";
        
        if ($uniquementPublics) {
            $sql .= " AND g.groupe_rendu_public = 1";
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute([':idPromo' => $idPromo]);
        $groupes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($groupes as &$groupe) {
            $sqlEtudiants = "SELECT e.id_etudiant, e.prenom_etudiant, e.nom_etudiant, e.genre_etudiant, 
                                    e.type_bac, e.periode_redoublement,
                                    (SELECT AVG(valeur_note) FROM Note n WHERE n.id_etudiant = e.id_etudiant) as moyenne
                             FROM Etudiant e
                             INNER JOIN Affectation a ON e.id_etudiant = a.id_etudiant
                             WHERE a.id_groupe = :id_groupe AND a.affectation_courante = 1
                             ORDER BY e.nom_etudiant";
                             
            $stmtEtu = $db->prepare($sqlEtudiants);
            $stmtEtu->execute([':id_groupe' => $groupe['id_groupe']]);
            $groupe['etudiants'] = $stmtEtu->fetchAll(PDO::FETCH_ASSOC);

            $sommeMoyenne = 0;
            $nbF = 0; $nbH = 0; $nbNotes = 0;
            $nbRedoublants = 0;
            $bacs = [];

            foreach($groupe['etudiants'] as $e) {
                if ($e['genre_etudiant'] === 'F' || $e['genre_etudiant'] === 'Femme') $nbF++;
                else $nbH++;

                if ($e['moyenne'] !== null) {
                    $sommeMoyenne += $e['moyenne'];
                    $nbNotes++;
                }

                $redoub = trim($e['periode_redoublement'] ?? '');
                if (!empty($redoub) && $redoub !== 'Non' && $redoub !== '0') {
                    $nbRedoublants++;
                }

                $bac = $e['type_bac'] ?: 'N/A';
                if (!isset($bacs[$bac])) $bacs[$bac] = 0;
                $bacs[$bac]++;
            }

            $strBacs = [];
            foreach($bacs as $type => $count) {
                $strBacs[] = "$type:$count";
            }

            $groupe['stats'] = [
                'nb_etudiants' => count($groupe['etudiants']),
                'nb_femmes' => $nbF,
                'nb_hommes' => $nbH,
                'moyenne_groupe' => $nbNotes > 0 ? round($sommeMoyenne / $nbNotes, 2) : 'N/A',
                'nb_redoublants' => $nbRedoublants,
                'repartition_bacs' => implode(', ', $strBacs)
            ];
        }
        return $groupes;
    }

    public function getEtudiantsSansGroupe($idPromo) {
        $db = Connexion::pdo();
        $sql = "SELECT e.id_etudiant, e.nom_etudiant, e.prenom_etudiant, e.genre_etudiant,
                       (SELECT AVG(valeur_note) FROM Note n WHERE n.id_etudiant = e.id_etudiant) as moyenne
                FROM Etudiant e
                WHERE e.id_etudiant NOT IN (
                    SELECT id_etudiant FROM Affectation WHERE affectation_courante = 1
                )
                ORDER BY e.nom_etudiant";
        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deplacerEtudiant($idEtu, $idNouveauGroupe) {
        $db = Connexion::pdo();
        try {
            $db->beginTransaction();

            $sqlDesac = "UPDATE Affectation SET affectation_courante = 0 WHERE id_etudiant = ?";
            $db->prepare($sqlDesac)->execute([$idEtu]);

            if (!empty($idNouveauGroupe)) {
                $sqlNew = "INSERT INTO Affectation (id_etudiant, id_groupe, affectation_courante, date_affectation) 
                           VALUES (?, ?, 1, NOW())";
                $db->prepare($sqlNew)->execute([$idEtu, $idNouveauGroupe]);
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
        }
    }

    public function createGroupe($idPromo, $data) {
        $db = Connexion::pdo();
        try {
            $db->beginTransaction();

            $stmtId = $db->query("SELECT MAX(id_groupe) FROM Groupe");
            $maxId = $stmtId->fetchColumn();
            $nextId = $maxId ? $maxId + 1 : 1;

            $sql = "INSERT INTO Groupe (id_groupe, libelle_groupe, type_groupe, capacite_max_groupe, groupe_rendu_public) 
                    VALUES (:id, :libelle, :type, :capacite, 0)";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':id' => $nextId,
                ':libelle' => $data['libelle'],
                ':type' => $data['type'],
                ':capacite' => $data['capaciteMax']
            ]);

            $sqlLien = "INSERT INTO Contient (id_promotion, id_groupe, nb_groupes) VALUES (:idP, :idG, '1')";
            $stmtLien = $db->prepare($sqlLien);
            $stmtLien->execute([
                ':idP' => $idPromo,
                ':idG' => $nextId
            ]);

            $db->commit();
            return $nextId;

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function deleteGroupe($idGroupe) {
        $db = Connexion::pdo();
        try {
            $db->beginTransaction();
            $db->prepare("DELETE FROM Affectation WHERE id_groupe = ?")->execute([$idGroupe]);
            $db->prepare("DELETE FROM respecte WHERE id_groupe = ?")->execute([$idGroupe]);
            $db->prepare("DELETE FROM Contient WHERE id_groupe = ?")->execute([$idGroupe]);
            $db->prepare("DELETE FROM Groupe WHERE id_groupe = ?")->execute([$idGroupe]);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function sauvegarderAffectations($idPromo, $listeAffectations) {
        $db = Connexion::pdo();
        try {
            $db->beginTransaction();
            $db->query("UPDATE Affectation SET affectation_courante = 0");

            $sqlInsert = "INSERT INTO Affectation (id_etudiant, id_groupe, affectation_courante, date_affectation) 
                          VALUES (:idEtu, :idGroupe, 1, NOW())";
            $stmt = $db->prepare($sqlInsert);

            foreach ($listeAffectations as $aff) {
                $stmtGetId = $db->prepare("SELECT id_etudiant FROM Etudiant WHERE numero_etudiant = :num");
                $stmtGetId->execute([':num' => $aff['numeroEtudiant']]);
                $idEtu = $stmtGetId->fetchColumn();

                if ($idEtu && isset($aff['idGroupe'])) {
                     $stmt->execute([':idEtu' => $idEtu, ':idGroupe' => $aff['idGroupe']]);
                }
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
?>