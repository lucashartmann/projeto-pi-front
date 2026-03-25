<?php
namespace model;
use model\usuario\Usuario;
use model\usuario\Tipo;
require_once 'usuario.php';
$usuario = new Usuario();

class Captador extends Usuario{
    public $salario;
    
    public function __construct(){
        parent::__construct($username, $senha, $email, $nome, $cpf_cnpj, Tipo::CAPTADOR);
        $this->salario = 0.0;
    }

    public function get_salario(){
        return $this->salario;
    }

    public function set_salario($value){
        $this->salario = $value;
    }
}

?>