<?php


require_once __DIR__ . '/../controllers/loginController.php';
require_once __DIR__ . '/../controllers/usuarioController.php';
require_once __DIR__ . '/../controllers/atendimentoController.php';
// ob_start();
header('Content-Type: application/json');
// Init::initialize();
ini_set('display_errors', 0);
error_reporting(E_ALL);
$acao = $_GET['acao'] ?? '';
$controller = new LoginController();

switch ($acao) {

    case "login":
        $body = file_get_contents("php://input");
        $data = json_decode($body, true);

        if (!is_array($data)) {
            $resultado = (["status" => "erro", "mensagem" => "JSON inválido"]);
            return;
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            $resultado = (["status" => "erro", "mensagem" => "JSON inválido"]);
            return;
        }
        $resultado = $controller->verificarLogin($data);
        break;

    case "cadastro":
        $controller = new UsuarioController();
        $body = file_get_contents("php://input");
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $resultado = (["status" => "erro", "mensagem" => "JSON inválido"]);
            return;
        }
        $resultado = $controller->atualizarUsuario($data);
        break;

    case "deslogar":
        $resultado = $controller->deslogar();
        break;

    case "get_usuario":
        $resultado = $controller->carregarUsuario();
        break;

    case "favoritar_imoveis":
        $body = file_get_contents("php://input");
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $resultado = (["status" => "erro", "mensagem" => "JSON inválido"]);
            return;
        }
        $resultado = $controller->favoritarImoveis($data);
        break;

    case "get_favoritos":
        $resultado = $controller->carregarFavoritos();
        break;

    case "get_atendimentos":
        $controller = new AtendimentoController();
        $resultado = $controller->carregarAtendimentos();
        break;

    case "recuperar_senha":
        $body = file_get_contents("php://input");
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $resultado = (["status" => "erro", "mensagem" => "JSON inválido"]);
            return;
        }
        $resultado = $controller->recuperarSenha($data);
        break;

    default:
        $resultado = (["status" => "erro", "mensagem" => "Ação inválida"]);
        break;
}

if ($acao) {
    echo json_encode($resultado);
}
