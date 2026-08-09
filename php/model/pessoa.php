<?php

require_once __DIR__ . '/endereco.php';

class Pessoa
{

    protected  ?int $id;
    protected  ?string $nome;
    protected  ?string $cpfCnpj;
    protected  ?string $rg;
    protected  array $telefones;
    protected  ?Endereco $endereco;
    protected  ?DateTime $dataNascimento;
    protected  ?bool $ativo;
    protected  ?DateTime $dataCadastro;
    protected  ?DateTime $dataModificacao;
    protected  ?string $senha;
    protected  ?string $email;
    protected ?DateTime $ultimoLogin;

    public function __construct(string $email, string $nome, string $cpfCnpj)
    {
        $this->id = NULL;
        $this->email = $email;
        $this->nome = $nome;
        $this->cpfCnpj = $cpfCnpj;
        $this->rg = NULL;
        $this->telefones = [];
        $this->endereco = NULL;
        $this->dataNascimento = NULL;
        $this->ativo = true;
        $this->dataCadastro = NULL;
        $this->dataModificacao = NULL;
        $this->senha = NULL;
        $this->ultimoLogin = NULL;
    }

    public function setEmail(?string $value)
    {
        $this->email = $value;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getUltimoLogin(): ?DateTime
    {
        return $this->ultimoLogin;
    }

    public function setUltimoLogin(?DateTime $value)
    {
        $this->ultimoLogin = $value;
    }

    public function getSenha(): ?string
    {
        return $this->senha;
    }

    public function setSenha(?string $value)
    {
        $this->senha = $value;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function getCpfCnpj(): ?string
    {
        return $this->cpfCnpj;
    }

    public function getRg(): ?string
    {
        return $this->rg;
    }

    public function getTelefones(): array
    {
        return $this->telefones;
    }

    public function getEndereco(): ?Endereco
    {
        return $this->endereco;
    }

    public function getDataNascimento(): ?DateTime
    {
        return $this->dataNascimento;
    }

    public function isAtivo(): bool
    {
        return $this->ativo;
    }

    public function getDataCadastro(): ?DateTime
    {
        return $this->dataCadastro;
    }

    public function getDataModificacao(): ?DateTime
    {
        return $this->dataModificacao;
    }

    public function setId(?int $value)
    {
        $this->id = $value;
    }

    public function setNome(?string $value)
    {
        $this->nome = $value;
    }

    public function setCpfCnpj(?string $value)
    {
        $this->cpfCnpj = $value;
    }

    public function setRg(?string $value)
    {
        $this->rg = $value;
    }

    public function setTelefones(array $value)
    {
        $this->telefones = $value;
    }

    public function setEndereco(?Endereco $value)
    {
        $this->endereco = $value;
    }

    public function setDataNascimento(?DateTime $value)
    {
        $this->dataNascimento = $value;
    }

    public function setAtivo(bool $value)
    {
        $this->ativo = $value;
    }

    public function setDataCadastro(?DateTime $value)
    {
        $this->dataCadastro = $value;
    }

    public function setDataModificacao(?DateTime $value)
    {
        $this->dataModificacao = $value;
    }
}
