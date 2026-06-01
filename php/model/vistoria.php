<?php

require_once __DIR__ . '/imovel.php';
require_once __DIR__ . '/usuario.php';

class Vistoria
{
    private int $id;
    private ?Usuario $vistoriador;
    private ?Imovel $imovel;
    private ?DateTime $data;
    private ?string $relatorio;

    public function __construct()
    {
        $this->vistoriador = NULL;
        $this->imovel = NULL;
        $this->data = NULL;
        $this->relatorio = NULL;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }



    public function getVistoriador()
    {
        return $this->vistoriador;
    }


    public function setVistoriador(?Usuario $vistoriador): self
    {
        $this->vistoriador = $vistoriador;

        return $this;
    }


    public function getImovel()
    {
        return $this->imovel;
    }


    public function setImovel(?Imovel $imovel): self
    {
        $this->imovel = $imovel;

        return $this;
    }


    public function getData()
    {
        return $this->data;
    }


    public function setData(?DateTime $data): self
    {
        $this->data = $data;

        return $this;
    }


    public function getRelatorio()
    {
        return $this->relatorio;
    }


    public function setRelatorio(string $relatorio): self
    {
        $this->relatorio = $relatorio;

        return $this;
    }
}
