<?php
require_once("config/connexion.php");

class Modele {
    public function get($attribut) {
        return $this->$attribut;
    }

    public function set($attribut, $valeur) {
        $this->$attribut = $valeur;
    }

    public function __construct($donnees = NULL) {
        if (!is_null($donnees) && is_array($donnees)) {
            foreach ($donnees as $attribut => $valeur) {
                $this->set($attribut, $valeur);
            }
        }
    }

    public static function getAll() {
        $table = static::$objet;
        $requete = "SELECT * FROM $table";
        $resultat = Connexion::pdo()->query($requete);
        $resultat->setFetchmode(PDO::FETCH_CLASS, static::class);
        return $resultat->fetchAll();
    }

    public static function getObjetById($id) {
        $tableObjet = static::$objet;
        $tableCle = static::$cle;
        $requete = "SELECT * FROM $tableObjet WHERE $tableCle = :id";
        $requete_prep = Connexion::pdo()->prepare($requete);
        $values = array("id" => $id);
        $requete_prep->execute($values);
        $requete_prep->setFetchmode(PDO::FETCH_CLASS, static::class);
        return $requete_prep->fetch();
    }

    public static function deleteObjetById($id) {
        $tableObjet = static::$objet;
        $tableCle = static::$cle;
        $requete = "DELETE FROM $tableObjet WHERE $tableCle = :id";
        $requete_prep = Connexion::pdo()->prepare($requete);
        $values = array("id" => $id);
        $requete_prep->execute($values);
    }
}
?>