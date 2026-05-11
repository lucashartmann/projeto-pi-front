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

    public function setFiltros($filtros)
    {
        $this->filtros = $filtros;
    }

    public function getFiltros()
    {
        return $this->filtros;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    public function getEndereco()
    {
        return $this->endereco;
    }
    public function setEndereco($endereco)
    {
        $this->endereco = $endereco;
    }
    public function getNome()
    {
        return $this->nome;
    }
    public function setNome($nome)
    {
        $this->nome = $nome;
    }
}
