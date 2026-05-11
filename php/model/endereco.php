<?php


class Endereco
{
    public $id;
    public $rua;
    public $numero;
    public $bairro;
    public $cep;
    public $complemento;
    public $cidade;
    public $uf;

    public function __construct($rua, $bairro, $cep, $cidade, $uf)
    {
        $this->id = NULL;
        $this->rua = $rua;
        $this->numero = NULL;
        $this->bairro = $bairro;
        $this->cep = $cep;
        $this->complemento = NULL;
        $this->cidade = $cidade;
        $this->uf = $uf;
    }


    public function __init__($rua, $bairro, $cep, $cidade, $uf)
    {
        $this->__construct($rua, $bairro, $cep, $cidade, $uf);
    }

    public function getUf()
    {
        return $this->uf;
    }

    public function setUf($uf)
    {
        $this->uf = $uf;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getRua()
    {
        return $this->rua;
    }

    public function setRua($value)
    {
        $this->rua = $value;
    }

    public function getNumero()
    {
        return $this->numero;
    }

    public function setNumero($value)
    {
        $this->numero = $value;
    }

    public function getBairro()
    {
        return $this->bairro;
    }

    public function setBairro($value)
    {
        $this->bairro = $value;
    }

    public function getCep()
    {
        return $this->cep;
    }

    public function setCep($value)
    {
        $this->cep = $value;
    }

    public function getComplemento()
    {
        return $this->complemento;
    }

    public function setComplemento($value)
    {
        $this->complemento = $value;
    }

    public function getCidade()
    {
        return $this->cidade;
    }

    public function setCidade($value)
    {
        $this->cidade = $value;
    }
}
