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
    private ?int $largura;
    private ?int $altura;
    private ?int $posicaoX;
    private ?int $posicaoY;


    public function __construct(?int $idAnuncio, string $caminho, TipoAnexo $tipoAnexo)
    {
        $this->idAnuncio = $idAnuncio;
        $this->caminho = $caminho;
        $this->tipoAnexo = $tipoAnexo;
        $this->largura = null;
        $this->altura = null;
        $this->posicaoX = null;
        $this->posicaoY = null;
    }

    public function setPosicaoX(?int $posicaoX): void
    {
        $this->posicaoX = $posicaoX;
    }

    public function getPosicaoX(): ?int
    {
        return $this->posicaoX;
    }

    public function setPosicaoY(?int $posicaoY): void
    {
        $this->posicaoY = $posicaoY;
    }

    public function getPosicaoY(): ?int
    {
        return $this->posicaoY;
    }

    public function setLargura(?int $largura): void
    {
        $this->largura = $largura;
    }

    public function getLargura(): ?int
    {
        return $this->largura;
    }

    public function setAltura(?int $altura): void
    {
        $this->altura = $altura;
    }

    public function getAltura(): ?int
    {
        return $this->altura;
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

    public function __toString()
    {
        return "Anexo: { idAnuncio: " . $this->idAnuncio . ", caminho: " . $this->caminho . ", tipoAnexo: " . $this->tipoAnexo->value . " }";
    }
}
