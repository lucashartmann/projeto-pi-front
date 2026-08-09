<?php

enum TipoAnexo: string
{
    case IMAGEM = "imagem";
    case VIDEO = "video";
    case DOCUMENTO = "documento";
}

class Anexo
{
    private ?int $idAnuncio;
    private string $caminho;
    private TipoAnexo $tipoAnexo;

    public function __construct(int $idAnuncio = null, string $caminho, TipoAnexo $tipoAnexo)
    {
        $this->idAnuncio = $idAnuncio;
        $this->caminho = $caminho;
        $this->tipoAnexo = $tipoAnexo;
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
