<?php
require_once("config/connexion.php");

class SondageEtudiant {
    
    public function getSondages() {
        $db = Connexion::pdo();
        $req = "SELECT * FROM Sondage ORDER BY id_sondage DESC";
        $sondages = $db->query($req)->fetchAll(PDO::FETCH_ASSOC);

        foreach ($sondages as &$s) {
            $stmt = $db->prepare("SELECT * FROM OptionSondage WHERE id_sondage = ?");
            $stmt->execute([$s['id_sondage']]);
            $s['options'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $sondages;
    }

    public function repondre($idEtudiant, $idSondage, $reponses) {
        $db = Connexion::pdo();
        
        $del = "DELETE FROM ReponseSondage WHERE id_etudiant = ? AND id_sondage = ?";
        $db->prepare($del)->execute([$idEtudiant, $idSondage]);

        $stmtMax = $db->query("SELECT MAX(id_reponse) FROM ReponseSondage");
        $currentMax = $stmtMax->fetchColumn();

        $sql = "INSERT INTO ReponseSondage (id_reponse, valeur_reponse, ordre, id_etudiant, id_sondage) 
                VALUES (:idR, :val, :ordre, :idE, :idS)";
        $stmt = $db->prepare($sql);

        if (is_array($reponses)) {
            foreach ($reponses as $valeur => $ordre) {
                $currentMax++;
                $stmt->execute([
                    ':idR' => $currentMax,
                    ':val' => $valeur,
                    ':ordre' => $ordre,
                    ':idE' => $idEtudiant,
                    ':idS' => $idSondage
                ]);
            }
        } else {
            $currentMax++;
            $stmt->execute([
                ':idR' => $currentMax,
                ':val' => $reponses, 
                ':ordre' => 1, 
                ':idE' => $idEtudiant, 
                ':idS' => $idSondage
            ]);
        }
    }

    public function getLimiteMaxAmis() {
        $db = Connexion::pdo();
        $stmt = $db->query("SELECT parametres_contrainte FROM Contrainte WHERE type_contrainte = 'Max_Amis'");
        $val = $stmt->fetchColumn();
        
        return ($val !== false) ? (int)$val : 4; 
    }

    public function getCollegues($idEtudiant) {
        $db = Connexion::pdo();
        $sql = "SELECT e.id_etudiant, e.nom_etudiant, e.prenom_etudiant 
                FROM ChoixCollegue c 
                INNER JOIN Etudiant e ON c.id_collegue = e.id_etudiant 
                WHERE c.id_etudiant = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$idEtudiant]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ajouterCollegue($idEtudiant, $idCollegue) {
        $db = Connexion::pdo();
        
        if($idEtudiant == $idCollegue) return false;

        $max = $this->getLimiteMaxAmis();

        $stmtCount = $db->prepare("SELECT COUNT(*) FROM ChoixCollegue WHERE id_etudiant = ?");
        $stmtCount->execute([$idEtudiant]);
        
        if ($stmtCount->fetchColumn() >= $max) return false;

        $sql = "INSERT IGNORE INTO ChoixCollegue (id_etudiant, id_collegue) VALUES (?, ?)";
        return $db->prepare($sql)->execute([$idEtudiant, $idCollegue]);
    }
}
?>