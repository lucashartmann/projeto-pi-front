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
    public int $id;
    public string $username;
    public string $senha;
    public string $email;
    public string $nome;
    public string $cpfCnpj;
    public string $rg;
    public array $telefones;
    public ?Endereco $endereco;
    public ?DateTime $dataNascimento;
    public ?Tipo $tipo;
    public ?DateTime $dataCadastro;
    public ?DateTime $dataModificacao;

    public function __construct(string $username, string $senha, string $email, string $nome, string $cpfCnpj, ?Tipo $tipo)
    {
        $this->id = 0;
        $this->username = $username;
        $this->senha = $senha;
        $this->email = $email;
        $this->nome = $nome;
        $this->cpfCnpj = $cpfCnpj;
        $this->rg = "";
        $this->telefones = [];
        $this->endereco = NULL;
        $this->dataNascimento = NULL;
        $this->tipo = $tipo;
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

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername(string $value)
    {
        $this->username = $value;
    }

    public function getSenha()
    {
        return $this->senha;
    }
    public function setSenha(string $value)
    {
        $this->senha = $value;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function setEmail(string $value)
    {
        $this->email = $value;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome(string $value)
    {
        $this->nome = $value;
    }

    public function getCpfCnpj()
    {
        return $this->cpfCnpj;
    }

    public function setCpfCnpj(string $value)
    {
        $this->cpfCnpj = $value;
    }

    public function getRg()
    {
        return $this->rg;
    }

    public function setRg(string $value)
    {
        $this->rg = $value;
    }

    public function getTelefones()
    {
        return $this->telefones;
    }

    public function setTelefones(array $value)
    {
        $this->telefones = $value;
    }

    public function getEndereco()
    {
        return $this->endereco;
    }

    public function setEndereco(?Endereco $value)
    {
        $this->endereco = $value;
    }

    public function getDataNascimento()
    {
        return $this->dataNascimento;
    }

    public function setDataNascimento(?DateTime $value)
    {
        $this->dataNascimento = $value;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function setTipo(?Tipo $value)
    {
        $this->tipo = $value;
    }
}
