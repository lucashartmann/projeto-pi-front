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

    case "listarAtendimentos":
        listarAtendimentos();
        break;

    default:
        echo json_encode(["erro" => "Ação inválida"]);
        break;
}

function listarAtendimentos()
{
    try {
        $atendimentos = Init::getInstance()->getListaAtendimentos();
        $lista = [];
        if ($atendimentos) {
            foreach ($atendimentos as $atendimento) {
                $lista[] = [
                    "id" => $atendimento->getid(),
                    "corretor" => $atendimento->getCorretor() ? $atendimento->getCorretor()->getNome() : NULL,
                    "cliente" =>  $atendimento->getCliente() ? [
                        "id" => $atendimento->getCliente()->getid(),
                        "nome" => $atendimento->getCliente()->getNome(),
                        # "idade" => $atendimento->getCliente()->getidade(),
                        "telefones" => [$atendimento->getCliente()->getTelefones()],
                        "email" => $atendimento->getCliente()->getEmail(),
                    ] : NULL,
                    "imovel" => $atendimento->getImovel() ? [
                        "id" => $atendimento->getImovel()->getid(),
                        "titulo" => $atendimento->getImovel()->getAnuncio()->getTitulo() ?: NULL,
                    ] : NULL,
                    "status" =>  $atendimento->getStatus() ? $atendimento->getStatus()->value : NULL,
                ];
            }
        }
        echo (json_encode($lista));
    } catch (Exception $e) {
        echo json_encode(["erro" => "Erro ao listar atendimentos: " . $e->getMessage()]);
    }
}
