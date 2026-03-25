<?php


from model.usuario import Usuario, Tipo


class Gerente(Usuario){
    public function __init__($this, username, senha, email, nome, cpf_cnpj){
        super().__init__(username, senha, email, nome, cpf_cnpj, Tipo.GERENTE)
        $this.salario = 0.0

    public function get_salario($this){
        return $this.salario

    public function set_salario($this, value){
        $this.salario = value
}?>