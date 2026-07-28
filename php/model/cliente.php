<?php

require_once __DIR__ . '/usuario.php';
require_once __DIR__ . '/endereco.php';

class Cliente extends Usuario
{
    public array $tipoImoveisDesejado;
    public int $quantQuartosDesejado;
    public int $quantBanheirosDesejado;
    public ?Endereco $enderecoDesejado;

    public array $imoveisFavoritos;

    public function __construct(string $username, string $senha, string $email, string $nome, string $cpfCnpj)
    {
        parent::__construct($username, $senha, $email, $nome, $cpfCnpj, Tipo::CLIENTE);
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
