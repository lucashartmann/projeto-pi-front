<?php
class Visita
{
    private int $id;
    private ?Cliente $cliente;
    private ?Imovel $imovel;
    private ?Corretor $corretor;

    public function __construct()
    {
        $this->cliente = NULL;
        $this->imovel = NULL;
        $this->corretor = NULL;
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


    public function getCorretor()
    {
        return $this->corretor;
    }


    public function setCorretor(?Corretor $corretor): self
    {
        $this->corretor = $corretor;

        return $this;
    }


    public function getCliente()
    {
        return $this->cliente;
    }


    public function setCliente(?Cliente $cliente): self
    {
        $this->cliente = $cliente;

        return $this;
    }


    public function getId()
    {
        return $this->id;
    }


    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }
}
