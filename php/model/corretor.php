<?php


from model.usuario import Usuario, Tipo


class Corretor(Usuario){
    public function __init__($this, username, senha, email, nome, cpf_cnpj, creci){
        super().__init__(username, senha, email, nome, cpf_cnpj, Tipo.CORRETOR)
        $this.creci = creci

    public function get_creci($this){
        return $this.creci

    public function set_creci($this, value){
        $this.creci = value
}?>