<?php

class Proprietario
{
    public $id;
    public $email;
    public $nome;
    public $cpf_cnpj;
    public $rg;
    public $telefones;
    public $endereco;
    public $data_nascimento;
    public $imoveis;
    public $data_cadastro;
    public $data_modificacao;

    public function __construct($email, $nome, $cpf_cnpj)
    {
        $this->id = NULL;
        $this->email = $email;
        $this->nome = $nome;
        $this->cpf_cnpj = $cpf_cnpj;
        $this->rg = NULL;
        $this->telefones = [];
        $this->endereco = NULL;
        $this->data_nascimento = NULL;
        $this->imoveis = [];
        $this->data_cadastro = NULL;
        $this->data_modificacao = NULL;
    }

    public function set_data_cadastro($data)
    {
        $this->data_cadastro = $data;
    }
    public function get_data_cadastro()
    {
        return $this->data_cadastro;
    }
    public function set_data_modificacao($data)
    {
        $this->data_modificacao = $data;
    }
    public function get_data_modificacao()
    {
        return $this->data_modificacao;
    }
    public function set_imoveis($valor)
    {
        $this->imoveis = $valor;
    }
    public function get_imoveis()
    {
        return $this->imoveis;
    }
    public function get_id()
    {
        return $this->id;
    }
    public function set_id($value)
    {
        $this->id = $value;
    }
    public function get_email()
    {
        return $this->email;
    }
    public function set_email($value)
    {
        $this->email = $value;
    }
    public function get_nome()
    {
        return $this->nome;
    }
    public function set_nome($value)
    {
        $this->nome = $value;
    }
    public function get_cpf_cnpj()
    {
        return $this->cpf_cnpj;
    }
    public function set_cpf_cnpj($value)
    {
        $this->cpf_cnpj = $value;
    }
    public function get_rg()
    {
        return $this->rg;
    }
    public function set_rg($value)
    {
        $this->rg = $value;
    }
    public function get_telefones()
    {
        return $this->telefones;
    }
    public function set_telefones($value)
    {
        $this->telefones = $value;
    }
    public function get_endereco()
    {
        return $this->endereco;
    }
    public function set_endereco($value)
    {
        $this->endereco = $value;
    }
    public function get_data_nascimento()
    {
        return $this->data_nascimento;
    }

    public function set_data_nascimento($value)
    {
        $this->data_nascimento = $value;
    }
}
