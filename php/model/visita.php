<?php
class Visita
{
    private $id;
    private $cliente;
    private $imovel;
    private $corretor;

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


    public function setImovel($imovel): self
    {
        $this->imovel = $imovel;

        return $this;
    }


    public function getCorretor()
    {
        return $this->corretor;
    }


    public function setCorretor($corretor): self
    {
        $this->corretor = $corretor;

        return $this;
    }


    public function getCliente()
    {
        return $this->cliente;
    }


    public function setCliente($cliente): self
    {
        $this->cliente = $cliente;

        return $this;
    }


    public function getId()
    {
        return $this->id;
    }


    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }
}
