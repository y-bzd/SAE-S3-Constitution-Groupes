<?php
require_once("modele.php");

class Etudiant extends Modele {
    protected static $objet = "Etudiant";
    protected static $cle = "id_etudiant";

    public function __construct($donnees = NULL) {
        parent::__construct($donnees);
    }

    public static function getProfilComplet($idEtu) {
        $db = Connexion::pdo();
        
        $req = "SELECT * FROM Etudiant WHERE id_etudiant = :id";
        $stmt = $db->prepare($req);
        $stmt->execute([':id' => $idEtu]);
        $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$etudiant) return null;

        $reqNotes = "SELECT libelle_note, valeur_note FROM Note WHERE id_etudiant = :id";
        $stmtNotes = $db->prepare($reqNotes);
        $stmtNotes->execute([':id' => $idEtu]);
        $etudiant['notes'] = $stmtNotes->fetchAll(PDO::FETCH_ASSOC);

        $reqGrp = "SELECT g.libelle_groupe 
                   FROM Groupe g 
                   INNER JOIN Affectation a ON g.id_groupe = a.id_groupe 
                   WHERE a.id_etudiant = :id AND a.affectation_courante = 1";
        $stmtGrp = $db->prepare($reqGrp);
        $stmtGrp->execute([':id' => $idEtu]);
        $grp = $stmtGrp->fetch(PDO::FETCH_ASSOC);
        
        $etudiant['groupe'] = $grp ? $grp['libelle_groupe'] : "Aucun";

        return $etudiant;
    }

    public static function ajouterEtudiant($data) {
        $db = Connexion::pdo();
        $stmtId = $db->query("SELECT MAX(id_etudiant) FROM Etudiant");
        $nextId = $stmtId->fetchColumn() + 1;

        $sql = "INSERT INTO Etudiant (id_etudiant, numero_etudiant, nom_etudiant, prenom_etudiant, email_etudiant, type_bac) 
                VALUES (:id, :num, :nom, :prenom, :email, :bac)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':id' => $nextId,
            ':num' => $data['numero'],
            ':nom' => $data['nom'],
            ':prenom' => $data['prenom'],
            ':email' => $data['email'],
            ':bac' => $data['bac']
        ]);
    }

    public static function modifierEtudiant($id, $data) {
        $db = Connexion::pdo();
        $sql = "UPDATE Etudiant SET numero_etudiant = :num, nom_etudiant = :nom, prenom_etudiant = :prenom, 
                email_etudiant = :email, type_bac = :bac 
                WHERE id_etudiant = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':num' => $data['numero'],
            ':nom' => $data['nom'],
            ':prenom' => $data['prenom'],
            ':email' => $data['email'],
            ':bac' => $data['bac'],
            ':id' => $id
        ]);
    }

    public static function getListeSimple() {
        $db = Connexion::pdo();
        $req = "SELECT id_etudiant, nom_etudiant, prenom_etudiant FROM Etudiant ORDER BY nom_etudiant";
        return $db->query($req)->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>