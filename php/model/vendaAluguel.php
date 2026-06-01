<?php
class VendaAluguel
{
    public int $id;
    public ?Cliente $cliente;
    public ?Corretor $captador;
    public ?Corretor $corretor;
    public ?Imovel $imovel;
    public ?DateTime $data;
    public float $comissaoCaptador;
    public float $comissaoCorretor;
    public ?DateTime $dataCadastro;
    public ?DateTime $dataModificacao;

    public function __init__()
    {
        $this->id = 0;
        $this->cliente = NULL;
        $this->captador = NULL;
        $this->corretor = NULL;
        $this->imovel = NULL;
        $this->data = NULL;
        $this->comissaoCaptador = 0.0;
        $this->comissaoCorretor = 0.0;
        $this->dataCadastro = NULL;
        $this->dataModificacao = NULL;
    }

    public function setDataCadastro(?DateTime $data)
    {
        $this->dataCadastro = $data;
    }

    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    public function setDataModificacao(?DateTime $data)
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

    public function setId(int $value)
    {
        $this->id = $value;
    }

    // public function get_cpf_cliente(){
    //     return $this->$cpf_cliente;
    // }

    // public function set_cpf_cliente(string $value){
    //     $this->cpf_cliente = $value;
    // }

    public function getCliente()
    {
        return $this->cliente;
    }

    public function setCliente(?Cliente $value)
    {
        $this->cliente = $value;
    }

    // public function get_proprietario(){
    //     return $this->proprietario;
    // }

    // public function set_proprietario(?Proprietario $value){
    //     $this->proprietario = $value;
    // }   

    public function getCaptador()
    {
        return $this->captador;
    }

    public function setCaptador(?Corretor $value)
    {
        $this->captador = $value;
    }

    public function getCorretor()
    {
        return $this->corretor;
    }

    public function setCorretor(?Corretor $value)
    {
        $this->corretor = $value;
    }

    // public function get_imovel(){
    //     return $this->$imovel;
    // }

    public function setImovel(?Imovel $value)
    {
        $this->imovel = $value;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData(?DateTime $value)
    {
        $this->data = $value;
    }

    public function getComissaoCaptador()
    {
        return $this->comissaoCaptador;
    }

    public function setComissaoCaptador(float $value)
    {
        $this->comissaoCaptador = $value;
    }

    public function getComissaoCorretor()
    {
        return $this->comissaoCorretor;
    }

    public function setComissaoCorretor(float $value)
    {
        $this->comissaoCorretor = $value;
    }
}
