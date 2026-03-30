<?php

enum Tipo: string
{
    case ADMINISTRADOR = "ADMIN";
    case CORRETOR = "CORRETOR";
    case GERENTE = "GERENTE";
    case CAPTADOR = "CAPTADOR";
    case CLIENTE = "CLIENTE";
    case PROPRIETARIO = "PROPRIETARIO";
}


class Usuario
{
    public $id;
    public $username;
    public $senha;
    public $email;
    public $nome;
    public $cpf_cnpj;
    public $rg;
    public $telefones;
    public $endereco;
    public $data_nascimento;
    public $tipo;
    public $data_cadastro;
    public $data_modificacao;

    public function __construct($username, $senha, $email, $nome, $cpf_cnpj, $tipo)
    {
        $this->id = NULL;
        $this->username = $username;
        $this->senha = $senha;
        $this->email = $email;
        $this->nome = $nome;
        $this->cpf_cnpj = $cpf_cnpj;
        $this->rg = NULL;
        $this->telefones = [];
        $this->endereco = NULL;
        $this->data_nascimento = NULL;
        $this->tipo = $tipo;
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

    public function get_id()
    {
        return $this->id;
    }

    public function set_id($value)
    {
        $this->id = $value;
    }

    public function get_username()
    {
        return $this->username;
    }

    public function set_username($value)
    {
        $this->username = $value;
    }

    public function get_senha()
    {
        return $this->senha;
    }
    public function set_senha($value)
    {
        $this->senha = $value;
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

    public function get_tipo()
    {
        return $this->tipo;
    }

    public function set_tipo($value)
    {
        $this->tipo = $value;
    }
}
