<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); 
    exit;
}

require_once("config/connexion.php");
require_once("modele/utilisateur.php");
require_once("modele/promotion.php");

Connexion::connect();
if (!Connexion::pdo()) {
    http_response_code(500);
    echo json_encode(["message" => "Erreur de connexion BDD"]);
    exit;
}

$path_info = trim(str_replace(dirname($_SERVER['SCRIPT_NAME']), '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)), '/');
$path_info = str_replace('api.php', '', $path_info);
$path_info = trim($path_info, '/');
if (strpos($path_info, 'api/') === 0) $path_info = substr($path_info, 4);
elseif ($path_info === 'api') $path_info = '';

$urlParts = explode('/', $path_info);
$resource = $urlParts[0] ?? null;
$id = $urlParts[1] ?? null;
$subResource = $urlParts[2] ?? null;
$subId = $urlParts[3] ?? null;
$method = $_SERVER['REQUEST_METHOD'];

switch ($resource) {
    case 'auth':
        if ($id === 'login') {
            if ($method === 'POST') {
                $json = file_get_contents("php://input");
                $data = json_decode($json, true);
                if (isset($data['identifiantConnexion'], $data['hashMdp'])) {
                    $userObj = new Utilisateur(); 
                    $result = $userObj->login($data['identifiantConnexion'], $data['hashMdp']);
                    if ($result) echo json_encode($result);
                    else { http_response_code(401); echo json_encode(["message" => "Identifiants incorrects"]); }
                } else { http_response_code(400); echo json_encode(["message" => "Données incomplètes"]); }
            } else {
                http_response_code(405); echo json_encode(["message" => "Méthode non autorisée"]);
            }
        } else {
            http_response_code(404); echo json_encode(["message" => "Endpoint introuvable"]);
        }
        break;

    case 'promotions':
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';
        if (!preg_match('/(Bearer|Token)\s+([a-zA-Z0-9]+)/', $authHeader)) {
            http_response_code(401); echo json_encode(["message" => "Non autorisé"]); exit;
        }

        $promoObj = new Promotion(); 

        if ($id && $subResource === 'etudiants') {
            if ($method === 'GET') {
                echo json_encode($promoObj->getEtudiants($id));
            } else {
                http_response_code(405); echo json_encode(["message" => "Méthode non autorisée"]);
            }
        }
        elseif ($id && $subResource === 'groupes') {
            if (!$subId) {
                if ($method === 'GET') {
                    echo json_encode($promoObj->getGroupes($id));
                } elseif ($method === 'POST') {
                    $json = file_get_contents("php://input");
                    $data = json_decode($json, true);
                    if (isset($data['libelle'])) {
                        $newId = $promoObj->createGroupe($id, $data);
                        http_response_code(201); echo json_encode(["id" => $newId]);
                    } else { http_response_code(400); }
                } else {
                    http_response_code(405); echo json_encode(["message" => "Méthode non autorisée"]);
                }
            }
            else {
                if ($method === 'DELETE') {
                    $promoObj->deleteGroupe($subId);
                    echo json_encode(["message" => "Groupe supprimé"]);
                } else {
                    http_response_code(405); echo json_encode(["message" => "Méthode non autorisée"]);
                }
            }
        }
        elseif ($id && $subResource === 'affectations') {
             if ($method === 'PUT') {
                 $json = file_get_contents("php://input");
                 $data = json_decode($json, true);
                 $promoObj->sauvegarderAffectations($id, $data);
                 echo json_encode(["message" => "Affectations mises à jour"]);
             } else {
                 http_response_code(405); echo json_encode(["message" => "Méthode non autorisée"]);
             }
        }
        else {
            http_response_code(404); echo json_encode(["message" => "Ressource introuvable"]);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(["message" => "Endpoint introuvable"]);
        break;
}
?>