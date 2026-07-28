<?php


require_once __DIR__ . '/../controllers/visitaController.php';


// ob_start();
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);
$acao = $_GET['acao'] ?? '';

switch ($acao) {

    case "cadastrar_visita":
        // cadastrar();
        break;
    case "cadastrar_vistoria":
        // cadastrar();
        break;

    default:
        echo json_encode(["status" => "erro", "mensagem" => "Ação inválida"]);
        break;
}
