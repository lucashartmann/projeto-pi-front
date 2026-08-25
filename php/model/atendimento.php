<?php

require_once __DIR__ . '/corretor.php';
require_once __DIR__ . '/cliente.php';
require_once __DIR__ . '/imovel.php';


enum StatusAtendimento: string
{
    case EM_ANDAMENTO = 'Em Andamento';
    case PENDENTE = 'Pendente';
    # RECEM_CADASTRADO = 'Recém Cadastrado';
}

class Atendimento
{
    private int $id;
    private ?Corretor $corretor;
    private ?Cliente $cliente;
    private ?Imovel $imovel;
    private ?StatusAtendimento $status;
    private ?DateTime $dataCadastro;
    private ?DateTime $dataAtualizacao;

    public function __construct()
    {
        $this->id = 0;
        $this->corretor = NULL;
        $this->cliente = NULL;
        $this->imovel = NULL;
        $this->status = NULL;
        $this->dataCadastro = NULL;
        $this->dataAtualizacao = NULL;
    }

    public function getDataCadastro(): ?DateTime
    {
        return $this->dataCadastro;
    }

    public function setDataCadastro(?DateTime $dataCadastro): void
    {
        $this->dataCadastro = $dataCadastro;
    }

    public function getDataAtualizacao(): ?DateTime
    {
        return $this->dataAtualizacao;
    }

    public function setDataAtualizacao(?DateTime $dataAtualizacao): void
    {
        $this->dataAtualizacao = $dataAtualizacao;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCorretor()
    {
        return $this->corretor;
    }

    public function setCorretor(?Corretor $value)
    {
        $this->corretor = $value;
    }

    public function getCliente()
    {
        return $this->cliente;
    }

    public function setCliente(?Cliente $value)
    {
        $this->cliente = $value;
    }

    public function getImovel()
    {
        return $this->imovel;
    }

    public function setImovel(?Imovel $value)
    {
        $this->imovel = $value;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus(?StatusAtendimento $value)
    {
        $this->status = $value;
    }

    public function __toString()
    {
        return "Atendimento: { id: " . $this->id . ", corretor: " . ($this->corretor ? $this->corretor->getId() : 'null') . ", cliente: " . ($this->cliente ? $this->cliente->getId() : 'null') . ", imovel: " . ($this->imovel ? $this->imovel->getId() : 'null') . ", status: " . ($this->status ? $this->status->value : 'null') . " }";
    }
}
