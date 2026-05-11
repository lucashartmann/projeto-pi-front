<?php


require_once __DIR__ . '/usuario.php';


class Corretor extends Usuario
{
    public $creci;

    public function __construct($username, $senha, $email, $nome, $cpfCnpj, $creci)
    {
        parent::__construct($username, $senha, $email, $nome, $cpfCnpj, Tipo::CORRETOR);
        $this->creci = $creci;
    }

    public function getCreci()
    {
        return $this->creci;
    }

    public function setCreci($value)
    {
        $this->creci = $value;
    }
}
