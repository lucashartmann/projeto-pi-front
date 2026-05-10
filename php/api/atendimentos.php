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

switch ($acao) {

    case "listar_atendimentos":
        listar_atendimentos();
        break;

    default:
        echo json_encode(["erro" => "Ação inválida"]);
        break;
}

function listar_atendimentos()
{
    try {
            $atendimentos = Init::getInstance()->get_lista_atendimentos();
            $lista = [];
            if ($atendimentos) {
                foreach ($atendimentos as $atendimento) {
                    $lista[] = [
                        "id" => $atendimento->get_id(),
                    "corretor" => $atendimento->get_corretor() ? $atendimento->get_corretor()->get_nome() : NULL,
                    "cliente" =>  $atendimento->get_cliente() ? [
                        "id" => $atendimento->get_cliente()->get_id(),
                        "nome" => $atendimento->get_cliente()->get_nome(),
                        # "idade" => $atendimento->get_cliente()->get_idade(),
                        "telefones" => [$atendimento->get_cliente()->get_telefones()],
                        "email" => $atendimento->get_cliente()->get_email(),
                    ] : NULL,
                    "imovel" => $atendimento->get_imovel() ? [
                        "id" => $atendimento->get_imovel()->get_id(),
                        "titulo" => $atendimento->get_imovel()->get_anuncio()->get_titulo() ?: NULL,
                    ] : NULL,
                    "status" =>  $atendimento->get_status() ? $atendimento->get_status()->value : NULL,
                ];
            }
        }
        echo (json_encode($lista));
    } catch (Exception $e) {
        echo json_encode(["erro" => "Erro ao listar atendimentos: " . $e->getMessage()]);
    }
}
