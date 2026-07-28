<?php

require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/imovelDAO.php';
require_once __DIR__ . '/../model/vistoria.php';

class VistoriaDAO
{
    private Banco $bancoDados;
    private ImovelDAO $imovelDAO;

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
        $this->imovelDAO = new ImovelDAO();
    }
    public function listarPorVistoriador($vistoriador)
    {
        $lista = [];
        $vistorias = $this->bancoDados->prepare("SELECT * from vistoria WHERE id_vistoriador = $vistoriador");

        foreach ($vistorias as $vistoria) {
            $novaVistoria = new Vistoria();
            $novaVistoria->setId($vistoria['id_vistoria']);
            $novaVistoria->setImovel($this->imovelDAO->buscarPorId($vistoria['id_imovel']));
            $lista[] = $novaVistoria;
        }

        return $lista;
    }
    public function cadastrar($vistoria)
    {
        return $this->bancoDados->exec("
            INSERT INTO vistoria (id_imovel, data_vistoria, status) 
            VALUES (
                " . ($vistoria->getImovel() ? $vistoria->getImovel()->getId() : "NULL") . ",
                '" . ($vistoria->getDataVistoria() ? $vistoria->getDataVistoria()->format("Y-m-d H:i:s") : "NULL") . "',
                '" . ($vistoria->getStatus() ? $vistoria->getStatus()->value : "NULL") . "'
            )
        ");
    }
}