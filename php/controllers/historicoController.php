<?php

require_once __DIR__ . '/../dao/historicoDAO.php';
require_once __DIR__ . '/../model/historico.php';
require_once __DIR__ . '/imovelController.php';
require_once __DIR__ . '/pessoaController.php';

$isLocal = $_SERVER['SERVER_NAME'] === 'localhost';
ini_set('display_errors', $isLocal ? '1' : '0');
ini_set('display_startup_errors', $isLocal ? '1' : '0');
error_reporting(E_ALL);

class HistoricoController
{

    public function montarJson(array $historicos)
    {
        $json = [];
        $pessoaController = new PessoaController();
        $imovelController = new ImovelController();
        if ($historicos && count($historicos) > 0) {
            usort($historicos, function ($a, $b) {
                return $b->getDataAlteracao() <=> $a->getDataAlteracao();
            });
        } else {
            return [];
        }
        foreach ($historicos as $historico) {
            $json[] = [
                "id" => $historico->getId(),
                "imovel" => $historico->getImovel() ? $imovelController->montarJson([$historico->getImovel()])[0] : null,
                "cliente" => $historico->getCliente()  ? $pessoaController->montarJson([$historico->getCliente()])[0] : null,
                "funcionario" => $historico->getFuncionario() ? $pessoaController->montarJson([$historico->getFuncionario()])[0] : null,
                "data" => $historico->getDataAlteracao() ? $historico->getDataAlteracao()->format("Y-m-d H:i:s") : null,
                "alteracao" => $historico->getAlteracao()
            ];
        }
        return $json;
    }

    public function listarPorIdImovel(int $id)
    {
        try {
            $historicoDAO = new HistoricoDAO();
            $historicos = $historicoDAO->listarPorIdImovel($id);
            if (!$historicos) {
                return (["status" => "erro", "mensagem" => "Nenhum histórico encontrado para o imóvel com ID: $id"]);
            }
            return self::montarJson($historicos);
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao listar históricos do imóvel: " . $e->getMessage()]);
        }
    }

    public function listarPorIdCliente(int $id)
    {
        try {
            $historicoDAO = new HistoricoDAO();
            $historicos = $historicoDAO->listarPorIdCliente($id);
            if (!$historicos) {
                return (["status" => "erro", "mensagem" => "Nenhum histórico encontrado para o cliente com ID: $id"]);
            }
            return self::montarJson($historicos);
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao listar históricos do cliente: " . $e->getMessage()]);
        }
    }

    public function cadastrar(array $dados) {}
}
