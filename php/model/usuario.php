<?php

enum Tipo: string
{
    case ADMINISTRADOR = "ADMIN";
    case CORRETOR = "CORRETOR";
    case GERENTE = "GERENTE";
    case CAPTADOR = "CAPTADOR";
    case CLIENTE = "CLIENTE";
    case PROPRIETARIO = "PROPRIETARIO";
    case FINANCEIRO = "FINANCEIRO";
    case VISTORIADOR = "VISTORIADOR";
}


class Usuario
{
    public $id;
    public $username;
    public $senha;
    public $email;
    public $nome;
    public $cpfCnpj;
    public $rg;
    public $telefones;
    public $endereco;
    public $dataNascimento;
    public $tipo;
    public $dataCadastro;
    public $dataModificacao;

    public function __construct($username, $senha, $email, $nome, $cpfCnpj, $tipo)
    {
        $this->id = NULL;
        $this->username = $username;
        $this->senha = $senha;
        $this->email = $email;
        $this->nome = $nome;
        $this->cpfCnpj = $cpfCnpj;
        $this->rg = NULL;
        $this->telefones = [];
        $this->endereco = NULL;
        $this->dataNascimento = NULL;
        $this->tipo = $tipo;
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

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($value)
    {
        $this->username = $value;
    }

    public function getSenha()
    {
        return $this->senha;
    }
    public function setSenha($value)
    {
        $this->senha = $value;
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

    public function getTipo()
    {
        return $this->tipo;
    }

    public function setTipo($value)
    {
        $this->tipo = $value;
    }
}
