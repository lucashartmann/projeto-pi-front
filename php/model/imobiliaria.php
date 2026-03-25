<?php


from database.banco import Banco
from model.estoque import Estoque


class Imobiliaria{
    public function __init__($this, nome, cnpj){
        $this.banco_dados = Banco()
        $this.nome = nome
        $this.cnpj = cnpj
        $this.estoque = Estoque()
        $this.quantidade_funcionarios = 0
        $this.quantidade_clientes = 0
        $this.quantidade_fornecedores = 0
        $this.faturamento = 0

    public function get_nome($this){
        return $this.nome

    public function set_nome($this, value){
        $this.nome = value

    public function get_cnpj($this){
        return $this.cnpj

    public function set_cnpj($this, value){
        $this.cnpj = value

    public function get_estoque($this){
        return $this.estoque

    public function set_estoque($this, value){
        $this.estoque = value

    public function atualizar($this, campo_desejado, valor, tabela){
        return $this.banco_dados.atualizar(campo_desejado, valor, tabela)

    public function verificar_usuario($this,
                          username, senha){
        return $this.banco_dados.verificar_usuario(
            username, senha)

    public function cadastrar_endereco($this, endereco){
        return $this.banco_dados.cadastrar_endereco(endereco)

    public function cadastrar_atendimento($this, atendimento){
        return $this.banco_dados.cadastrar_atendimento(atendimento)

    public function get_lista_atendimentos($this){
        return $this.banco_dados.get_lista_atendimentos()

    public function cadastrar_usuario($this, usuario){
        return $this.banco_dados.cadastrar_usuario(usuario)

    public function cadastrar_proprietario($this, proprietario){
        return $this.banco_dados.cadastrar_proprietario(proprietario)

    public function get_lista_usuarios($this){
        return $this.banco_dados.get_lista_usuarios()

    public function get_usuario_por_cpf_cnpj($this, cpf){
        return $this.banco_dados.get_usuario_por_cpf_cnpj(cpf)

    public function get_proprietario_por_cpf_cnpj($this, cpf){
        return $this.banco_dados.get_proprietario_por_cpf_cnpj(cpf)

    public function get_lista_clientes($this){
        return $this.banco_dados.get_lista_clientes()

    public function cadastrar_lista_filtros($this, lista_filtros, tabela){
        return $this.banco_dados.cadastrar_lista_filtros(lista_filtros, tabela)

    public function verificar_endereco($this, endereco){
        return $this.banco_dados.verificar_endereco(endereco)

    public function get_condominio_por_id_endereco($this, id){
        return $this.banco_dados.get_condominio_por_id_endereco(id)

    public function cadastrar_condominio($this, condominio){
        return $this.banco_dados.cadastrar_condominio(condominio)

    public function get_lista_proprietarios($this){
        return $this.banco_dados.get_lista_proprietarios()

    public function get_lista_enderecos($this){
        return $this.banco_dados.get_lista_enderecos()

    public function get_lista_filtros_apartamento($this){
        return $this.banco_dados.get_lista_filtros_apartamento()

    public function get_lista_filtros_condominio($this){
        return $this.banco_dados.get_lista_filtros_condominio()

    public function atualizar_anuncio($this, anuncio){
        return $this.banco_dados.atualizar_anuncio(anuncio)

    public function atualizar_condominio($this, condominio){
        return $this.banco_dados.atualizar_condominio(condominio)

    public function atualizar_usuario($this, usuario){
        return $this.banco_dados.atualizar_usuario(usuario)

    public function atualizar_proprietario($this, proprietario){
        return $this.banco_dados.atualizar_proprietario(proprietario)

    public function remover($this, campo_desejado, valor, tabela){
        return $this.banco_dados.remover(campo_desejado, valor, tabela)

    public function get_imoveis_por_proprietario($this, cpf){
        return $this.banco_dados.get_imoveis_por_proprietario(cpf)

    public function get_imovel_por_id($this, id_imovel){
        return $this.banco_dados.get_imovel_por_id(id_imovel)
}?>