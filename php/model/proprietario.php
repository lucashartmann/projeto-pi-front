<?php

class Proprietario
{
    public $id;
    public $email;
    public $nome;
    public $cpfCnpj;
    public $rg;
    public $telefones;
    public $endereco;
    public $dataNascimento;
    public $imoveis;
    public $dataCadastro;
    public $dataModificacao;

    public function __construct($email, $nome, $cpfCnpj)
    {
        $this->id = NULL;
        $this->email = $email;
        $this->nome = $nome;
        $this->cpfCnpj = $cpfCnpj;
        $this->rg = NULL;
        $this->telefones = [];
        $this->endereco = NULL;
        $this->dataNascimento = NULL;
        $this->imoveis = [];
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
    public function setImoveis($valor)
    {
        $this->imoveis = $valor;
    }
    public function getImoveis()
    {
        return $this->imoveis;
    }
    public function getId()
    {
        return $this->id;
    }
    public function setId($value)
    {
        $this->id = $value;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function setEmail($value)
    {
        $this->email = $value;
    }
    public function getNome()
    {
        return $this->nome;
    }
    public function setNome($value)
    {
        $this->nome = $value;
    }
    public function getCpfCnpj()
    {
        return $this->cpfCnpj;
    }
    public function setCpfCnpj($value)
    {
        $this->cpfCnpj = $value;
    }
    public function getRg()
    {
        return $this->rg;
    }
    public function setRg($value)
    {
        $this->rg = $value;
    }
    public function getTelefones()
    {
        return $this->telefones;
    }
    public function setTelefones($value)
    {
        $this->telefones = $value;
    }
    public function getEndereco()
    {
        return $this->endereco;
    }
    public function setEndereco($value)
    {
        $this->endereco = $value;
    }
    public function getDataNascimento()
    {
        return $this->dataNascimento;
    }

    public function setDataNascimento($value)
    {
        $this->dataNascimento = $value;
    }
}
