<?php
require_once __DIR__ . '/usuario.php';

class Captador extends Usuario
{
    public float $salario;

    public function __construct(string $username, string $senha, string $email, string $nome, string $cpfCnpj)
    {
        parent::__construct($username, $senha, $email, $nome, $cpfCnpj, Tipo::CAPTADOR);
        $this->salario = 0.0;
    }

    public function getSalario()
    {
        return $this->salario;
    }

    public function setSalario(float $value)
    {
        $this->salario = $value;
    }
}
