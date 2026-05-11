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

    public function setAnexos($value)
    {
        $this->anexos = $value;
    }

    public function setVideos($value)
    {
        $this->videos = $value;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    public function setImagens($value)
    {
        $this->imagens = $value;
    }

    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }
    public function getTitulo()
    {
        return $this->titulo;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getImagens()
    {
        return $this->imagens;
    }

    public function getVideos()
    {
        return $this->videos;
    }

    public function getAnexos()
    {
        return $this->anexos;
    }
}
