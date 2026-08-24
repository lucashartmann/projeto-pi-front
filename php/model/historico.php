<?php

require_once __DIR__ . '/cliente.php';
require_once __DIR__ . '/proprietario.php';
require_once __DIR__ . '/imovel.php';
require_once __DIR__ . '/pessoa.php';

class Historico
{
    private ?int $id;
    private ?string $alteracao;
    private ?DateTime $dataAlteracao;
    private ?Pessoa $cliente;
    private ?Imovel $imovel;
    private ?Pessoa $funcionario;

    public function __construct(?string $alteracao, ?DateTime $dataAlteracao = null, ?Proprietario $proprietario = null, ?Pessoa $cliente = null, ?Imovel $imovel = null, ?Pessoa $funcionario = null)
    {
        $this->id = null;
        $this->alteracao = $alteracao;
        $this->dataAlteracao = $dataAlteracao;
        $this->funcionario = $funcionario;
        $this->cliente = $cliente;
        $this->imovel = $imovel;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFuncionario(): ?Pessoa
    {
        return $this->funcionario;
    }

    public function getCliente(): ?Pessoa
    {
        return $this->cliente;
    }

    public function setFuncionario(?Pessoa $funcionario): void
    {
        $this->funcionario = $funcionario;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getAlteracao(): ?string
    {
        return $this->alteracao;
    }

    public function getDataAlteracao(): ?DateTime
    {
        return $this->dataAlteracao;
    }

    public function getImovel(): ?Imovel
    {
        return $this->imovel;
    }

    public function setAlteracao(?string $alteracao): void
    {
        $this->alteracao = $alteracao;
    }

    public function setDataAlteracao(?DateTime $dataAlteracao): void
    {
        $this->dataAlteracao = $dataAlteracao;
    }


    public function setCliente(?Pessoa $cliente): void
    {
        $this->cliente = $cliente;
    }

    public function setImovel(?Imovel $imovel): void
    {
        $this->imovel = $imovel;
    }

    public function __toString()
    {
        return "Historico: { id: " . $this->id . ", alteracao: " . $this->alteracao . ", dataAlteracao: " . ($this->dataAlteracao ? $this->dataAlteracao->format('Y-m-d H:i:s') : 'null') . ", funcionario: " . ($this->funcionario ? $this->funcionario->getId() : 'null') . ", cliente: " . ($this->cliente ? $this->cliente->getId() : 'null') . ", imovel: " . ($this->imovel ? $this->imovel->getId() : 'null') . " }";
    }
}
