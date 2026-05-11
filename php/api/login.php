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
ini_set('display_errors', 0);
error_reporting(E_ALL);
$acao = $_GET['acao'] ?? '';

switch ($acao) {

    case "login":
        verificarLogin();
        break;

    case "deslogar":
        deslogar();
        break;

    case "getUsuario":
        carregarUsuario();
        break;

    default:
        echo json_encode(["erro" => "Ação inválida"]);
        break;
}

function deslogar()
{
    try {
        session_start();
        session_destroy();

        echo json_encode(["status" => "ok"]);
    } catch (Exception $e) {
        echo json_encode(["erro" => "Erro ao deslogar: " . $e->getMessage()]);
    }
}

function carregarUsuario()
{
    try {
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
    } catch (Exception $e) {
        echo json_encode(["erro" => "Erro ao carregar usuário: " . $e->getMessage()]);
    }
}


function verificarLogin()
{


    try {
        session_start();
        $data = json_decode(file_get_contents('php://input'), true);

        $usuario = $data['usuario'] ?? '';
        $senha = $data['senha'] ?? '';

        if (!$usuario || !$senha) {
            echo json_encode(["status" => "erro", "mensagem" => "Usuário ou senha não fornecidos"]);
            return;
        }

        $consulta = Init::getInstance()->verificarUsuario($usuario, $senha);

        if ($consulta) {
            $_SESSION['usuario_id'] = $consulta->getId();
            $_SESSION['tipo'] = $consulta->getTipo() ?? NULL;
            echo (json_encode(["status" => "ok", "usuario" => [
                "id" => $consulta->getId(),
                "nome" => $consulta->getNome(),
                "tipo" => $consulta->getTipo(),
            ]]));
        } else {
            echo (json_encode(["status" => "erro", "mensagem" => "Usuário ou senha incorretos"]));
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["erro" => "Erro ao verificar login: " . $e->getMessage()]);
    }
}
