<?php


require_once __DIR__ . '/usuario.php';

class Gerente extends Usuario
{
    public $salario;

    public function __construct($username, $senha, $email, $nome, $cpfCnpj)
    {
        parent::__construct($username, $senha, $email, $nome, $cpfCnpj, Tipo::GERENTE);
        $this->salario = 0.0;
    }

    public function getSalario()
    {
        return $this->salario;
    }

    public function setSalario($value)
    {
        $this->salario = $value;
    }
}
