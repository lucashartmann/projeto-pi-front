<?php
class VendaAluguel
{
    public $id;
    public $cliente;
    public $captador;
    public $corretor;
    public $imovel;
    public $data;
    public $comissaoCaptador;
    public $comissaoCorretor;
    public $dataCadastro;
    public $dataModificacao;

    public function __init__()
    {
        $this->id = 0;
        $this->cliente = NULL;
        $this->captador = NULL;
        $this->corretor = NULL;
        $this->imovel = NULL;
        $this->data = NULL;
        $this->comissaoCaptador = NULL;
        $this->comissaoCorretor = NULL;
        $this->dataCadastro = NULL;
        $this->dataModificacao = NULL;
    }

    public function setDataCadastro($data)
    {
        $this->dataCadastro = $data;
    }

    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    public function setDataModificacao($data)
    {
        $this->dataModificacao = $data;
    }

    public function getDataModificacao()
    {
        return $this->dataModificacao;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    // public function get_cpf_cliente(){
    //     return $this->$cpf_cliente;
    // }

    // public function set_cpf_cliente($value){
    //     $this->cpf_cliente = $value;
    // }

    public function getCliente()
    {
        return $this->cliente;
    }

    public function setCliente($value)
    {
        $this->cliente = $value;
    }

    // public function get_proprietario(){
    //     return $this->proprietario;
    // }

    // public function set_proprietario($value){
    //     $this->proprietario = $value;
    // }   

    public function getCaptador()
    {
        return $this->captador;
    }

    public function setCaptador($value)
    {
        $this->captador = $value;
    }

    public function getCorretor()
    {
        return $this->corretor;
    }

    public function setCorretor($value)
    {
        $this->corretor = $value;
    }

    // public function get_imovel(){
    //     return $this->$imovel;
    // }

    public function setImovel($value)
    {
        $this->imovel = $value;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($value)
    {
        $this->data = $value;
    }

    public function getComissaoCaptador()
    {
        return $this->comissaoCaptador;
    }

    public function setComissaoCaptador($value)
    {
        $this->comissaoCaptador = $value;
    }

    public function getComissaoCorretor()
    {
        return $this->comissaoCorretor;
    }

    public function setComissaoCorretor($value)
    {
        $this->comissaoCorretor = $value;
    }
}
