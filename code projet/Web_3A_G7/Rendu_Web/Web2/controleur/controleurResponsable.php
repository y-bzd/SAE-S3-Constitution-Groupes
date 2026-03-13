<?php
require_once("controleur.php");
require_once("modele/promotion.php");
require_once("modele/responsable.php");

class ControleurResponsable extends Controleur {

public static function panel() {
        if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'ADMIN') {
            header("Location: index.php"); exit;
        }
        
        $lesContraintes = Responsable::getContraintes();
        
        require_once("modele/sondageEtudiant.php");
        $modeleSondage = new SondageEtudiant();
        $lesSondages = $modeleSondage->getSondages();
        
        $pagetitle = "Administration";
        require("vue/debut.php");
        require("vue/responsable/panel.php");
        require("vue/fin.php");
    }

    public static function voirResultats() {
        if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'ADMIN') {
            header("Location: index.php"); exit;
        }

        if (isset($_GET['id_sondage'])) {
            $resp = new Responsable();
            $resultats = $resp->getResultatsDetailles($_GET['id_sondage']);
            
            $pagetitle = "Résultats : " . $resultats['info']['critere_sondage'];
            require("vue/debut.php");
            require("vue/responsable/resultatsSondage.php");
            require("vue/fin.php");
        } else {
            header("Location: index.php?controleur=controleurResponsable&action=panel");
        }
    }

    public static function traiterImport() {
        if (isset($_FILES['csv']) && $_FILES['csv']['error'] == 0) {
            $admin = new Responsable();
            $admin->importerNotes($_FILES['csv']['tmp_name']);
        }
        header("Location: index.php?controleur=controleurResponsable&action=panel");
    }

    public static function creerSondage() {
        if (isset($_POST['titre']) && isset($_SESSION['utilisateur'])) {
            $admin = new Responsable();
            $options = explode(",", $_POST['options']);
            $idResp = $_SESSION['utilisateur']['idUtilisateur']; 
            $admin->creerSondage($_POST['titre'], 'QCM', $idResp, $options);
        }
        header("Location: index.php?controleur=controleurResponsable&action=panel");
    }

    public static function ajouterContrainte() {
        if (isset($_POST['type']) && isset($_POST['param'])) {
            $admin = new Responsable();
            $admin->ajouterContrainte($_POST['type'], $_POST['param']);
        }
        header("Location: index.php?controleur=controleurResponsable&action=panel");
    }

    public static function supprimerContrainte() {
        if (isset($_GET['id'])) {
            $admin = new Responsable();
            $admin->supprimerContrainte($_GET['id']);
        }
        header("Location: index.php?controleur=controleurResponsable&action=panel");
    }

    public static function exportCsv() {
        if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'ADMIN') {
            header("Location: index.php");
            exit;
        }

        $db = Connexion::pdo();
        $sql = "SELECT * FROM Etudiant ORDER BY nom_etudiant";
        $etudiants = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=export_etudiants_complet.csv');

        $output = fopen('php://output', 'w');
        
        fputs($output, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
        fputcsv($output, array('ID', 'Numero', 'Nom', 'Prenom', 'Email', 'Telephone', 'Adresse', 'Ville', 'CP', 'Bac', 'Redoublement', 'Parcours'), ";");

        foreach ($etudiants as $etu) {
            fputcsv($output, array(
                $etu['id_etudiant'],
                $etu['numero_etudiant'],
                $etu['nom_etudiant'],
                $etu['prenom_etudiant'],
                $etu['email_etudiant'],
                $etu['tel_etudiant'] ?? '',
                $etu['rue_etudiant'] ?? '',
                $etu['ville_etudiant'] ?? '',
                $etu['code_postal_etudiant'] ?? '',
                $etu['type_bac'],
                $etu['periode_redoublement'],
                $etu['parcours_etudiant']
            ), ";");
        }
        fclose($output);
        exit();
    }

    public static function gestionEtudiants() {
        if (!isset($_SESSION['utilisateur']) || 
           ($_SESSION['utilisateur']['role'] !== 'ADMIN' && $_SESSION['utilisateur']['role'] !== 'ENSEIGNANT')) {
            header("Location: index.php"); exit;
        }

        $promo = new Promotion();
        $lesEtudiants = $promo->getEtudiants(1); 
        
        $pagetitle = "Gestion des Étudiants";
        require("vue/debut.php");
        require("vue/responsable/gestionEtudiants.php");
        require("vue/fin.php");
    }

    public static function ajouterEtudiant() {
        self::verifierDroitAdmin();
        if (isset($_POST['numero'])) {
            Etudiant::ajouterEtudiant($_POST);
        }
        header("Location: index.php?controleur=controleurResponsable&action=gestionEtudiants");
    }

    public static function editerEtudiant() {
        self::verifierDroitAdmin();
        if (isset($_POST['id_etudiant'])) {
            Etudiant::modifierEtudiant($_POST['id_etudiant'], $_POST);
        }
        header("Location: index.php?controleur=controleurResponsable&action=gestionEtudiants");
    }

    public static function supprimerEtudiant() {
        self::verifierDroitAdmin();
        if (isset($_GET['id'])) {
            Etudiant::deleteObjetById($_GET['id']);
        }
        header("Location: index.php?controleur=controleurResponsable&action=gestionEtudiants");
    }

    private static function verifierDroitAdmin() {
        if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'ADMIN') {
            header("Location: index.php?controleur=controleurUtilisateur&action=afficherFormulaireConnexion");
            exit;
        }
    }

    public static function genererAuto() {
        if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'ADMIN') {
            header("Location: index.php"); exit;
        }

        require_once("modele/algorithmes.php");

        if (isset($_POST['algo'])) {
            $idPromo = 1;
            
            switch($_POST['algo']) {
                case 'g_distributeur':
                    Algorithmes::gloutonDistributeur($idPromo);
                    break;
                case 'g_compensateur':
                    Algorithmes::gloutonCompensateur($idPromo);
                    break;
                case 'g_covoit_equilibre':
                    Algorithmes::gloutonCovoitEquilibre($idPromo);
                    break;
                case 'g_covoit_niveau':
                    Algorithmes::gloutonCovoitNiveau($idPromo);
                    break;
                case 'fb_simple':
                    Algorithmes::forceBruteSimple($idPromo);
                    break;
                case 'fb_blocs':
                    Algorithmes::forceBruteBlocs($idPromo);
                    break;
            }
        }
        header("Location: index.php?controleur=controleurGroupe&action=lireGroupes");
        exit;
    }
}
?>