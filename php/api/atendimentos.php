<?php


require_once __DIR__ . '/../controllers/atendimentoController.php';


// ob_start();
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);
$acao = $_GET['acao'] ?? '';
$controller = new AtendimentoController();

switch ($acao) {

    case "cadastrar":
        $idImovel = $_GET['idImovel'] ?? null;
        $resultado = $controller->cadastrar($idImovel);
        break;

    case "listar":
        $resultado = $controller->listar();
        break;

    default:
        $resultado = (["status" => "erro", "mensagem" => "Ação inválida"]);
        break;
}

if ($acao) {
    echo json_encode($resultado);
}
