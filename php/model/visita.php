<?php
class Visita
{
    private int $id;
    private ?Cliente $cliente;
    private ?Imovel $imovel;
    private ?Corretor $corretor;
    private ?DateTime $data;
    private ?string $nome;

    public function __construct(?CLiente $cliente, ?Imovel $imovel, ?Corretor $corretor, ?DateTime $data, ?string $nome)
    {
        $this->cliente = $cliente;
        $this->imovel = $imovel;
        $this->corretor = $corretor;
        $this->data = $data;
        $this->nome = $nome;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(?string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getData(): ?DateTime
    {
        return $this->data;
    }

    public function setData(?DateTime $data): self
    {
        $this->data = $data;

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

    public function __toString()
    {
        return "Visita: { id: " . $this->id . ", cliente: " . ($this->cliente ? $this->cliente->getId() : 'null') . ", imovel: " . ($this->imovel ? $this->imovel->getId() : 'null') . ", corretor: " . ($this->corretor ? $this->corretor->getId() : 'null') . " }";
    }
}
