<?php
require_once("config/connexion.php");

class Responsable { 
    
    public function __construct($db = null) {}

    public function importerNotes($cheminFichier) {
        $db = Connexion::pdo();
        if (($handle = fopen($cheminFichier, "r")) !== FALSE) {
            try {
                $db->beginTransaction();
                $stmtId = $db->query("SELECT MAX(id_importation_notes) FROM FeuilleNote");
                $newIdFeuille = $stmtId->fetchColumn() + 1;
                $db->prepare("INSERT INTO FeuilleNote (id_importation_notes, date_importation) VALUES (:id, NOW())")
                   ->execute([':id' => $newIdFeuille]);

                fgetcsv($handle, 1000, ";");
                while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                    $numEtu = $data[0];
                    $libelle = $data[1];
                    $valeur = str_replace(',', '.', $data[2]);

                    $stmtEtu = $db->prepare("SELECT id_etudiant FROM Etudiant WHERE numero_etudiant = ?");
                    $stmtEtu->execute([$numEtu]);
                    $idEtu = $stmtEtu->fetchColumn();

                    if ($idEtu) {
                        $stmtMax = $db->query("SELECT MAX(id_note) FROM Note");
                        $nextId = $stmtMax->fetchColumn() + 1;
                        $sql = "INSERT INTO Note (id_note, libelle_note, valeur_note, id_etudiant, id_importation_notes) VALUES (?, ?, ?, ?, ?)";
                        $db->prepare($sql)->execute([$nextId, $libelle, $valeur, $idEtu, $newIdFeuille]);
                    }
                }
                $db->commit();
                fclose($handle);
                return true;
            } catch (Exception $e) {
                $db->rollBack();
                return false;
            }
        }
        return false;
    }

    public function creerSondage($titre, $type, $idResponsable, $options) {
        $db = Connexion::pdo();
        try {
            $db->beginTransaction();
            $stmtId = $db->query("SELECT MAX(id_sondage) FROM Sondage");
            $newId = $stmtId->fetchColumn() + 1;

            $sql = "INSERT INTO Sondage (id_sondage, critere_sondage, type_sondage, id_responsable) VALUES (?, ?, ?, ?)";
            $db->prepare($sql)->execute([$newId, $titre, $type, $idResponsable]);

            $stmtOptId = $db->query("SELECT MAX(id_option) FROM OptionSondage");
            $nextOptId = $stmtOptId->fetchColumn();

            foreach ($options as $opt) {
                if (!empty(trim($opt))) {
                    $nextOptId++;
                    $db->prepare("INSERT INTO OptionSondage VALUES (?, ?, ?)")
                       ->execute([$nextOptId, trim($opt), $newId]);
                }
            }
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    public function ajouterContrainte($type, $param) {
        $db = Connexion::pdo();
        $stmtId = $db->query("SELECT MAX(id_contrainte) FROM Contrainte");
        $newId = $stmtId->fetchColumn() + 1;
        
        $sql = "INSERT INTO Contrainte (id_contrainte, type_contrainte, parametres_contrainte) VALUES (?, ?, ?)";
        return $db->prepare($sql)->execute([$newId, $type, $param]);
    }

    public function supprimerContrainte($id) {
        $db = Connexion::pdo();
        try {
            $db->beginTransaction();

            $sqlLien = "DELETE FROM respecte WHERE id_contrainte = ?";
            $db->prepare($sqlLien)->execute([$id]);

            $sqlContrainte = "DELETE FROM Contrainte WHERE id_contrainte = ?";
            $res = $db->prepare($sqlContrainte)->execute([$id]);

            $db->commit();
            return $res;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    public static function getContraintes() {
        $db = Connexion::pdo();
        return $db->query("SELECT * FROM Contrainte")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getResultatsDetailles($idSondage) {
        $db = Connexion::pdo();
        
        $sqlStats = "SELECT o.texte_option, COUNT(r.id_reponse) as nb_votes 
                     FROM OptionSondage o 
                     LEFT JOIN ReponseSondage r ON o.id_option = r.valeur_reponse 
                     WHERE o.id_sondage = ? 
                     GROUP BY o.id_option, o.texte_option";
        $stats = $db->prepare($sqlStats);
        $stats->execute([$idSondage]);
        $donnees['stats'] = $stats->fetchAll(PDO::FETCH_ASSOC);

        $sqlDetails = "SELECT e.nom_etudiant, e.prenom_etudiant, o.texte_option 
                       FROM ReponseSondage r
                       INNER JOIN Etudiant e ON r.id_etudiant = e.id_etudiant
                       INNER JOIN OptionSondage o ON r.valeur_reponse = o.id_option
                       WHERE r.id_sondage = ?
                       ORDER BY e.nom_etudiant";
        $details = $db->prepare($sqlDetails);
        $details->execute([$idSondage]);
        $donnees['details'] = $details->fetchAll(PDO::FETCH_ASSOC);

        $stmtInfo = $db->prepare("SELECT * FROM Sondage WHERE id_sondage = ?");
        $stmtInfo->execute([$idSondage]);
        $donnees['info'] = $stmtInfo->fetch(PDO::FETCH_ASSOC);

        return $donnees;
    }
}
?>