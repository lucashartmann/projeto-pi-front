<?php

require_once __DIR__ . '/endereco.php';

class Condominio
{
    private int $id;
    private string $nome;
    private ?Endereco $endereco;
    private array $filtros;

    public function __construct(?string $nome = null, ?Endereco $endereco = null)
    {
        $this->id = 0;
        $this->nome = $nome;
        $this->endereco = $endereco;
        $this->filtros = [];
    }


    public function __init__(string $nome, ?Endereco $endereco)
    {
        $this->__construct($nome, $endereco);
    }

    public function setFiltros(array $filtros)
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

    public function setId(int $id)
    {
        $this->id = $id;
    }
    public function getEndereco()
    {
        return $this->endereco;
    }
    public function setEndereco(?Endereco $endereco)
    {
        $this->endereco = $endereco;
    }
    public function getNome()
    {
        return $this->nome;
    }
    public function setNome(string $nome)
    {
        $this->nome = $nome;
    }
}
