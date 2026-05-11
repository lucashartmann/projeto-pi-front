<?php

require_once __DIR__ . '/../model/cliente.php';
require_once __DIR__ . '/../model/corretor.php';
require_once __DIR__ . '/../model/imovel.php';
require_once __DIR__ . '/../model/captador.php';
require_once __DIR__ . '/../model/atendimento.php';
require_once __DIR__ . '/../model/endereco.php';
require_once __DIR__ . '/../model/anuncio.php';
require_once __DIR__ . '/../model/vendaAluguel.php';
require_once __DIR__ . '/../model/condominio.php';
require_once __DIR__ . '/../model/gerente.php';
require_once __DIR__ . '/../model/usuario.php';
require_once __DIR__ . '/../model/proprietario.php';
require_once __DIR__ . '/../model/__init__.php';
require_once __DIR__ . '/../controller/controller.php';

ob_start();
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);
$acao = $_GET['acao'] ?? '';
$controller = new controller();

switch ($acao) {

    case "cadastrar_imovel":
        $body = file_get_contents("php://input");
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $resultado = (["erro" => "JSON inválido"]);
            return;
        }

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
            $resultado = (["erro" => "ID do imóvel não fornecido"]);
        }
        break;

    case "apagar_imovel":
        $id = $_GET['id'] ?? null;
        if ($id) {
            $resultado = $controller->apagarImovel($id);
        } else {
            $resultado = (["erro" => "ID do imóvel não fornecido"]);
        }
        break;


    default:
        $resultado = (["erro" => "Ação inválida"]);
        break;
}

if ($acao) {
    echo json_encode($resultado);
}
