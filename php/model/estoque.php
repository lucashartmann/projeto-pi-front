<?php

require_once __DIR__ . '/../database/banco.php';

class Estoque
{
    public $bancoDados;

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
    }

    public function cadastrarAnuncio($anuncio)
    {
        return $this->bancoDados->cadastrarAnuncio($anuncio);
    }

    public function cadastrarImovel($imovel)
    {
        return $this->bancoDados->cadastrarImovel($imovel);
    }
    public function atualizarImovel($imovel)
    {
        return $this->bancoDados->atualizarImovel($imovel);
    }
    public function getListaImoveis()
    {
        return $this->bancoDados->getListaImoveis();
    }
    public function getListaImoveisDisponiveis()
    {
        return $this->bancoDados->getListaImoveisDisponiveis();
    }
    // public function get_imoveis_por_categoria($categoria){
    //     return $this->bancoDados->get_imoveis_por_categoria($categoria);
    // }

    //     public function adicionar_anexo($anexo, $tipo, $codigo){
    //         return $this->bancoDados->adicionar_anexo($anexo, $tipo, $codigo);

    //     }        
    // }


    public function getImovelPorId($id)
    {
        return $this->bancoDados->getImovelPorId($id);
    }
}
