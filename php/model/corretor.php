<?php


require_once __DIR__ . '/usuario.php';


class Corretor extends Usuario
{
    public string $creci;

    public function __construct(string $username, string $senha, string $email, string $nome, string $cpfCnpj, string $creci)
    {
        parent::__construct($username, $senha, $email, $nome, $cpfCnpj, Tipo::CORRETOR);
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
}
