<?php
require_once("controleur.php");

class ControleurGroupe extends Controleur {
    protected static $objet = "groupe";
    protected static $nomClasse = "Groupe";
    protected static $cle = "id_groupe";

    public static function lireGroupes() {
        $promo = new Promotion();
        $lesGroupes = $promo->getGroupes(1);
        $sansGroupe = $promo->getEtudiantsSansGroupe(1);

        $pagetitle = "Gestion des Groupes et Affectations";
        require("vue/debut.php");
        require("vue/groupe/lesGroupes.php");
        require("vue/fin.php");
    }

    public static function affecter() {
        if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'ADMIN') {
            header("Location: index.php");
            exit;
        }

        if (isset($_POST['id_etudiant']) && isset($_POST['id_groupe'])) {
            $promo = new Promotion();
            $promo->deplacerEtudiant($_POST['id_etudiant'], $_POST['id_groupe']);
        }

        header("Location: index.php?controleur=controleurGroupe&action=lireGroupes");
        exit;
    }

    public static function creerGroupe() {
        if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'ADMIN') {
            header("Location: index.php"); exit;
        }

        if (isset($_POST['libelle']) && isset($_POST['type']) && isset($_POST['capacite'])) {
            $promo = new Promotion();
            $data = [
                'libelle' => $_POST['libelle'],
                'type' => $_POST['type'],
                'capaciteMax' => $_POST['capacite']
            ];
            $promo->createGroupe(1, $data);
        }

        header("Location: index.php?controleur=controleurGroupe&action=lireGroupes");
        exit;
    }

    public static function supprimerGroupe() {
        if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'ADMIN') {
            header("Location: index.php"); exit;
        }

        if (isset($_GET['id_groupe'])) {
            $promo = new Promotion();
            $promo->deleteGroupe($_GET['id_groupe']);
        }

        header("Location: index.php?controleur=controleurGroupe&action=lireGroupes");
        exit;
    }
}
?>