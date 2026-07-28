<?php


require_once __DIR__ . '/../controllers/usuarioController.php';

// ob_start();
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);
$acao = $_GET['acao'] ?? '';
$controller = new UsuarioController();

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

if ($acao) {
    echo json_encode($resultado);
}
