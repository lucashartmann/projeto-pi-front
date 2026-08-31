<?php


require_once __DIR__ . '/../controllers/visitaController.php';
require_once __DIR__ . '/../model/seguranca.php';


// ob_start();
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);
$acao = $_GET['acao'] ?? '';

switch ($acao) {

    case "cadastrar_visita":
        Seguranca::verificarAcesso();
        // cadastrar();
        break;
    case "cadastrar_vistoria":
        Seguranca::verificarAcesso();
        // cadastrar();
        break;

    default:
        error_log(json_encode(["status" => "erro", "mensagem" => "Ação inválida"]));
        break;
}

if (!headers_sent()) {
    http_response_code(200);
    echo json_encode($resultado);
} else {
    error_log("Erro: Cabeçalhos já enviados, não é possível enviar a resposta JSON.");
}
