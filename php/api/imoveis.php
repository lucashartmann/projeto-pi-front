<?php

require_once __DIR__ . '/../controllers/imovelController.php';

// ob_start();
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);
$acao = $_GET['acao'] ?? '';
$controller = new ImovelController();

switch ($acao) {

    case "cadastrarClick":
        $id = $_GET['id'] ?? null;
        if ($id) {
            $resultado = $controller->buscarPorId($id);
        } else {
            $resultado = (["status" => "erro", "mensagem" => "ID do imóvel não fornecido"]);
        }
        $resultado = $controller->cadastrarClick($id);
        break;

    case "cadastrar":
        $data = $_POST;
        $resultado = $controller->cadastrar($data);
        break;

    case 'listar':
        $resultado = $controller->listar();
        break;

    case 'listar_disponiveis':
        $resultado = $controller->listarDisponiveis();
        break;

    case "get_imovel":
        $id = $_GET['id'] ?? null;
        if ($id) {
            $resultado = $controller->buscarPorId($id);
        } else {
            $resultado = (["status" => "erro", "mensagem" => "ID do imóvel não fornecido"]);
        }
        break;

    case "apagar":
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

if ($acao) {
    echo json_encode($resultado);
}
