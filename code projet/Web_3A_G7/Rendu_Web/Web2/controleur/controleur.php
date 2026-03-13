<?php
require_once("modele/modele.php");
require_once("modele/etudiant.php");
require_once("modele/utilisateur.php");
require_once("modele/groupe.php");
require_once("modele/promotion.php");
require_once("modele/responsable.php");
require_once("modele/sondageEtudiant.php");

class Controleur {
    
    public static function lireObjets() {
        $t = static::$objet;
        $classe = static::$nomClasse;
        
        $pagetitle = "Liste des " . ucfirst($t) . "s";
        
        $lesObjets = $classe::getAll();
        
        $nomVariable = "les" . ucfirst($t) . "s";
        $$nomVariable = $lesObjets;
        
        require("vue/debut.php");
        require("vue/" . $t . "/les" . ucfirst($t) . "s.php");
        require("vue/fin.php");
    }
}
?>