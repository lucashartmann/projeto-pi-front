<?php

require_once __DIR__ . '/../controllers/imovelController.php';
require_once __DIR__ . '/../model/seguranca.php';

// ob_start();
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);
$acao = $_GET['acao'] ?? '';
$controller = new ImovelController();

switch ($acao) {

    case "cadastrarClick":
        Seguranca::verificarAcesso();
        $id = $_GET['id'] ?? null;
        if ($id) {
            $resultado = $controller->buscarPorId($id);
        } else {
            $resultado = (["status" => "erro", "mensagem" => "ID do imóvel não fornecido"]);
        }
        $resultado = $controller->cadastrarClick($id);
        break;

    case "cadastrar":
        Seguranca::verificarAcesso();
        $data = $_POST;
        if (!$_POST) {
            $body = file_get_contents("php://input");
            $data = json_decode($body, true);
        }
        $resultado = $controller->cadastrar($data);
        break;

    case 'listar':
        Seguranca::verificarAcesso();
        $resultado = $controller->listar();
        break;

    case 'listar_disponiveis':
        $resultado = $controller->listarDisponiveis();
        break;

    case "get_imovel":
        Seguranca::verificarAcesso();
        $id = $_GET['id'] ?? null;
        if ($id) {
            $resultado = $controller->buscarPorId($id);
        } else {
            $resultado = (["status" => "erro", "mensagem" => "ID do imóvel não fornecido"]);
        }
        break;

    case "apagar":
        Seguranca::verificarAcesso();
        $id = $_GET['id'] ?? null;
        if ($id) {
            $resultado = $controller->apagar($id);
        } else {
            $resultado = (["status" => "erro", "mensagem" => "ID do imóvel não fornecido"]);
        }
        break;

    case "listar_destacados":
        $resultado = $controller->listarDestacados();
        break;

    case "destacar":
        Seguranca::verificarAcesso();
        $id = $_GET['id'] ?? null;
        if ($id) {
            $resultado = $controller->destacar($id);
        } else {
            $resultado = (["status" => "erro", "mensagem" => "ID do imóvel não fornecido"]);
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
