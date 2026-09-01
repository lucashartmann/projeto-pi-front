<?php

require_once __DIR__ . '/../controllers/historicoController.php';
require_once __DIR__ . '/../model/seguranca.php';


header('Content-Type: application/json');
$isLocal = $_SERVER['SERVER_NAME'] === 'localhost';
ini_set('display_errors', $isLocal ? '1' : '0');
ini_set('display_startup_errors', $isLocal ? '1' : '0');
error_reporting(E_ALL);
$acao = $_GET['acao'] ?? '';
$controller = new HistoricoController();

switch ($acao) {

    case "listarPorIdImovel":
        Seguranca::verificarAcesso();
        $id = $_GET['id'] ?? null;
        if ($id === null) {
            $resultado = (["status" => "erro", "mensagem" => "ID do imóvel não fornecido"]);
        } else {
            $resultado = $controller->listarPorIdImovel((int)$id);
        }
        break;

    case "listarPorIdCliente":
        Seguranca::verificarAcesso();
        $id = $_GET['id'] ?? null;
        if ($id === null) {
            $resultado = (["status" => "erro", "mensagem" => "ID do cliente não fornecido"]);
        } else {
            $resultado = $controller->listarPorIdCliente((int)$id);
        }
        break;

    default:
        $resultado = (["status" => "erro", "mensagem" => "Ação inválida"]);
        break;
}

if (!headers_sent()) {
    http_response_code(200);
    echo json_encode($resultado);
} else {
    error_log("Erro: Cabeçalhos já enviados, não é possível enviar a resposta JSON.");
}
