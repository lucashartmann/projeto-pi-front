<?php


require_once __DIR__ . '/usuario.php';


class Corretor extends Usuario
{
    public $creci;

    public function __construct($username, $senha, $email, $nome, $cpf_cnpj, $creci)
    {
        parent::__construct($username, $senha, $email, $nome, $cpf_cnpj, Tipo::CORRETOR);
        $this->creci = $creci;
    }

    public function get_creci()
    {
        return $this->creci;
    }

    public function set_creci($value)
    {
        $this->creci = $value;
    }
}
