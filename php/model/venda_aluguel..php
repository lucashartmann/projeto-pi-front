
<?php
class Venda_Aluguel
{
    public $id_venda;
    public $cliente;
    public $captador;
    public $corretor;
    public $imovel;
    public $data_venda;
    public $comissao_captador;
    public $comissao_corretor;
    public $data_cadastro;
    public $data_modificacao;

    public function __init__()
    {
        $this->id_venda = 0;
        $this->cliente = NULL;
        $this->captador = NULL;
        $this->corretor = NULL;
        $this->imovel = NULL;
        $this->data_venda = NULL;
        $this->comissao_captador = NULL;
        $this->comissao_corretor = NULL;
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

    public function get_id_venda()
    {
        return $this->id_venda;
    }

    public function set_id_venda($value)
    {
        $this->id_venda = $value;
    }

    // public function get_cpf_cliente(){
    //     return $this->$cpf_cliente;
    // }

    // public function set_cpf_cliente($value){
    //     $this->cpf_cliente = $value;
    // }

    public function get_cliente()
    {
        return $this->cliente;
    }

    public function set_cliente($value)
    {
        $this->cliente = $value;
    }

    // public function get_proprietario(){
    //     return $this->proprietario;
    // }

    // public function set_proprietario($value){
    //     $this->proprietario = $value;
    // }   

    public function get_captador()
    {
        return $this->captador;
    }

    public function set_captador($value)
    {
        $this->captador = $value;
    }

    public function get_corretor()
    {
        return $this->corretor;
    }

    public function set_corretor($value)
    {
        $this->corretor = $value;
    }

    // public function get_imovel(){
    //     return $this->$imovel;
    // }

    public function set_imovel($value)
    {
        $this->imovel = $value;
    }

    public function get_data_venda()
    {
        return $this->data_venda;
    }

    public function set_data_venda($value)
    {
        $this->data_venda = $value;
    }

    public function get_comissao_captador()
    {
        return $this->comissao_captador;
    }

    public function set_comissao_captador($value)
    {
        $this->comissao_captador = $value;
    }

    public function get_comissao_corretor()
    {
        return $this->comissao_corretor;
    }

    public function set_comissao_corretor($value)
    {
        $this->comissao_corretor = $value;
    }
} ?>