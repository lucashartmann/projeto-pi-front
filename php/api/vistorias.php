<?php


require_once __DIR__ . '/../controllers/vistoriaController.php';
require_once __DIR__ . '/../model/seguranca.php';


// ob_start();
header('Content-Type: application/json');
$isLocal = $_SERVER['SERVER_NAME'] === 'localhost';
ini_set('display_errors', $isLocal ? '1' : '0');
ini_set('display_startup_errors', $isLocal ? '1' : '0');
error_reporting(E_ALL);
$acao = $_GET['acao'] ?? '';
$controller = new VistoriaController();


switch ($acao) {

    case "cadastrar":
        Seguranca::verificarAcesso();
        $data = $_POST;
        if (!$_POST) {
            $body = file_get_contents("php://input");
            $data = json_decode($body, true);
        }
        $resultado = $controller->cadastrar($data);
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


