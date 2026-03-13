<?php
session_start();
require_once("config/connexion.php");
Connexion::connect();
require_once("controleur/controleur.php");

$tableauControleurs = ["controleurUtilisateur", "controleurEtudiant", "controleurGroupe", "controleurResponsable", "controleurPublic"];

$actionParDefaut = [
    "controleurUtilisateur" => "afficherFormulaireConnexion",
    "controleurEtudiant" => "lireEtudiants",
    "controleurGroupe" => "lireGroupes",
    "controleurResponsable" => "panel",
    "controleurPublic" => "consulterGroupes"
];

$controleur = "controleurUtilisateur";
if(isset($_GET["controleur"]) && in_array($_GET["controleur"], $tableauControleurs)) {
    $controleur = $_GET["controleur"];
}

require_once("controleur/$controleur.php");

if(isset($_GET["action"]) && in_array($_GET["action"], get_class_methods($controleur))) {
    $action = $_GET["action"];
} else {
    $action = $actionParDefaut[$controleur] ?? "afficherFormulaireConnexion";
}

$controleur::$action();
?>