<?php


require_once __DIR__ . '/estoque.php';
require_once __DIR__ . '/../database/banco.php';

class Imobiliaria
{
    public $nome;
    public $cnpj;
    public $bancoDados;
    public $estoque;
    public $quantidadeFuncionarios;
    public $quantidadeClientes;
    public $quantidadeFornecedores;
    public $faturamento;


    public function __construct($nome, $cnpj)
    {
        $this->bancoDados = new Banco();
        $this->nome = $nome;
        $this->cnpj = $cnpj;
        $this->estoque = new Estoque();
        $this->quantidadeFuncionarios = 0;
        $this->quantidadeClientes = 0;
        $this->quantidadeFornecedores = 0;
        $this->faturamento = 0;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($value)
    {
        $this->nome = $value;
    }

    public function getCnpj()
    {
        return $this->cnpj;
    }

    public function setCnpj($value)
    {
        $this->cnpj = $value;
    }

    public function getEstoque()
    {
        return $this->estoque;
    }

    public function setEstoque($value)
    {
        $this->estoque = $value;
    }

    public function atualizar($campo_desejado, $valor, $tabela)
    {
        return $this->bancoDados->atualizar($campo_desejado, $valor, $tabela);
    }

    public function verificarUsuario($username, $senha)
    {
        return $this->bancoDados->verificarUsuario(
            $username,
            $senha
        );
    }

    public function getUsuarioPorId($id)
    {
        return $this->bancoDados->getUsuarioPorId($id);
    }

    public function cadastrarEndereco($endereco)
    {
        return $this->bancoDados->cadastrarEndereco($endereco);
    }

    public function cadastrarAtendimento($atendimento)
    {
        return $this->bancoDados->cadastrarAtendimento($atendimento);
    }

    public function getListaAtendimentos()
    {
        return $this->bancoDados->getListaAtendimentos();
    }

    public function cadastrarUsuario($usuario)
    {
        return $this->bancoDados->cadastrarUsuario($usuario);
    }

    public function cadastrarProprietario($proprietario)
    {
        return $this->bancoDados->cadastrarProprietario($proprietario);
    }

    public function getListaUsuarios()
    {
        return $this->bancoDados->getListaUsuarios();
    }

    public function getUsuarioPorCpfCnpj($cpf)
    {
        return $this->bancoDados->getUsuarioPorCpfCnpj($cpf);
    }

    public function getProprietarioPorCpfCnpj($cpf)
    {
        return $this->bancoDados->getProprietarioPorCpfCnpj($cpf);
    }

    public function getListaClientes()
    {
        return $this->bancoDados->getListaClientes();
    }

    public function cadastrarListaFiltros($lista_filtros, $tabela)
    {
        return $this->bancoDados->cadastrarListaFiltros($lista_filtros, $tabela);
    }

    public function verificarEndereco($endereco)
    {
        return $this->bancoDados->verificarEndereco($endereco);
    }

    public function getCondominioPorIdEndereco($id)
    {
        return $this->bancoDados->getCondominioPorIdEndereco($id);
    }

    public function cadastrarCondominio($condominio)
    {
        return $this->bancoDados->cadastrarCondominio($condominio);
    }

    public function getListaProprietarios()
    {
        return $this->bancoDados->getListaProprietarios();
    }

    public function getListaEnderecos()
    {
        return $this->bancoDados->getListaEnderecos();
    }

    public function getListaFiltrosApartamento()
    {
        return $this->bancoDados->getListaFiltrosApartamento();
    }

    public function getListaFiltrosCondominio()
    {
        return $this->bancoDados->getListaFiltrosCondominio();
    }

    public function atualizarAnuncio($anuncio)
    {
        return $this->bancoDados->atualizarAnuncio($anuncio);
    }

    public function atualizarCondominio($condominio)
    {
        return $this->bancoDados->atualizarCondominio($condominio);
    }

    public function atualizarUsuario($usuario)
    {
        return $this->bancoDados->atualizarUsuario($usuario);
    }

    public function atualizarProprietario($proprietario)
    {
        return $this->bancoDados->atualizarProprietario($proprietario);
    }

    public function remover($campo_desejado, $valor, $tabela)
    {
        return $this->bancoDados->remover($campo_desejado, $valor, $tabela);
    }

    public function getImoveisPorProprietario($cpf)
    {
        return $this->bancoDados->getImoveisPorProprietario($cpf);
    }

    public function getImovelPorId($id_imovel)
    {
        return $this->bancoDados->getImovelPorId($id_imovel);
    }

    public function getAnuncioPorId($id_anuncio)
    {
        return $this->bancoDados->getAnuncioPorId($id_anuncio);
    }

    public function cadastrarVisita($visita)
    {
        return $this->bancoDados->cadastrarVisita($visita);
    }

    public function getListaVisitasPorCorretor($corretor)
    {
        return $this->bancoDados->getListaVisitasPorCorretor($corretor);
    }


    public function
    getListaVistoriasPorVistoriador($vistoriador)
    {
        return $this->bancoDados->getListaVistoriasPorVistoriador($vistoriador);
    }

    public function cadastrarVistoria($vistoria)
    {
        return $this->bancoDados->cadastrarVistoria($vistoria);
    }
}
