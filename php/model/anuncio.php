<?php

class Anuncio
{
    public int $id;
    public string $descricao;
    public string $titulo;
    public array $imagens;
    public array $videos;
    public array $anexos;

    public function __construct()
    {
        $this->id = 0;
        $this->descricao = "";
        $this->titulo = "";
        $this->imagens = [];
        $this->videos = [];
        $this->anexos = [];
    }

    public function setAnexos(array $value)
    {
        $this->anexos = $value;
    }

    public function setVideos(array $value)
    {
        $this->videos = $value;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function setDescricao(string $descricao)
    {
        $this->descricao = $descricao;
    }

    public function setImagens(array $value)
    {
        $this->imagens = $value;
    }

    public function setTitulo(string $titulo)
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
