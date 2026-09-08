<?php

require_once __DIR__ . '/../dao/contratoDAO.php';
require_once __DIR__ . '/../model/contrato.php';
require_once __DIR__ . '/../utils/imagem.php';

$isLocal = $_SERVER['SERVER_NAME'] === 'localhost';
ini_set('display_errors', $isLocal ? '1' : '0');
ini_set('display_startup_errors', $isLocal ? '1' : '0');
error_reporting(E_ALL);

class ContratoController
{

    public function montarJson(array $contratos)
    {
        $json = [];
        foreach ($contratos as $contrato) {
            $json[] = [
                "id" => $contrato->getId(),
                "cliente" => $contrato->getCliente() ? [
                    "id" => $contrato->getCliente()->getId(),
                    "nome" => $contrato->getCliente()->getNome()
                ] : null,
                "proprietario" => $contrato->getProprietario() ? [
                    "id" => $contrato->getProprietario()->getId(),
                    "nome" => $contrato->getProprietario()->getNome()
                ] : null,
                "captador" => $contrato->getCaptador() ? [
                    "id" => $contrato->getCaptador()->getId(),
                    "nome" => $contrato->getCaptador()->getNome()
                ] : null,
                "corretor" => $contrato->getCorretor() ? [
                    "id" => $contrato->getCorretor()->getId(),
                    "nome" => $contrato->getCorretor()->getNome()
                ] : null,
                "data" => $contrato->getData() ? $contrato->getData()->format("Y-m-d H:i:s") : null,
                "imovel" => $contrato->getImovel() ? [
                    "id" => $contrato->getImovel()->getId(),
                    "endereco" => $contrato->getImovel()->getEndereco()
                ] : null,
                "comissao_captador" => $contrato->getComissaoCaptador(),
                "comissao_corretor" => $contrato->getComissaoCorretor(),
                "tempo" => $contrato->getTempo(),
                "data_cadastro" => $contrato->getDataCadastro() ? $contrato->getDataCadastro()->format("Y-m-d H:i:s") : null,
                "data_modificacao" => $contrato->getDataModificacao() ? $contrato->getDataModificacao()->format("Y-m-d H:i:s") : null
            ];
        }
        return $json;
    }

    public function cadastrar(array $dados)
    {
        try {

            $valorAluguel = isset($dados['valor-aluguel']) ? floatval($dados['valor-aluguel']) : 0.0;
            $valorVenda = isset($dados['valor-venda']) ? floatval($dados['valor-venda']) : 0.0;
            $tipoTempo = isset($dados['tipo-tempo']) ? trim($dados['tipo-tempo']) : null;
            $idImovel = isset($dados['imovel']) ? intval($dados['imovel']) : null;
            $idProprietario = isset($dados['proprietario']) ? intval($dados['proprietario']) : null;
            $idCorretor = isset($dados['corretor']) ? intval($dados['corretor']) : null;
            $idCliente = isset($dados['cliente']) ? intval($dados['cliente']) : null;
            $idCaptador = isset($dados['captador']) ? intval($dados['captador']) : null;
            $tempoContrato = isset($dados['tempo']) ? intval($dados['tempo']) : null;
            $dataTermino = isset($dados['termino']) ? new DateTime($dados['termino']) : null;
            $mesmoAluguel = isset($dados['mesmo-aluguel']) ? boolval($dados['mesmo-aluguel']) : false;
            $mesmaVenda = isset($dados['mesma-venda']) ? boolval($dados['mesma-venda']) : false;
            $observacoes = isset($dados['observacoes']) ? trim($dados['observacoes']) : null;
            $documentos = isset($dados['documentos']) ? $dados['documentos'] : [];

            $contrato = new Contrato();
            

            $dao = new ContratoDAO();
            $dao->cadastrar($contrato);

            return [
                "status" => "sucesso",
                "mensagem" => "Contrato cadastrado com sucesso"
            ];
        } catch (Exception $e) {
            error_log("ContratoController::cadastrar - Error: " . $e->getMessage());
            return [
                "status" => "erro",
                "mensagem" => "Erro ao cadastrar contrato: " . $e->getMessage()
            ];
        }
    }

    public function listar()
    {
        try {
            $dao = new ContratoDAO();
            $contratos = $dao->listar();

            return [
                "status" => "sucesso",
                "dados" => $contratos
            ];
        } catch (Exception $e) {
            error_log("ContratoController::listar - Error: " . $e->getMessage());
            return [
                "status" => "erro",
                "mensagem" => "Erro ao listar contratos: " . $e->getMessage()
            ];
        }
    }

    public function remover($id)
    {
        try {
            $dao = new ContratoDAO();
            $dao->remover($id);

            return [
                "status" => "sucesso",
                "mensagem" => "Contrato removido com sucesso"
            ];
        } catch (Exception $e) {
            error_log("ContratoController::remover - Error: " . $e->getMessage());
            return [
                "status" => "erro",
                "mensagem" => "Erro ao remover contrato: " . $e->getMessage()
            ];
        }
    }

    public function buscarPorId($id)
    {
        try {
            $dao = new ContratoDAO();
            $contrato = $dao->buscarPorId($id);
            if (!$contrato) {
                return [
                    "status" => "erro",
                    "mensagem" => "Contrato não encontrado com ID: $id"
                ];
            }
            return $this->montarJson([$contrato])[0];
        } catch (Exception $e) {
            error_log("ContratoController::buscarPorId - Error: " . $e->getMessage());
            return [
                "status" => "erro",
                "mensagem" => "Erro ao buscar contrato: " . $e->getMessage()
            ];
        }
    }
}
