<?php

class Anuncio
{
    public $id;
    public $descricao;
    public $titulo;
    public $imagens;
    public $videos;
    public $anexos;

    public function __construct()
    {
        $this->id = null;
        $this->descricao = null;
        $this->titulo = null;
        $this->imagens = [];
        $this->videos = [];
        $this->anexos = [];
    }

    public function set_anexos($value)
    {
        $this->anexos = $value;
    }

    public function set_videos($value)
    {
        $this->videos = $value;
    }

    public function set_id($id)
    {
        $this->id = $id;
    }

    public function set_descricao($descricao)
    {
        $this->descricao = $descricao;
    }

    public function set_imagens($value)
    {
        $this->imagens = $value;
    }

    public function set_titulo($titulo)
    {
        $this->titulo = $titulo;
    }
    public function get_titulo()
    {
        return $this->titulo;
    }

    public function get_descricao()
    {
        return $this->descricao;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_imagens()
    {
        return $this->imagens;
    }

    public function get_videos()
    {
        return $this->videos;
    }

    public function get_anexos()
    {
        return $this->anexos;
    }
}
