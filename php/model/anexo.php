<?php

enum TipoAnexo: string
{
    case IMAGEM = "imagem";
    case VIDEO = "video";
    case DOCUMENTO = "documento";
}

class Anexo
{
    private int $id;
    private string $caminho;
    private TipoAnexo $tipoAnexo;

    public function __construct(int $id, string $caminho, TipoAnexo $tipoAnexo)
    {
        $this->id = $id;
        $this->caminho = $caminho;
        $this->tipoAnexo = $tipoAnexo;
    }

    public function getId(): int
    {
        return $this->id;
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