<?php
require_once("modele.php");

class Utilisateur extends Modele {
    protected static $objet = "utilisateur";
    protected static $cle = "id_utilisateur";

    public function __construct($param = NULL) {
        if (is_array($param)) {
            parent::__construct($param);
        }
    }

    public function login($identifiant, $mdp) {
        $req = "SELECT * FROM Utilisateur WHERE identifiant_connexion = :login";
        $stmt = Connexion::pdo()->prepare($req);
        $stmt->execute(['login' => $identifiant]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($mdp, $user['hash_mdp'])) {
            $token = bin2hex(random_bytes(16));
            return [
                "token" => $token,
                "utilisateur" => [
                    "idUtilisateur" => $user['id_utilisateur'],
                    "identifiantConnexion" => $user['identifiant_connexion'],
                    "role" => $user['code_role']
                ]
            ];
        }
        return false;
    }
}
?>