<?php

class Condominio{
    public function __init__($this, nome, endereco){
        $this.id = NULL
        $this.nome = nome
        $this.endereco = endereco
        $this.filtros = []

    public function set_filtros($this, filtros){
        $this.filtros = filtros

    public function get_filtros($this){
        return $this.filtros

    public function get_id($this){
        return $this.id

    public function set_id($this, id){
        $this.id = id

    public function get_endereco($this){
        return $this.endereco

    public function set_endereco($this, endereco){
        $this.endereco = endereco

    public function get_nome($this){
        return $this.nome

    public function set_nome($this, nome){
        $this.nome = nome
}?>