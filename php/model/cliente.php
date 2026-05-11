<?php

require_once __DIR__ . '/usuario.php';


class Cliente extends Usuario
{
    public $tipoImoveisDesejado;
    public $quantQuartosDesejado;
    public $quantBanheirosDesejado;
    public $enderecoDesejado;

    public function __construct($username, $senha, $email, $nome, $cpfCnpj)
    {
        parent::__construct($username, $senha, $email, $nome, $cpfCnpj, Tipo::CLIENTE);
        $this->tipoImoveisDesejado = [];
        $this->quantQuartosDesejado = 0;
        $this->quantBanheirosDesejado = 0;
        $this->enderecoDesejado = NULL;
    }

    public function setTiposImoveisDesejados($tipoImoveis)
    {
        $this->tipoImoveisDesejado = $tipoImoveis;
    }

    public function setQuantQuartosDesejado($quantQuartosDesejado)
    {
        $this->quantQuartosDesejado = $quantQuartosDesejado;
    }
    public function setQuantBanheirosDesejado($quantBanheirosDesejado)
    {
        $this->quantBanheirosDesejado = $quantBanheirosDesejado;
    }
    public function setEnderecoDesejado($endereco)
    {
        $this->enderecoDesejado = $endereco;
    }
}
