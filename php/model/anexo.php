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
    private ?float $largura;
    private ?float $altura;
    private ?float $posicaoX;
    private ?float $posicaoY;


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

    public function setPosicaoX(?float $posicaoX): void
    {
        $this->posicaoX = $posicaoX;
    }

    public function getPosicaoX(): ?float
    {
        return $this->posicaoX;
    }

    public function setPosicaoY(?float $posicaoY): void
    {
        $this->posicaoY = $posicaoY;
    }

    public function getPosicaoY(): ?float
    {
        return $this->posicaoY;
    }

    public function setLargura(?float $largura): void
    {
        $this->largura = $largura;
    }

    public function getLargura(): ?float
    {
        return $this->largura;
    }

    public function setAltura(?float $altura): void
    {
        $this->altura = $altura;
    }

    public function getAltura(): ?float
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
