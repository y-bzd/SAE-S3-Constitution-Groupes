<?php
require_once("controleur.php");

class ControleurEtudiant extends Controleur {

    public static function lireEtudiants() {
        $promo = new Promotion();
        $lesEtudiants = $promo->getEtudiants(1); 
        
        $pagetitle = "Liste des Étudiants";
        require("vue/debut.php");
        require("vue/etudiant/lesEtudiants.php");
        require("vue/fin.php");
    }

    public static function monEspace() {
        if (!isset($_SESSION['utilisateur'])) {
            header("Location: index.php?controleur=controleurUtilisateur&action=afficherFormulaireConnexion");
            exit;
        }

        $idEtu = $_SESSION['utilisateur']['idUtilisateur'];

        $modele = new SondageEtudiant();
        $mesCollegues = $modele->getCollegues($idEtu);
        $limiteAmis = $modele->getLimiteMaxAmis();

        $db = Connexion::pdo();
        $sqlTous = "SELECT id_etudiant, nom_etudiant, prenom_etudiant FROM Etudiant WHERE id_etudiant != ? ORDER BY nom_etudiant";
        $stmtTous = $db->prepare($sqlTous);
        $stmtTous->execute([$idEtu]);
        $tous = $stmtTous->fetchAll(PDO::FETCH_ASSOC);

        $sondages = $modele->getSondages();

        $pagetitle = "Mon Espace Étudiant";
        require("vue/debut.php");
        require("vue/etudiant/espace.php");
        require("vue/fin.php");
    }

    public static function ajouterCollegue() {
        if (!isset($_SESSION['utilisateur'])) {
            header("Location: index.php"); 
            exit;
        }

        $idEtu = $_SESSION['utilisateur']['idUtilisateur'];

        if (isset($_POST['id_collegue'])) {
            $modele = new SondageEtudiant();
            $modele->ajouterCollegue($idEtu, $_POST['id_collegue']);
        }
        
        header("Location: index.php?controleur=controleurEtudiant&action=monEspace");
        exit;
    }

    public static function validerSondage() {
        if (!isset($_SESSION['utilisateur'])) {
            header("Location: index.php"); 
            exit;
        }
        
        $idEtu = $_SESSION['utilisateur']['idUtilisateur'];

        if (isset($_POST['id_sondage'])) {
            $modele = new SondageEtudiant();

            if (isset($_POST['reponse_ordre']) && is_array($_POST['reponse_ordre'])) {
                $reponses = array_filter($_POST['reponse_ordre'], function($v) { return $v !== ''; });
                $modele->repondre($idEtu, $_POST['id_sondage'], $reponses);
            }
            elseif (isset($_POST['reponse_unique'])) {
                $modele->repondre($idEtu, $_POST['id_sondage'], $_POST['reponse_unique']);
            }
        }
        header("Location: index.php?controleur=controleurEtudiant&action=monEspace");
    }
}
?>