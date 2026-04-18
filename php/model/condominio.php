<?php

class Condominio
{
    public $id;
    public $nome;
    public $endereco;
    public $filtros;

    public function __construct($nome = null, $endereco = null)
    {
        $this->id = NULL;
        $this->nome = $nome;
        $this->endereco = $endereco;
        $this->filtros = [];
    }


    public function __init__($nome, $endereco)
    {
        $this->__construct($nome, $endereco);
    }

    public function set_filtros($filtros)
    {
        $this->filtros = $filtros;
    }

    public function get_filtros()
    {
        return $this->filtros;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function set_id($id)
    {
        $this->id = $id;
    }
    public function get_endereco()
    {
        return $this->endereco;
    }
    public function set_endereco($endereco)
    {
        $this->endereco = $endereco;
    }
    public function get_nome()
    {
        return $this->nome;
    }
    public function set_nome($nome)
    {
        $this->nome = $nome;
    }
}
