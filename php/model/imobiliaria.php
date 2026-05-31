<?php


require_once __DIR__ . '/estoque.php';
require_once __DIR__ . '/../database/banco.php';

class Imobiliaria
{
    public string $nome;
    public string $cnpj;
    public ?Banco $bancoDados;
    public ?Estoque $estoque;
    public int $quantidadeFuncionarios;
    public int $quantidadeClientes;
    public int $quantidadeFornecedores;
    public float $faturamento;


    public function __construct(string $nome, string $cnpj)
    {
        $this->bancoDados = Banco::getInstance();
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

    public function setNome(string $value)
    {
        $this->nome = $value;
    }

    public function getCnpj()
    {
        return $this->cnpj;
    }

    public function setCnpj(string $value)
    {
        $this->cnpj = $value;
    }

    public function getEstoque()
    {
        return $this->estoque;
    }

    public function setEstoque(?Estoque $value)
    {
        $this->estoque = $value;
    }

    public function atualizar(string $campo_desejado, $valor, string $tabela)
    {
        return $this->bancoDados->atualizar($campo_desejado, $valor, $tabela);
    }

    public function verificarUsuario(string $username, string $senha)
    {
        return $this->bancoDados->verificarUsuario(
            $username,
            $senha
        );
    }

    public function getUsuarioPorId(int $id)
    {
        return $this->bancoDados->getUsuarioPorId($id);
    }

    public function cadastrarEndereco(Endereco $endereco)
    {
        return $this->bancoDados->cadastrarEndereco($endereco);
    }

    public function cadastrarAtendimento(Atendimento $atendimento)
    {
        return $this->bancoDados->cadastrarAtendimento($atendimento);
    }

    public function getListaAtendimentos()
    {
        return $this->bancoDados->getListaAtendimentos();
    }

    public function cadastrarUsuario(Usuario $usuario)
    {
        return $this->bancoDados->cadastrarUsuario($usuario);
    }

    public function cadastrarProprietario(Proprietario $proprietario)
    {
        return $this->bancoDados->cadastrarProprietario($proprietario);
    }

    public function getListaUsuarios()
    {
        return $this->bancoDados->getListaUsuarios();
    }

    public function getUsuarioPorCpfCnpj(string $cpf)
    {
        return $this->bancoDados->getUsuarioPorCpfCnpj($cpf);
    }

    public function getProprietarioPorCpfCnpj(string $cpf)
    {
        return $this->bancoDados->getProprietarioPorCpfCnpj($cpf);
    }

    public function getListaClientes()
    {
        return $this->bancoDados->getListaClientes();
    }

    public function cadastrarListaFiltros(array $lista_filtros, string $tabela)
    {
        return $this->bancoDados->cadastrarListaFiltros($lista_filtros, $tabela);
    }

    public function verificarEndereco(Endereco $endereco)
    {
        return $this->bancoDados->verificarEndereco($endereco);
    }

    public function getCondominioPorIdEndereco(int $id)
    {
        return $this->bancoDados->getCondominioPorIdEndereco($id);
    }

    public function cadastrarCondominio(Condominio $condominio)
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

    public function atualizarAnuncio(Anuncio $anuncio)
    {
        return $this->bancoDados->atualizarAnuncio($anuncio);
    }

    public function atualizarCondominio(Condominio $condominio)
    {
        return $this->bancoDados->atualizarCondominio($condominio);
    }

    public function atualizarUsuario(Usuario $usuario)
    {
        return $this->bancoDados->atualizarUsuario($usuario);
    }

    public function atualizarProprietario(Proprietario $proprietario)
    {
        return $this->bancoDados->atualizarProprietario($proprietario);
    }

    public function remover(string $campo_desejado, $valor, string $tabela)
    {
        return $this->bancoDados->remover($campo_desejado, $valor, $tabela);
    }

    public function getImoveisPorProprietario(string $cpf)
    {
        return $this->bancoDados->getImoveisPorProprietario($cpf);
    }

    public function getImovelPorId(int $id_imovel)
    {
        return $this->bancoDados->getImovelPorId($id_imovel);
    }

    public function getAnuncioPorId(int $id_anuncio)
    {
        return $this->bancoDados->getAnuncioPorId($id_anuncio);
    }

    public function cadastrarVisita(Visita $visita)
    {
        return $this->bancoDados->cadastrarVisita($visita);
    }

    public function getListaVisitasPorCorretor(string $corretor)
    {
        return $this->bancoDados->getListaVisitasPorCorretor($corretor);
    }


    public function
    getListaVistoriasPorVistoriador(string $vistoriador)
    {
        return $this->bancoDados->getListaVistoriasPorVistoriador($vistoriador);
    }

    public function cadastrarVistoria(Vistoria $vistoria)
    {
        return $this->bancoDados->cadastrarVistoria($vistoria);
    }

    public function cadastrarAnexo(Anexo $anexo)
    {
        return $this->bancoDados->cadastrarAnexo($anexo);
    }
}
