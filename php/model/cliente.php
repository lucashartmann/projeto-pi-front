
<?php

from model.usuario import Usuario, Tipo


class Cliente(Usuario){
    public function __init__($this, username, senha, email, nome, cpf_cnpj){
        super().__init__(username, senha, email, nome, cpf_cnpj, Tipo.CLIENTE)
        $this.tipo_imoveis_desejado = []
        $this.quant_quartos_desejado = 0
        $this.quant_banheiros_desejado = 0
        $this.endereco_desejado = NULL

    public function set_tipos_imoveis_desejados($this, tipo_imoveis){
        $this.tipo_imoveis_desejado = tipo_imoveis

    public function set_quat_quartos_desejado($this, quant_quartos_desejado){
        $this.quant_quartos_desejado = quant_quartos_desejado

    public function set_quant_banheiros_desejado($this, quant_banheiros_desejado){
        $this.quant_banheiros_desejado = quant_banheiros_desejado

    public function set_endereco_desejado($this, endereco){
        $this.endereco_desejado = endereco
}?>