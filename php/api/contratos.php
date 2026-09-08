<?php



require_once __DIR__ . '/../controllers/contratoController.php';
require_once __DIR__ . '/../model/seguranca.php';


// ob_start();
header('Content-Type: application/json');
$isLocal = $_SERVER['SERVER_NAME'] === 'localhost';
ini_set('display_errors', $isLocal ? '1' : '0');
ini_set('display_startup_errors', $isLocal ? '1' : '0');
error_reporting(E_ALL);
$acao = $_GET['acao'] ?? '';
$controller = new ContratoController();

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

    case "listar":
        Seguranca::verificarAcesso();
        $resultado = $controller->listar();
        break;

    case "remover":
        Seguranca::verificarAcesso();
        $id = $_GET['id'] ?? null;
        $resultado = $controller->remover($id);
        break;

    case "buscar_por_id":
        Seguranca::verificarAcesso();
        $id = $_GET['id'] ?? null;
        $resultado = $controller->buscarPorId($id);
        break;

    default:
        $resultado = [
            "status" => "erro",
            "mensagem" => "Ação inválida"
        ];
}

if (!headers_sent()) {
    http_response_code(200);
    echo json_encode($resultado);
} else {
    error_log("Erro: Cabeçalhos já enviados, não é possível enviar a resposta JSON.");
}
