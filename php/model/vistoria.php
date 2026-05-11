<?php 

class Vistoria {
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

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
    }


    /**
     * Get the value of vistoriador
     */
    public function getVistoriador()
    {
        return $this->vistoriador;
    }

    /**
     * Set the value of vistoriador
     */
    public function setVistoriador($vistoriador): self
    {
        $this->vistoriador = $vistoriador;

        return $this;
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
     * Get the value of data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the value of data
     */
    public function setData($data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get the value of relatorio
     */
    public function getRelatorio()
    {
        return $this->relatorio;
    }

    /**
     * Set the value of relatorio
     */
    public function setRelatorio($relatorio): self
    {
        $this->relatorio = $relatorio;

        return $this;
    }
}