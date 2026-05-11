<?php

class Vistoria
{
    private $id;
    private $vistoriador;
    private $imovel;
    private $data;
    private $relatorio;

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

    public function setId($id)
    {
        $this->id = $id;
    }



    public function getVistoriador()
    {
        return $this->vistoriador;
    }


    public function setVistoriador($vistoriador): self
    {
        $this->vistoriador = $vistoriador;

        return $this;
    }


    public function getImovel()
    {
        return $this->imovel;
    }


    public function setImovel($imovel): self
    {
        $this->imovel = $imovel;

        return $this;
    }


    public function getData()
    {
        return $this->data;
    }


    public function setData($data): self
    {
        $this->data = $data;

        return $this;
    }


    public function getRelatorio()
    {
        return $this->relatorio;
    }


    public function setRelatorio($relatorio): self
    {
        $this->relatorio = $relatorio;

        return $this;
    }
}
