<?php

enum Status_Atendimento: string
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

    public function set_id($id)
    {
        $this->id = $id;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_corretor()
    {
        return $this->corretor;
    }

    public function set_corretor($value)
    {
        $this->corretor = $value;
    }

    public function get_cliente()
    {
        return $this->cliente;
    }

    public function set_cliente($value)
    {
        $this->cliente = $value;
    }

    public function get_imovel()
    {
        return $this->imovel;
    }

    public function set_imovel($value)
    {
        $this->imovel = $value;
    }

    public function get_status()
    {
        return $this->status;
    }

    public function set_status($value)
    {
        $this->status = $value;
    }
}
