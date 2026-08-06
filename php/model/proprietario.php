<?php

require_once __DIR__ . '/pessoa.php';


class Proprietario extends Pessoa
{

    private ?array $imoveis;

    public function __construct(string $email, string $nome, string $cpfCnpj)
    {
        parent::__construct($email, $nome, $cpfCnpj);
        $this->imoveis = [];
    }

    public function getImoveis(): ?array
    {
        return $this->imoveis;
    }

    public function setImoveis(?array $value)
    {
        $this->imoveis = $value;
    }
}
