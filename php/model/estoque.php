
<?php

from database.banco import Banco


class Estoque{

    public function __init__($this){
        $this.banco_dados = Banco()

    public function cadastrar_anuncio($this, anuncio){
        return $this.banco_dados.cadastrar_anuncio(anuncio)

    public function cadastrar_imovel($this, imovel){
        return $this.banco_dados.cadastrar_imovel(imovel)

    public function atualizar_imovel($this, imovel){
        return $this.banco_dados.atualizar_imovel(imovel)

    public function get_lista_imoveis($this){
        return $this.banco_dados.get_lista_imoveis()

    public function get_lista_imoveis_disponiveis($this){
        return $this.banco_dados.get_lista_imoveis_disponiveis()

    public function get_imoveis_por_categoria($this, categoria){
        return $this.banco_dados.get_imoveis_por_categoria(categoria)

    public function get_imovel_por_id($this, id){
        return $this.banco_dados.get_imovel_por_id(id)

    public function adicionar_anexo($this, anexo, tipo, codigo){
        return $this.banco_dados.adicionar_anexo(anexo, tipo, codigo)
}?>