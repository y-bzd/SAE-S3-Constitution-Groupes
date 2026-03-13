<?php
require_once("controleur.php");
require_once("modele/promotion.php");

class ControleurPublic extends Controleur {
    
    public static function consulterGroupes() {
        if (!isset($_SESSION['utilisateur'])) {
            header("Location: index.php?controleur=controleurUtilisateur&action=afficherFormulaireConnexion");
            exit;
        }
        
        $pagetitle = "Consultation des groupes (Étudiants)";
        
        $db = Connexion::pdo();
        $stmt = $db->query("SELECT id_promotion, libelle_promotion FROM Promotion");
        $lesPromos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $idPromo = $_GET['idPromo'] ?? ($lesPromos[0]['id_promotion'] ?? 1);
        
        $promo = new Promotion();
        $lesGroupes = $promo->getGroupes($idPromo, true);

        require("vue/debut.php");
        require("vue/public/consultationGroupes.php");
        require("vue/fin.php");
    }
}
?>