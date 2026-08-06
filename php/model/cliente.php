<?php

require_once __DIR__ . '/pessoa.php';
require_once __DIR__ . '/endereco.php';

class Cliente extends Pessoa 
{
    private array $tipoImoveisDesejado;
    private int $quantQuartosDesejado;
    private int $quantBanheirosDesejado;
    private ?Endereco $enderecoDesejado;
    private array $imoveisFavoritos;

    public function __construct(string $email, string $nome, string $cpfCnpj)
    {
        parent::__construct($email, $nome, $cpfCnpj);
        $this->tipoImoveisDesejado = [];
        $this->quantQuartosDesejado = 0;
        $this->quantBanheirosDesejado = 0;
        $this->enderecoDesejado = NULL;
        $this->imoveisFavoritos = [];
    }

    public function setTiposImoveisDesejados(array $tipoImoveis)
    {
        $this->tipoImoveisDesejado = $tipoImoveis;
    }

    public function setQuantQuartosDesejado(int $quantQuartosDesejado)
    {
        $this->quantQuartosDesejado = $quantQuartosDesejado;
    }
    public function setQuantBanheirosDesejado(int $quantBanheirosDesejado)
    {
        $this->quantBanheirosDesejado = $quantBanheirosDesejado;
    }
    public function setEnderecoDesejado(?Endereco $endereco)
    {
        $this->enderecoDesejado = $endereco;
    }

    public function listarFavoritos()
    {
        return $this->imoveisFavoritos;
    }
}
