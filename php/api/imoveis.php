<?php

require_once __DIR__ . '/../model/cliente.php';
require_once __DIR__ . '/../model/corretor.php';
require_once __DIR__ . '/../model/imovel.php';
require_once __DIR__ . '/../model/captador.php';
require_once __DIR__ . '/../model/atendimento.php';
require_once __DIR__ . '/../model/endereco.php';
require_once __DIR__ . '/../model/anuncio.php';
require_once __DIR__ . '/../model/venda_aluguel.php';
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

    case "cadastrarImovel":
        $body = file_get_contents("php://input");
        $data = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(["erro" => "JSON inválido"]);
            return;
        }
        
        $resultado = $controller->cadastrarImovel($data);
        echo json_encode($resultado);
        break;

    case 'listarImoveis':
        $resultado = $controller->getListaImoveis();
        echo json_encode($resultado);
        break;

    case 'listarImoveisDisponiveis':
        $resultado = $controller->getListaImoveisDisponiveis();
        echo json_encode($resultado);
        break;

    case "getDadosImovel":
        $id = $_GET['id'] ?? null;
        if ($id) {
            $resultado = $controller->getImovelPorId($id);
            echo json_encode($resultado);
        } else {
            echo json_encode(["erro" => "ID do imóvel não fornecido"]);
        }
        break;

    case "apagarImovel":
        $id = $_GET['id'] ?? null;
        if ($id) {
            $resultado = $controller->apagarImovel($id);
            echo json_encode($resultado);
        } else {
            echo json_encode(["erro" => "ID do imóvel não fornecido"]);
        }
        break;


    default:
        echo json_encode(["erro" => "Ação inválida"]);
        break;
}


