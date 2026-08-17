<?php

require_once __DIR__ . '/../dao/historicoDAO.php';
require_once __DIR__ . '/../model/historico.php';
require_once __DIR__ . '/imovelController.php';
require_once __DIR__ . '/pessoaController.php';

class HistoricoController
{

    public function montarJson(array $historicos)
    {
        $json = [];
        $pessoaController = new PessoaController();
        $imovelController = new ImovelController();
        foreach ($historicos as $historico) {
            $json[] = [
                "id" => $historico->getId(),
                "imovel" => $historico->getImovel() !== null ? $imovelController->montarJson([$historico->getImovel()]) : null,
                "cliente" => $historico->getCliente() !== null ? $pessoaController->montarJson([$historico->getCliente()]) : null,
                "funcionario" => $historico->getFuncionario() !== null ? $pessoaController->montarJson([$historico->getFuncionario()]) : null,
                "data" => $historico->getDataAlteracao()->format("Y-m-d"),
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
