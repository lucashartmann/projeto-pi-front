<?php

require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/../model/anuncio.php';

enum TipoAnexo: string
{
    case IMAGEM = "imagem";
    case VIDEO = "video";
    case DOCUMENTO = "documento";
}

class Anexo
{
    private ?int $idAnuncio;
    private ?Anuncio $anuncio;
    private string $caminho;
    private TipoAnexo $tipoAnexo;

    public function __construct(int $idAnuncio = null, string $caminho, TipoAnexo $tipoAnexo)
    {
        $this->idAnuncio = $idAnuncio;
        $this->caminho = $caminho;
        $this->tipoAnexo = $tipoAnexo;
    }

    public function setAnuncio(?Anuncio $anuncio): void
    {
        $this->anuncio = $anuncio;
    }

    public function getAnuncio(): ?Anuncio
    {
        return $this->anuncio;
    }


    public function setIdAnuncio(int $idAnuncio): void
    {
        $this->idAnuncio = $idAnuncio;
    }

    public function getIdAnuncio(): ?int
    {
        return $this->idAnuncio;
    }

    public function getCaminho(): string
    {
        return $this->caminho;
    }

    public function getTipo(): TipoAnexo
    {
        return $this->tipoAnexo;
    }
}
