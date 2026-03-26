<?php


require_once __DIR__ . '/estoque.php';
require_once __DIR__ . '../database/banco.php';

class Imobiliaria
{
    public $nome;
    public $cnpj;


    public function __construct($nome, $cnpj)
    {
        $this->banco_dados = Banco();
        $this->nome = $nome;
        $this->cnpj = $cnpj;
        $this->estoque = Estoque();
        $this->quantidade_funcionarios = 0;
        $this->quantidade_clientes = 0;
        $this->quantidade_fornecedores = 0;
        $this->faturamento = 0;
    }

    public function get_nome()
    {
        return $this . nome;
    }

    public function set_nome($value)
    {
        $this . nome = $value;
    }

    public function get_cnpj()
    {
        return $this . cnpj;
    }

    public function set_cnpj($value)
    {
        $this . cnpj = $value;
    }

    public function get_estoque()
    {
        return $this . estoque;
    }

    public function set_estoque($value)
    {
        $this . estoque = $value;
    }

    public function atualizar($campo_desejado, $valor, $tabela)
    {
        return $this . banco_dados . atualizar($campo_desejado, $valor, $tabela);
    }

    public function verificar_usuario($username, $senha)
    {
        return $this . banco_dados . verificar_usuario(
            $username,
            $senha
        );
    }

    public function cadastrar_endereco($endereco)
    {
        return $this . banco_dados . cadastrar_endereco($endereco);
    }

    public function cadastrar_atendimento($atendimento)
    {
        return $this . banco_dados . cadastrar_atendimento($atendimento);
    }

    public function get_lista_atendimentos()
    {
        return $this . banco_dados . get_lista_atendimentos();
    }

    public function cadastrar_usuario($usuario)
    {
        return $this . banco_dados . cadastrar_usuario($usuario);
    }

    public function cadastrar_proprietario($proprietario)
    {
        return $this . banco_dados . cadastrar_proprietario($proprietario);
    }

    public function get_lista_usuarios()
    {
        return $this . banco_dados . get_lista_usuarios();
    }

    public function get_usuario_por_cpf_cnpj($cpf)
    {
        return $this . banco_dados . get_usuario_por_cpf_cnpj($cpf);
    }

    public function get_proprietario_por_cpf_cnpj($cpf)
    {
        return $this . banco_dados . get_proprietario_por_cpf_cnpj($cpf);
    }

    public function get_lista_clientes()
    {
        return $this . banco_dados . get_lista_clientes();
    }

    public function cadastrar_lista_filtros($lista_filtros, $tabela)
    {
        return $this . banco_dados . cadastrar_lista_filtros($lista_filtros, $tabela);
    }

    public function verificar_endereco($endereco)
    {
        return $this . banco_dados . verificar_endereco($endereco);
    }

    public function get_condominio_por_id_endereco($id)
    {
        return $this . banco_dados . get_condominio_por_id_endereco($id);
    }

    public function cadastrar_condominio($condominio)
    {
        return $this . banco_dados . cadastrar_condominio($condominio);
    }

    public function get_lista_proprietarios()
    {
        return $this . banco_dados . get_lista_proprietarios();
    }

    public function get_lista_enderecos()
    {
        return $this . banco_dados . get_lista_enderecos();
    }

    public function get_lista_filtros_apartamento()
    {
        return $this . banco_dados . get_lista_filtros_apartamento();
    }

    public function get_lista_filtros_condominio()
    {
        return $this . banco_dados . get_lista_filtros_condominio();
    }

    public function atualizar_anuncio($anuncio)
    {
        return $this . banco_dados . atualizar_anuncio($anuncio);
    }

    public function atualizar_condominio($condominio)
    {
        return $this . banco_dados . atualizar_condominio($condominio);
    }

    public function atualizar_usuario($usuario)
    {
        return $this . banco_dados . atualizar_usuario($usuario);
    }

    public function atualizar_proprietario($proprietario)
    {
        return $this . banco_dados . atualizar_proprietario($proprietario);
    }

    public function remover($campo_desejado, $valor, $tabela)
    {
        return $this . banco_dados . remover($campo_desejado, $valor, $tabela);
    }

    public function get_imoveis_por_proprietario($cpf)
    {
        return $this . banco_dados . get_imoveis_por_proprietario($cpf);
    }

    public function get_imovel_por_id($id_imovel)
    {
        return $this . banco_dados . get_imovel_por_id($id_imovel);
    }
}
