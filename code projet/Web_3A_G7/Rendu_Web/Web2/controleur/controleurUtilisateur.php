<?php
require_once("controleur.php");

class ControleurUtilisateur extends Controleur {
    protected static $objet = "utilisateur";
    protected static $nomClasse = "Utilisateur";
    protected static $cle = "id_utilisateur";
    
    public static function afficherFormulaireConnexion() {
        $pagetitle = "Connexion Responsable";
        require("vue/debut.php");
        require("vue/utilisateur/formulaireConnexion.php");
        require("vue/fin.php");
    }

    public static function connecter() {
        if (isset($_POST['login']) && isset($_POST['mdp'])) {
            $login = $_POST['login'];
            $mdp = $_POST['mdp'];

            $user = new Utilisateur(); 
            $res = $user->login($login, $mdp);

            if ($res) {
                $_SESSION['utilisateur'] = $res['utilisateur'];
                header("Location: index.php?controleur=controleurEtudiant&action=lireEtudiants");
            } else {
                $erreur = "Identifiants incorrects.";
                $pagetitle = "Erreur Connexion";
                require("vue/debut.php");
                require("vue/utilisateur/formulaireConnexion.php");
                require("vue/fin.php");
            }
        }
    }

    public static function deconnecter() {
        session_destroy();
        header("Location: index.php");
    }
}
?>