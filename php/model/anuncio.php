<?php

class Anuncio
{
    private int $id;
    private string $descricao;
    private string $titulo;
    private array $imagens;
    private array $videos;
    private array $anexos;
    private ?int $idImovel;

    public function __construct()
    {
        $this->id = 0;
        $this->idImovel = null;
        $this->descricao = "";
        $this->titulo = "";
        $this->imagens = [];
        $this->videos = [];
        $this->anexos = [];
    }

    public function getIdImovel()
    {
        return $this->idImovel;
    }

    public function setIdImovel(int $idImovel)
    {
        $this->idImovel = $idImovel;
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

    public function __toString()
    {
        return "Anuncio: { id: " . $this->id . ", titulo: " . $this->titulo . ", descricao: " . $this->descricao . ", idImovel: " . $this->idImovel . " }";
    }
}
