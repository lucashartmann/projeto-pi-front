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

    public function __init__($rua, $bairro, $cep, $cidade, $uf)
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

    public function get_uf()
    {
        return $this->uf;
    }

    public function set_uf($uf)
    {
        $this->uf = $uf;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function set_id($id)
    {
        $this->id = $id;
    }

    public function get_rua()
    {
        return $this->rua;
    }

    public function set_rua($value)
    {
        $this->rua = $value;
    }

    public function get_numero()
    {
        return $this->numero;
    }

    public function set_numero($value)
    {
        $this->numero = $value;
    }

    public function get_bairro()
    {
        return $this->bairro;
    }

    public function set_bairro($value)
    {
        $this->bairro = $value;
    }

    public function get_cep()
    {
        return $this->cep;
    }

    public function set_cep($value)
    {
        $this->cep = $value;
    }

    public function get_complemento()
    {
        return $this->complemento;
    }

    public function set_complemento($value)
    {
        $this->complemento = $value;
    }

    public function get_cidade()
    {
        return $this->cidade;
    }

    public function set_cidade($value)
    {
        $this->cidade = $value;
    }
}
