<?php

require_once __DIR__ . '/../dao/visitaDAO.php';
require_once __DIR__ . '/../model/visita.php';
require_once __DIR__ . '/../model/imovel.php';
require_once __DIR__ . '/../model/cliente.php';
require_once __DIR__ . '/../dao/imovelDAO.php';
require_once __DIR__ . '/../dao/pessoaDAO.php';
require_once __DIR__ . '/pessoaController.php';
require_once __DIR__ . '/imovelController.php';

$isLocal = $_SERVER['SERVER_NAME'] === 'localhost';
ini_set('display_errors', $isLocal ? '1' : '0');
ini_set('display_startup_errors', $isLocal ? '1' : '0');
error_reporting(E_ALL);

class VisitaController
{

    public function montarJson(array $visitas)
    {
        $lista = [];
        $pessoaController = new PessoaController();
        $imovelController = new ImovelController();
        if (!$visitas) {
            return $lista;
        }
        foreach ($visitas as $visita) {
            $lista[] = [
                "id" => $visita->getId(),
                "nome" => $visita->getNome(),
                "data" => $visita->getData()->format('Y-m-d H:i'),
                "imovel" => $visita->getImovel() ? $imovelController->montarJson([$visita->getImovel()])[0] : NULL,
                "cliente" => $visita->getCliente() ? $pessoaController->montarJson([$visita->getCliente()])[0] : NULL,
                "corretor" => $visita->getCorretor() ? $pessoaController->montarJson([$visita->getCorretor()])[0] : NULL,
            ];
        }
        return $lista;
    }

    public function listarPorCorretor()
    {
        try {
            $visitaDAO = new VisitaDAO();
            $usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;
            if ($usuario === null) {
                return ["status" => "erro", "mensagem" => "Usuário não autenticado"];
            }
            $visitas = $visitaDAO->listarPorCorretor($usuario);
            if (!$visitas) {
                return [
                    "status" => "erro",
                    "mensagem" => "Nenhuma visita encontrada"
                ];
            } else {
                $resposta = self::montarJson($visitas);
                return $resposta;
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao listar visitas: " . $e->getMessage()]);
        }
    }

    public function cadastrar(array $data)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $nome = array_key_exists("nome", $data) ? $data["nome"] : '';
        $dataRecebida = array_key_exists("data", $data) ? $data['data'] : ''; // 2026-09-09
        $hora = array_key_exists("hora", $data) ? $data['hora'] : ''; // 10:52
        $idImovel = array_key_exists("imovel", $data) ? (int) trim($data['imovel']) : '';
        error_log("ID do imóvel recebido: " . $idImovel);
        $idCliente = array_key_exists("cliente", $data) ? (int) trim($data['cliente']) : '';
        $usuario = array_key_exists("usuario", $_SESSION) ? $_SESSION['usuario'] : null;
        $enviarEmail = array_key_exists("enviarEmail", $data) ? (bool) ($data['enviarEmail'])   : false;
        if ($idImovel === '') {
            return ["status" => "erro", "mensagem" => "ID do imóvel não fornecido"];
        }
        if ($idCliente === '') {
            return ["status" => "erro", "mensagem" => "ID do cliente não fornecido"];
        }
        if ($usuario === null) {
            return ["status" => "erro", "mensagem" => "Usuário não autenticado"];
        }
        error_log("ID do imóvel: " . $idImovel);
        $imovelDAO = new ImovelDAO();
        $imovel = $imovelDAO->buscarPorId($idImovel);
        if ($imovel === null) {
            return ["status" => "erro", "mensagem" => "Imóvel não encontrado"];
        }
        $clienteDAO = new PessoaDAO();
        $cliente = $clienteDAO->buscarPorId($idCliente);
        if ($cliente === null) {
            return ["status" => "erro", "mensagem" => "Cliente não encontrado"];
        }
        $dataFormatada = DateTime::createFromFormat('Y-m-d H:i', $dataRecebida . ' ' . $hora);
        $visita = new Visita($cliente, $imovel, $usuario, $dataFormatada, $nome);
        try {
            $visitaDAO = new VisitaDAO();
            $visitaDAO->cadastrar($visita);
            return ["status" => "sucesso", "mensagem" => "Visita cadastrada com sucesso"];
        } catch (Exception $e) {
            return ["status" => "erro", "mensagem" => "Erro ao cadastrar visita: " . $e->getMessage()];
        }
    }

    public function remover($id)
    {
        $visitaDAO = new VisitaDAO();
        try {
            $visitaDAO->remover($id);
            return ["status" => "sucesso", "mensagem" => "Visita removida com sucesso"];
        } catch (Exception $e) {
            return ["status" => "erro", "mensagem" => "Erro ao remover visita: " . $e->getMessage()];
        }
    }
}
