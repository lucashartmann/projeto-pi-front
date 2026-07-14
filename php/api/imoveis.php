<?php

require_once __DIR__ . '/../controllers/imovelController.php';

// ob_start();
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);
$acao = $_GET['acao'] ?? '';
$controller = new ImovelController();

switch ($acao) {

    case "cadastrar_imovel":
        // $body = file_get_contents("php://input");
        $data = $_POST;

        // if (json_last_error() !== JSON_ERROR_NONE) {
        //     $resultado = (["status" => "erro", "mensagem" => "JSON inválido"]);
        //     return;
        // }

        $resultado = $controller->cadastrarImovel($data);

        break;

    case 'listar_imoveis':
        $resultado = $controller->getListaImoveis();
        break;

    case 'listar_imoveis_disponiveis':
        $resultado = $controller->getListaImoveisDisponiveis();
        break;

    case "get_dados_imovel":
        $id = $_GET['id'] ?? null;
        if ($id) {
            $resultado = $controller->getImovelPorId($id);
        } else {
            $resultado = (["status" => "erro", "mensagem" => "ID do imóvel não fornecido"]);
        }
        break;

    case "apagar_imovel":
        $id = $_GET['id'] ?? null;
        if ($id) {
            $resultado = $controller->apagarImovel($id);
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
