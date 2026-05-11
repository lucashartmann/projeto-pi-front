<?php

enum StatusAtendimento: string
{
    case EM_ANDAMENTO = 'Em Andamento';
    case PENDENTE = 'Pendente';
    # RECEM_CADASTRADO = 'Recém Cadastrado';
}

class Atendimento
{
    public $id;
    public $corretor;
    public $cliente;
    public $imovel;
    public $status;

    public function __construct()
    {
        $this->id = NULL;
        $this->corretor = NULL;
        $this->cliente = NULL;
        $this->imovel = NULL;
        $this->status = NULL;
    }

    public function setId($id)
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

    public function setCorretor($value)
    {
        $this->corretor = $value;
    }

    public function getCliente()
    {
        return $this->cliente;
    }

    public function setCliente($value)
    {
        $this->cliente = $value;
    }

    public function getImovel()
    {
        return $this->imovel;
    }

    public function setImovel($value)
    {
        $this->imovel = $value;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($value)
    {
        $this->status = $value;
    }
}
