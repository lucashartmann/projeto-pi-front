<?php


class Endereco
{
    public int $id;
    public string $rua;
    public string $numero;
    public string $bairro;
    public string $cep;
    public string $complemento;
    public string $cidade;
    public string $uf;

    public function __construct(string $rua, string $bairro, string $cep, string $cidade, string $uf)
    {
        $this->id = 0;
        $this->rua = $rua;
        $this->numero = "";
        $this->bairro = $bairro;
        $this->cep = $cep;
        $this->complemento = "";
        $this->cidade = $cidade;
        $this->uf = $uf;
    }

    public function getUf()
    {
        return $this->uf;
    }

    public function setUf(string $uf)
    {
        $this->uf = $uf;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getRua()
    {
        return $this->rua;
    }

    public function setRua(string $value)
    {
        $this->rua = $value;
    }

    public function getNumero()
    {
        return $this->numero;
    }

    public function setNumero(int $value)
    {
        $this->numero = $value;
    }

    public function getBairro()
    {
        return $this->bairro;
    }

    public function setBairro(string $value)
    {
        $this->bairro = $value;
    }

    public function getCep()
    {
        return $this->cep;
    }

    public function setCep(string $value)
    {
        $this->cep = $value;
    }

    public function getComplemento()
    {
        return $this->complemento;
    }

    public function setComplemento(string $value)
    {
        $this->complemento = $value;
    }

    public function getCidade()
    {
        return $this->cidade;
    }

    public function setCidade(string $value)
    {
        $this->cidade = $value;
    }
}
