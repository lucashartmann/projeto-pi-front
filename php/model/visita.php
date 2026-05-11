<?php 
class Visita {
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

    /**
     * Get the value of imovel
     */
    public function getImovel()
    {
        return $this->imovel;
    }

    /**
     * Set the value of imovel
     */
    public function setImovel($imovel): self
    {
        $this->imovel = $imovel;

        return $this;
    }

    /**
     * Get the value of corretor
     */
    public function getCorretor()
    {
        return $this->corretor;
    }

    /**
     * Set the value of corretor
     */
    public function setCorretor($corretor): self
    {
        $this->corretor = $corretor;

        return $this;
    }

    /**
     * Get the value of cliente
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * Set the value of cliente
     */
    public function setCliente($cliente): self
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     */
    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }
}