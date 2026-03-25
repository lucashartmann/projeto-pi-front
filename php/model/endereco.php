<?php


class Endereco{

    public function __init__($this, rua, bairro, cep, cidade, uf){
        $this.id = NULL
        $this.rua = rua
        $this.numero = NULL
        $this.bairro = bairro
        $this.cep = cep
        $this.complemento = NULL
        $this.cidade = cidade
        $this.uf = uf

    public function get_uf($this){
        return $this.uf

    public function set_uf($this, uf){
        $this.uf = uf

    public function get_id($this){
        return $this.id

    public function set_id($this, id){
        $this.id = id

    public function get_rua($this){
        return $this.rua

    public function set_rua($this, value){
        $this.rua = value

    public function get_numero($this){
        return $this.numero

    public function set_numero($this, value){
        $this.numero = value

    public function get_bairro($this){
        return $this.bairro

    public function set_bairro($this, value){
        $this.bairro = value

    public function get_cep($this){
        return $this.cep

    public function set_cep($this, value){
        $this.cep = value

    public function get_complemento($this){
        return $this.complemento

    public function set_complemento($this, value){
        $this.complemento = value

    public function get_cidade($this){
        return $this.cidade

    public function set_cidade($this, value){
        $this.cidade = value
}?>