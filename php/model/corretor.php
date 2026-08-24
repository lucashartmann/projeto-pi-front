<?php


require_once __DIR__ . '/funcionario.php';


class Corretor extends Funcionario
{
    private string $creci;

    public function __construct(?string $email, string $nome, string $cpfCnpj, string $creci)
    {
        parent::__construct($email, $nome, $cpfCnpj, Cargo::CORRETOR);
        $this->creci = $creci;
    }

    public function getCreci()
    {
        return $this->creci;
    }

    public function setCreci(string $value)
    {
        $this->creci = $value;
    }

    public function __toString()
    {
        return "Corretor: { id: " . $this->id . ", nome: " . $this->nome . ", cpfCnpj: " . $this->cpfCnpj . ", email: " . $this->email . ", creci: " . $this->creci . " }";
    }
}
