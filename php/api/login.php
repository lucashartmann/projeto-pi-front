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
// Init::initialize();

$acao = $_GET['acao'] ?? '';

switch ($acao) {

    case "login":
        verificar_login();
        break;

    case "deslogar":
        deslogar();
        break;

    case "get_usuario":
        carregar_usuario();
        break;

    default:
        echo json_encode(["erro" => "Ação inválida"]);
        break;
}

function deslogar()
{
    session_start();
    session_destroy();

    echo json_encode(["status" => "ok"]);
}

function carregar_usuario()
{
    session_start();
    if (isset($_SESSION['usuario_id'])) {
        echo json_encode([
            "status" => "ok",
            "tipo" => $_SESSION['tipo']
        ]);
    } else {
        echo json_encode([
            "status" => "erro",
            "mensagem" => "Usuário não logado"
        ]);
    }
}


function verificar_login()
{
    session_start();

    try {
        $data = json_decode(file_get_contents('php://input'), true);

        $usuario = $data['usuario'] ?? '';
        $senha = $data['senha'] ?? '';

        if (!$usuario || !$senha) {
            echo json_encode(["status" => "erro", "mensagem" => "Usuário ou senha não fornecidos"]);
            return;
        }

        $consulta = Init::getInstance()->verificar_usuario($usuario, $senha);

        if ($consulta) {
            $_SESSION['usuario_id'] = $consulta->get_id();
            $_SESSION['tipo'] = $consulta->get_tipo() ?? NULL;
            echo (json_encode(["status" => "ok", "usuario" => [
                "id" => $consulta->get_id(),
                "nome" => $consulta->get_nome(),
                "tipo" => $consulta->get_tipo(),
            ]]));
        } else {
            echo (json_encode(["status" => "erro", "mensagem" => "Usuário ou senha incorretos"]));
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["status" => "erro", "mensagem" => "Erro interno"]);
    }
}
