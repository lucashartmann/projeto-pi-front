<?php

require_once __DIR__ . '/endereco.php';


class Proprietario
{
    public int $id;
    public string $email;
    public string $nome;
    public string $cpfCnpj;
    public string $rg;
    public array $telefones;
    public ?Endereco $endereco;
    public ?DateTime $dataNascimento;
    public array $imoveis;
    public ?DateTime $dataCadastro;
    public ?DateTime $dataModificacao;

    public function __construct(string $email, string $nome, string $cpfCnpj)
    {
        $this->id = 0;
        $this->email = $email;
        $this->nome = $nome;
        $this->cpfCnpj = $cpfCnpj;
        $this->rg = "";
        $this->telefones = [];
        $this->endereco = NULL;
        $this->dataNascimento = NULL;
        $this->imoveis = [];
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
    public function setImoveis(array $valor)
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
    public function setId(int $value)
    {
        $this->id = $value;
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
}
