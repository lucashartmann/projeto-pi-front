<?php


require_once __DIR__ . '/usuario.php';

class Gerente extends Usuario{
    public $salario;

    public function __construct($username, $senha, $email, $nome, $cpf_cnpj){
        parent::__construct($username, $senha, $email, $nome, $cpf_cnpj, Tipo::GERENTE);
        $this->salario = 0.0;
    }

    public function get_salario(){
        return $this->salario;
    }

    public function set_salario($value){
        $this->salario = $value;
    }
}