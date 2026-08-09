<?php

require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/imovelDAO.php';
require_once __DIR__ . '/../model/vistoria.php';

class VistoriaDAO
{
    private Banco $bancoDados;

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
    }

    public function listarPorVistoriador(Funcionario $vistoriador)
    {
        try {

            $lista = [];
            $vistorias = $this->bancoDados->prepare("SELECT * from vistoria WHERE id_vistoriador = $vistoriador");

            if (!$vistorias) {
                return $lista;
            }

            $imovelDAO = new ImovelDAO();

            foreach ($vistorias as $vistoria) {
                $novaVistoria = new Vistoria();
                $novaVistoria->setId($vistoria['id_vistoria']);
                $novaVistoria->setImovel($imovelDAO->buscarPorId($vistoria['id_imovel']));
                $lista[] = $novaVistoria;
            }

            return $lista;
        } catch (Exception $e) {
            error_log("ERRO! vistoriaDAO->listarPorVistoriador: " . $e->getMessage());
            throw new Exception("Erro ao listar vistorias por vistoriador: " . $e->getMessage());
        }
    }
    public function cadastrar(Vistoria $vistoria)
    {
        try {
            return $this->bancoDados->exec("
            INSERT INTO vistoria (id_imovel, data_vistoria, status) 
            VALUES (
                " . ($vistoria->getImovel() ? $vistoria->getImovel()->getId() : "NULL") . ",
                '" . ($vistoria->getDataVistoria() ? $vistoria->getDataVistoria()->format("Y-m-d H:i:s") : "NULL") . "',
                '" . ($vistoria->getStatus() ? $vistoria->getStatus()->value : "NULL") . "'
            )
        ");
        } catch (Exception $e) {
            error_log("ERRO! vistoriaDAO->cadastrar: " . $e->getMessage());
            throw new Exception("Erro ao cadastrar vistoria: " . $e->getMessage());
        }
    }
}
