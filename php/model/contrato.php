<?php

require_once __DIR__ . '/cliente.php';
require_once __DIR__ . '/corretor.php';
require_once __DIR__ . '/proprietario.php';
require_once __DIR__ . '/imovel.php';

class Contrato
{
    private int $id;
    private ?Cliente $cliente;
    private ?Corretor $captador;
    private ?Corretor $corretor;
    private ?Proprietario $proprietario;
    private ?Imovel $imovel;
    private ?DateTime $dataTermino;
    private float $comissaoCaptador;
    private float $comissaoCorretor;
    private ?DateTime $dataCadastro;
    private ?DateTime $dataModificacao;
    private int $tempo;

    public function __init__()
    {
        $this->id = 0;
        $this->cliente = NULL;
        $this->captador = NULL;
        $this->corretor = NULL;
        $this->imovel = NULL;
        $this->dataTermino = NULL;
        $this->comissaoCaptador = 0.0;
        $this->comissaoCorretor = 0.0;
        $this->dataCadastro = NULL;
        $this->dataModificacao = NULL;
        $this->proprietario = NULL;
        $this->tempo = 0;
    }

    public function getTempo()
    {
        return $this->tempo;
    }

    public function setTempo(int $value)
    {
        $this->tempo = $value;
    }

    public function setProprietario(?Proprietario $value)
    {
        $this->proprietario = $value;
    }

    public function getProprietario()
    {
        return $this->proprietario;
    }

    public function setDataCadastro(?DateTime $data)
    {
        $this->dataCadastro = $data;
    }

    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    public function setDataModificacao(?DateTime $data)
    {
        $this->dataModificacao = $data;
    }

    public function getDataModificacao()
    {
        return $this->dataModificacao;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(int $value)
    {
        $this->id = $value;
    }

    public function getCliente()
    {
        return $this->cliente;
    }

    public function setCliente(?Cliente $value)
    {
        $this->cliente = $value;
    }


    public function getCaptador()
    {
        return $this->captador;
    }

    public function setCaptador(?Corretor $value)
    {
        $this->captador = $value;
    }

    public function getCorretor()
    {
        return $this->corretor;
    }

    public function setCorretor(?Corretor $value)
    {
        $this->corretor = $value;
    }

    public function getImovel()
    {
        return $this->imovel;
    }


    public function setImovel(?Imovel $value)
    {
        $this->imovel = $value;
    }

    public function getDataTermino()
    {
        return $this->dataTermino;
    }

    public function setDataTermino(?DateTime $value)
    {
        $this->dataTermino = $value;
    }

    public function getComissaoCaptador()
    {
        return $this->comissaoCaptador;
    }

    public function setComissaoCaptador(float $value)
    {
        $this->comissaoCaptador = $value;
    }

    public function getComissaoCorretor()
    {
        return $this->comissaoCorretor;
    }

    public function setComissaoCorretor(float $value)
    {
        $this->comissaoCorretor = $value;
    }

    public function __toString()
    {
        return "VendaAluguel: { id: " . $this->id . ", cliente: " . ($this->cliente ? $this->cliente->getId() : 'null') . ", captador: " . ($this->captador ? $this->captador->getId() : 'null') . ", corretor: " . ($this->corretor ? $this->corretor->getId() : 'null') . ", imovel: " . ($this->imovel ? $this->imovel->getId() : 'null') . ", dataTermino: " . ($this->dataTermino ? $this->dataTermino->format('Y-m-d H:i:s') : 'null') . ", comissaoCaptador: " . $this->comissaoCaptador . ", comissaoCorretor: " . $this->comissaoCorretor . ", dataCadastro: " . ($this->dataCadastro ? $this->dataCadastro->format('Y-m-d H:i:s') : 'null') . ", dataModificacao: " . ($this->dataModificacao ? $this->dataModificacao->format('Y-m-d H:i:s') : 'null') . " }";
    }
}
