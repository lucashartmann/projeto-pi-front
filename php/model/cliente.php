<?php

require_once __DIR__ . '/pessoa.php';
require_once __DIR__ . '/endereco.php';

enum TipoInteresse: string
{
    case VENDA = "Venda";
    case ALUGUEL = "Aluguel";
    case VENDA_ALUGUEL = "Venda e Aluguel";
}

class Cliente extends Pessoa
{
    private array $tipoImoveisDesejado;
    private int $quantQuartosDesejado;
    private int $quantBanheirosDesejado;
    private ?Endereco $enderecoDesejado;
    private array $imoveisFavoritos;
    private ?TipoInteresse $tipoInteresse;
    private ?float $valorMinimo;
    private ?float $valorMaximo;

    public function __construct(string $email, string $nome, string $cpfCnpj)
    {
        parent::__construct($email, $nome, $cpfCnpj);
        $this->tipoImoveisDesejado = [];
        $this->quantQuartosDesejado = 0;
        $this->quantBanheirosDesejado = 0;
        $this->enderecoDesejado = NULL;
        $this->imoveisFavoritos = [];
        $this->tipoInteresse = NULL;
        $this->valorMinimo = NULL;
        $this->valorMaximo = NULL;
    }

    public function getTipoInteresse(): ?TipoInteresse
    {
        return $this->tipoInteresse;
    }

    public function setTipoInteresse(?TipoInteresse $tipoInteresse)
    {
        $this->tipoInteresse = $tipoInteresse;
    }

    public function getValorMinimo(): ?float
    {
        return $this->valorMinimo;
    }

    public function setValorMinimo(?float $valorMinimo)
    {
        $this->valorMinimo = $valorMinimo;
    }

    public function getValorMaximo(): ?float
    {
        return $this->valorMaximo;
    }

    public function setValorMaximo(?float $valorMaximo)
    {
        $this->valorMaximo = $valorMaximo;
    }

    public function getTipoImoveisDesejados(): array
    {
        return $this->tipoImoveisDesejado;
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

    public function __toString()
    {
        return "Cliente: { id: " . $this->id . ", nome: " . $this->nome . ", cpfCnpj: " . $this->cpfCnpj . ", email: " . $this->email . ", tipoInteresse: " . ($this->tipoInteresse ? $this->tipoInteresse->value : 'null') . ", valorMinimo: " . ($this->valorMinimo ?? 'null') . ", valorMaximo: " . ($this->valorMaximo ?? 'null') . " }";
    }
}
