<?php

require_once __DIR__ . '/cliente.php';
require_once __DIR__ . '/proprietario.php';
require_once __DIR__ . '/imovel.php';
require_once __DIR__ . '/pessoa.php';

class Historico
{
    private int $id;
    private string $alteracao;
    private ?DateTime $dataAlteracao;
    private ?Proprietario $proprietario;
    private ?Cliente $cliente;
    private ?Imovel $imovel;
    private ?Pessoa $usuario;

    public function __construct(int $id, string $alteracao, ?DateTime $dataAlteracao = null, ?Proprietario $proprietario = null, ?Cliente $cliente = null, ?Imovel $imovel = null, ?Pessoa $usuario = null)
    {
        $this->id = $id;
        $this->alteracao = $alteracao;
        $this->dataAlteracao = $dataAlteracao;
        $this->proprietario = $proprietario;
        $this->cliente = $cliente;
        $this->imovel = $imovel;
        $this->usuario = $usuario;
    }

    public function getUsuario(): ?Pessoa
    {
        return $this->usuario;
    }

    public function setUsuario(?Pessoa $usuario): void
    {
        $this->usuario = $usuario;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAlteracao(): string
    {
        return $this->alteracao;
    }

    public function getDataAlteracao(): ?DateTime
    {
        return $this->dataAlteracao;
    }

    public function getProprietario(): ?Proprietario
    {
        return $this->proprietario;
    }

    public function getCliente(): ?Cliente
    {
        return $this->cliente;
    }

    public function getImovel(): ?Imovel
    {
        return $this->imovel;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setAlteracao(string $alteracao): void
    {
        $this->alteracao = $alteracao;
    }

    public function setDataAlteracao(?DateTime $dataAlteracao): void
    {
        $this->dataAlteracao = $dataAlteracao;
    }

    public function setProprietario(?Proprietario $proprietario): void
    {
        $this->proprietario = $proprietario;
    }

    public function setCliente(?Cliente $cliente): void
    {
        $this->cliente = $cliente;
    }

    public function setImovel(?Imovel $imovel): void
    {
        $this->imovel = $imovel;
    }
}
