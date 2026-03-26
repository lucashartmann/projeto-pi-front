
<?php

require_once __DIR__ . '/usuario.php';


class Cliente extends Usuario
{
    public $tipo_imoveis_desejado;
    public $quant_quartos_desejado;
    public $quant_banheiros_desejado;
    public $endereco_desejado;

    public function __construct($username, $senha, $email, $nome, $cpf_cnpj)
    {
        parent::__construct($username, $senha, $email, $nome, $cpf_cnpj, Tipo::CLIENTE);
        $this->tipo_imoveis_desejado = [];
        $this->quant_quartos_desejado = 0;
        $this->quant_banheiros_desejado = 0;
        $this->endereco_desejado = NULL;
    }

    public function set_tipos_imoveis_desejados($tipo_imoveis)
    {
        $this->tipo_imoveis_desejado = $tipo_imoveis;
    }

    public function set_quant_quartos_desejado($quant_quartos_desejado)
    {
        $this->quant_quartos_desejado = $quant_quartos_desejado;
    }
    public function set_quant_banheiros_desejado($quant_banheiros_desejado)
    {
        $this->quant_banheiros_desejado = $quant_banheiros_desejado;
    }
    public function set_endereco_desejado($endereco)
    {
        $this->endereco_desejado = $endereco;
    }
}
?>