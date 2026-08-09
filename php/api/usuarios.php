<?php


require_once __DIR__ . '/../controllers/pessoaController.php';

// ob_start();
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);
$acao = $_GET['acao'] ?? '';
$controller = new PessoaController();

switch ($acao) {

    case "cadastro":
        $body = file_get_contents("php://input");
        $data = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $resultado = (["status" => "erro", "mensagem" => "JSON inválido"]);
            return;
        }
        $resultado = $controller->atualizar($data);
        break;

    case "apagar":
        $body = file_get_contents("php://input");
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $resultado = (["status" => "erro", "mensagem" => "JSON inválido"]);
            return;
        }

        $resultado = $controller->apagar($data);
        break;

    case "atualizar":
        $body = file_get_contents("php://input");
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $resultado = (["status" => "erro", "mensagem" => "JSON inválido"]);
            return;
        }

        $resultado = $controller->atualizar($data);
        break;

    case "buscar":
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            $resultado = (["status" => "erro", "mensagem" => "ID do usuário não fornecido"]);
            return;
        }
        $resultado = $controller->buscarPorId($id);
        break;


    case "listar":
        $resultado = $controller->listar();
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
